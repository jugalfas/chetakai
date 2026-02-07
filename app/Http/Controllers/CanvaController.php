<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CanvaController extends Controller
{
    public function connect()
    {
        // Generate code_verifier and code_challenge
        $code_verifier = $this->generateCodeVerifier(32);
        $code_challenge = $this->generateCodeChallenge($code_verifier);

        // Store the code_verifier in session
        session(['code_verifier' => $code_verifier]);
        $state = csrf_token(); // Generate a random state
        session(['oauth_state' => $state]);

        $query = http_build_query([
            'client_id'     => config('services.canva.client_id'),
            'redirect_uri'  => config('services.canva.redirect'),
            'response_type' => 'code',
            'scope'         => 'asset:read asset:write design:meta:read design:content:write',
            'code_challenge' => $code_challenge,
            'code_challenge_method' => 's256',
            'state' => $state,
        ]);
        return redirect("https://www.canva.com/api/oauth/authorize?$query");
    }

    private function generateCodeVerifier($length = 128)
    {
        return rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
    }

    private function generateCodeChallenge($code_verifier)
    {
        return rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
    }

    public function callback(Request $request)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::asForm()->post(
            'https://api.canva.com/rest/v1/oauth/token',
            [
                'grant_type'    => 'authorization_code',
                'client_id'     => config('services.canva.client_id'),
                'client_secret' => config('services.canva.client_secret'),
                'redirect_uri'  => config('services.canva.redirect'),
                'code'          => $request->code,
                'code_verifier' => session('code_verifier'), // REQUIRED
            ]
        );

        if (! $response->successful()) {
            dd(
                $response->status(),
                $response->body()
            );
        }

        session([
            'canva_token' => $response->json()['access_token']
        ]);

        return redirect('/canva/editor');
    }

    public function editor()
    {
        $designId = 'DAG_qpPQTXc';
        $encodedId = urlencode($designId);

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken(session('canva_token'))
            ->get("https://api.canva.com/rest/v1/designs/{$encodedId}");

        if (!$response->successful()) {
            // If the design doesn't exist or token is invalid, show error
            dd($response->status(), $response->json());
        }

        $designData = $response->json();
        $editUrl = $designData['design']['urls']['edit_url'];

        // Step 2: Prepare correlation_state (must be 50 chars or less, URL safe)
        $correlationState = bin2hex(random_bytes(16));
        session(['canva_correlation_state' => $correlationState]);

        // Append correlation_state to the edit_url
        $finalUrl = $editUrl . (strpos($editUrl, '?') !== false ? '&' : '?') . 'correlation_state=' . $correlationState;

        return redirect($finalUrl);
    }

    public function design(Request $request)
    {
        $correlationJwt = $request->query('correlation_jwt');

        if (!$correlationJwt) {
            return response('Missing correlation_jwt', 400);
        }

        // Canva returns a JWS (JSON Web Signature)
        // For simple state verification, we can decode the payload (second part of the JWT)
        $parts = explode('.', $correlationJwt);
        if (count($parts) !== 3) {
            return response('Invalid JWT format', 400);
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

        if (!$payload) {
            return response('Failed to decode JWT payload', 400);
        }

        $designId = $payload['design_id'] ?? null;
        $returnedState = $payload['correlation_state'] ?? null;
        $storedState = session('canva_correlation_state');

        // Verify correlation_state
        if ($returnedState !== $storedState) {
            return response('Invalid correlation state', 403);
        }

        return view('canva.success', [
            'design_id' => $designId,
            'message' => 'Successfully returned from Canva!'
        ]);
    }
}
