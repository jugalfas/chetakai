<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CanvaController extends Controller
{
    // Source - https://stackoverflow.com/q
    // Posted by Aleks Per, modified by community. See post 'Timeline' for change history
    // Retrieved 2026-01-04, License - CC BY-SA 4.0

    public function canva()
    {
        $client_id = 'AAG9YASKmYE'; // Replace with your Canva client ID
        $redirect_uri = 'https://example.com/redirect-canva'; // Your callback URI
        $scope = 'asset:write'; // Adjust scopes as necessary

        // Generate code_verifier and code_challenge
        $code_verifier = $this->generateCodeVerifier();
        $code_challenge = $this->generateCodeChallenge($code_verifier);

        // Store the code_verifier in session
        session(['code_verifier' => $code_verifier]);
        $state = bin2hex(random_bytes(16)); // Generate a random state
        session(['oauth_state' => $state]);

        // Build the authorization URL
        $authorization_url = 'https://www.canva.com/api/oauth/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'scope' => $scope,
            'code_challenge' => $code_challenge,
            'code_challenge_method' => 'S256',
            'state' => $state,
        ]);

        // Redirect to Canva's OAuth page
        return redirect($authorization_url);
    }

    private function generateCodeVerifier($length = 128)
    {
        return rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
    }

    private function generateCodeChallenge($code_verifier)
    {
        return rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
    }



    public function redirect_canva(Request $request)
    {
        // Retrieve the authorization code and state from the request
        $code = $request->input('code');
        $state = $request->input('state');

        // Retrieve the stored code_verifier and state from the session
        $stored_state = session('oauth_state');
        $code_verifier = session('code_verifier');  // Retrieve the same code_verifier

        // Ensure the state matches to prevent CSRF attacks
        if ($state !== $stored_state) {
            return response('Invalid state parameter', 400);
        }

        // Ensure the authorization code is present
        if (!$code) {
            return response('No authorization code found', 400);
        }

        // Canva API OAuth Token endpoint
        $token_url = "https://api.canva.com/rest/v1/oauth/token";

        // Base64-encode client_id:client_secret
        $client_id = 'AAG9YASKmYE'; // Replace with your actual Canva client ID
        $client_secret = 'asdasdasdasdasdasd'; // Replace with your actual Canva client secret
        $credentials = base64_encode("$client_id:$client_secret");

        // Construct the POST fields exactly like in the cURL command you provided
        $post_fields = "grant_type=authorization_code"
            . "&code_verifier=" . urlencode($code_verifier) // Properly URL encode the code_verifier
            . "&code=" . urlencode($code) // Properly URL encode the authorization code
            . "&redirect_uri=" . urlencode('https://example.com/redirect-canva'); // Properly URL encode the redirect_uri

        // Initialize cURL request
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $credentials, // Base64-encoded client_id:client_secret
                'Content-Type: application/x-www-form-urlencoded',
            ),
            CURLOPT_POSTFIELDS => $post_fields, // Use raw POSTFIELDS string
        ));

        // Execute the cURL request
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // Check for cURL errors
        if ($err) {
            return response('cURL Error: ' . htmlspecialchars($err), 500);
        }

        // Parse the response
        $response_data = json_decode($response, true);

        if (isset($response_data['access_token'])) {
            // Store the access token in the session or database
            session(['canva_access_token' => $response_data['access_token']]);

            // Redirect to a success page
            return redirect('/some-success-page'); // Replace with your desired redirection
        } else {
            // Output the full response for debugging
            return response('Error obtaining access token: ' . json_encode($response_data), 400);
        }
    }
}
