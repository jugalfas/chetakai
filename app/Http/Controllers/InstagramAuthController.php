<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class InstagramAuthController extends Controller
{
    public function redirect()
    {
        $query = http_build_query([
            'client_id' => config('services.facebook.client_id'),
            'redirect_uri' => route('auth.instagram.callback'),
            'scope' => 'pages_show_list,pages_read_engagement,pages_manage_posts,instagram_basic,instagram_content_publish,business_management',
            'response_type' => 'code',
        ]);

        return redirect('https://www.facebook.com/v24.0/dialog/oauth?' . $query);
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect()->route('profile.edit')->with('error', 'OAuth cancelled or permission denied.');
        }

        try {
            // 1. Exchange code for User Access Token
            /** @var \Illuminate\Http\Client\Response $tokenResponse */
            $tokenResponse = Http::get('https://graph.facebook.com/v24.0/oauth/access_token', [
                'client_id' => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'redirect_uri' => route('auth.instagram.callback'),
                'code' => $code,
            ]);

            if ($tokenResponse->failed()) {
                Log::error('Instagram OAuth Token Exchange Failed', $tokenResponse->json());
                return redirect()->route('profile.edit')->with('error', 'Failed to exchange code for token.');
            }

            $userAccessToken = $tokenResponse->json()['access_token'];

            // 2. Fetch Facebook Pages
            /** @var \Illuminate\Http\Client\Response $pagesResponse */
            $pagesResponse = Http::get('https://graph.facebook.com/v24.0/me/accounts', [
                'access_token' => $userAccessToken,
            ]);

            $pagesData = $pagesResponse->json();
            Log::info('Facebook Pages Response:', $pagesData);

            $pages = $pagesData['data'] ?? [];

            if (empty($pages)) {
                return redirect()->route('profile.edit')->with('error', 'NO_PAGE_FOUND');
            }

            // 3. Take first Page
            $page = $pages[0];
            $pageId = $page['id'];
            $pageAccessToken = $page['access_token'];

            /** @var \Illuminate\Http\Client\Response $igResponse */
            // 4. Fetch Instagram Business Account
            $igResponse = Http::get("https://graph.facebook.com/v24.0/{$pageId}", [
                'fields' => 'instagram_business_account{id,username,name}',
                'access_token' => $pageAccessToken,
            ]);

            $igData = $igResponse->json();
            Log::info('Instagram Business Account Response:', $igData);

            $igBusinessAccount = $igData['instagram_business_account'] ?? null;

            if (!$igBusinessAccount) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            // Update user state as not connected if it fails at this step
            $user->update([
                'instagram_connected' => false,
            ]);
            return redirect()->route('profile.edit')->with('error', 'NO_INSTAGRAM_BUSINESS_ACCOUNT');
        }

        // 5. Save in users table
        $igUsername = $igBusinessAccount['username'] ?? null;

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'instagram_username' => $igUsername,
            'instagram_account_type' => 'business',
            'instagram_connected' => true,
            'instagram_access_token' => encrypt($pageAccessToken),
            'instagram_business_id' => $igBusinessAccount['id'],
        ]);

            return redirect()->route('profile.edit')->with('success', 'Instagram Business account connected successfully!');

        } catch (\Exception $e) {
            Log::error('Instagram Auth Error: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'An unexpected error occurred during Instagram connection.');
        }
    }
}
