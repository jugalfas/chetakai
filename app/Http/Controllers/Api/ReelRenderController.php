<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Quote;
use App\Models\Reel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ReelRenderController extends Controller
{
    public function pending()
    {
        $post = Post::where('content_type', 'reel')
            ->where('render_status', 'pending')
            ->orderBy('created_at')
            ->first();

        if (!$post) {
            return response()->json(['data' => null]);
        }

        $quote = Quote::find($post->quote_id);

        return response()->json(['data' => [
            'id' => $post->id,
            'quote' => $quote->quote,
            'caption' => $post->caption,
            'hook' => $post->hook,
        ]]);
    }

    public function complete(Request $request)
    {
        $post = Post::findOrFail($request->post_id);

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $filename = 'reel_' . time() . '.mp4';
            $path = $file->storeAs('reels', $filename, 'public');

            $post->video_path = asset('storage/' . $path);
            $post->render_status = 'completed';
            $post->status = 'scheduled';
            $post->scheduled_at = now()->addMinutes(10); // auto schedule
            $post->save();
        }

        return response()->json(['success' => true]);
    }

    public function getAudioDuration(Request $request)
    {
        if (!$request->hasFile('audio')) {
            return response()->json(['error' => 'No audio uploaded'], 400);
        }

        // 1️⃣ Store uploaded file inside Laravel storage
        $file = $request->file('audio');

        $storedPath = $file->store('temp');
        // example: storage/app/temp/abcd123.mp3

        // 2️⃣ Now open using disk + relative path
        $duration = FFMpeg::fromDisk('local')
            ->open($storedPath)
            ->getDurationInSeconds();

        // 3️⃣ Cleanup (very important later for automation)
        Storage::disk('local')->delete($storedPath);

        return response()->json([
            'duration' => $duration
        ]);
    }

    public function create(Request $request)
    {
        set_time_limit(0);

        /* ---------------- VALIDATION ---------------- */

        if (!$request->hasFile('audio')) {
            return response()->json(['error' => 'Audio missing'], 422);
        }

        $videoUrl  = $request->input('video_url');
        $script    = $request->input('script');
        $duration  = (int)$request->input('duration');

        if (!$videoUrl || !$script || !$duration) {
            return response()->json(['error' => 'video_url / script / duration missing'], 422);
        }

        /* ---------------- PREPARE FOLDER ---------------- */

        $id = time(); // unique render job

        $base = storage_path("app/private/temp/$id");

        if (!file_exists($base)) {
            mkdir($base, 0777, true);
        }

        /* ---------------- SAVE AUDIO (CRITICAL FIX) ---------------- */

        $audioFile = $request->file('audio');
        $audioPath = $base . '/audio.mp3';
        $audioFile->move($base, 'audio.mp3');

        /* ---------------- DOWNLOAD VIDEO ---------------- */

        $videoPath = $base . '/video.mp4';

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
            'Accept' => '*/*',
            'Referer' => 'https://www.pexels.com/',
        ])
            ->timeout(0)
            ->withOptions([
                'stream' => true,
                'verify' => false
            ])
            ->get($videoUrl);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Pexels blocked download',
                'status' => $response->status()
            ], 500);
        }

        file_put_contents($videoPath, $response->body());

        if (!file_exists($videoPath) || filesize($videoPath) < 10000) {
            return response()->json(['error' => 'Video download failed'], 500);
        }

        /* ---------------- CUT VIDEO TO AUDIO ---------------- */

        $cutVideo = $base . '/cut.mp4';

        FFMpeg::fromDisk('local')
            ->open("temp/$id/video.mp4")
            ->export()
            ->toDisk('local')
            ->inFormat(new \FFMpeg\Format\Video\X264())
            ->addFilter(['-t', $duration])
            ->save("temp/$id/cut.mp4");

        if (!file_exists($cutVideo)) {
            return response()->json(['error' => 'Video cutting failed'], 500);
        }

        /* ---------------- MERGE AUDIO ---------------- */

        $merged = $base . '/merged.mp4';

        FFMpeg::fromDisk('local')
            ->open("temp/$id/cut.mp4")
            ->addFilter(['-i', $audioPath])
            ->export()
            ->toDisk('local')
            ->inFormat(new \FFMpeg\Format\Video\X264())
            ->addFilter(['-map', '0:v', '-map', '1:a', '-shortest'])
            ->save("temp/$id/merged.mp4");

        if (!file_exists($merged)) {
            return response()->json(['error' => 'Audio merge failed'], 500);
        }

        /* ---------------- GENERATE SUBTITLES ---------------- */

        $srt = $base . '/subs.srt';
        $this->generateSRT($script, $duration, $srt);

        /* ---------------- FINAL EXPORT (INSTAGRAM READY) ---------------- */

        $final = $base . '/final.mp4';

        // Fix SRT path for FFmpeg filter (backslashes need escaping or conversion to forward slashes on Windows)
        $srtFilterPath = str_replace('\\', '/', $srt);
        if (strpos($srtFilterPath, ':') !== false) {
            $srtFilterPath = str_replace(':', '\\:', $srtFilterPath);
        }

        $format = new \FFMpeg\Format\Video\X264('aac');
        $format->setAdditionalParameters([
            '-vf', "scale=1080:1920,subtitles='$srtFilterPath'",
            '-preset', 'fast',
            '-crf', '23',
            '-movflags', '+faststart'
        ]);
        $format->setAudioKiloBitrate(192);

        FFMpeg::fromDisk('local')
            ->open("temp/$id/merged.mp4")
            ->export()
            ->toDisk('local')
            ->inFormat($format)
            ->save("temp/$id/final.mp4");

        if (!file_exists($final)) {
            return response()->json(['error' => 'Final render failed'], 500);
        }

        /* ---------------- MOVE TO PUBLIC ---------------- */

        $publicPath = storage_path("app/public/reels");

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0777, true);
        }

        $reelName = Str::uuid() . '.mp4';
        $destination = $publicPath . '/' . $reelName;

        rename($final, $destination);

        return response()->json([
            'status' => 'success',
            'reel_url' => asset("storage/reels/$reelName")
        ]);
    }

    /* ================= SRT GENERATOR ================= */

    private function generateSRT($text, $duration, $path)
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', trim($text));

        $count = count($sentences);
        $segment = $duration / max($count, 1);

        $time = 0;
        $index = 1;
        $srt = '';

        foreach ($sentences as $line) {

            $start = $this->formatTime($time);
            $time += $segment;
            $end = $this->formatTime($time);

            $srt .= "$index\n";
            $srt .= "$start --> $end\n";
            $srt .= trim($line) . "\n\n";

            $index++;
        }

        file_put_contents($path, $srt);
    }

    private function formatTime($seconds)
    {
        $ms = sprintf('%03d', ($seconds - floor($seconds)) * 1000);
        $seconds = floor($seconds);

        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;

        return sprintf('%02d:%02d:%02d,%s', $h, $m, $s, $ms);
    }
}
