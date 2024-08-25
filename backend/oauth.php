<?php
session_start();

// Determine the provider (Google or Facebook) from the query parameter
$provider = $_GET['provider'] ?? '';

if ($provider == 'google') {
    $client_id = '792882179047-o5fpglvavuutamsmni9tr3tvhdin4ci9.apps.googleusercontent.com';
    $redirect_uri = 'http://localhost/icsm-creatives/backend/callback.php?provider=google';
    $auth_url = "https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&scope=email%20profile";
    header('Location: ' . $auth_url);
    exit();
}

if ($provider == 'facebook') {
    $client_id = '1015239673328681';
    $redirect_uri = 'http://localhost/icsm-creatives/backend/callback.php?provider=facebook';
    $auth_url = "https://www.facebook.com/v9.0/dialog/oauth?client_id=$client_id&redirect_uri=$redirect_uri&scope=email";
    header('Location: ' . $auth_url);
    exit();
}

// After redirecting back from Google/Facebook with a code
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    if ($provider == 'google') {
        $token_url = 'https://accounts.google.com/o/oauth2/token';
        $client_id = '792882179047-o5fpglvavuutamsmni9tr3tvhdin4ci9.apps.googleusercontent.com';
        $client_secret = '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';
        $redirect_uri = 'http://localhost/icsm-creatives/backend/callback.php?provider=google';

        $response = file_get_contents($token_url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'code' => $code,
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $redirect_uri,
                    'grant_type' => 'authorization_code',
                ]),
            ],
        ]));

        $token_data = json_decode($response, true);
        $access_token = $token_data['access_token'];

        // Get user info
        $user_info = file_get_contents("https://www.googleapis.com/oauth2/v1/userinfo?access_token=$access_token");
        $user_data = json_decode($user_info, true);
        // Now you can save $user_data['id'] (Google ID) and other data to your database

    } elseif ($provider == 'facebook') {
        $token_url = 'https://graph.facebook.com/v9.0/oauth/access_token';
        $client_id = '1015239673328681';
        $client_secret = 'fff768c23626870c46c9d3b2d2e48cac';
        $redirect_uri = 'http://localhost/icsm-creatives/backend/callback.php?provider=facebook';

        $response = file_get_contents($token_url . '?' . http_build_query([
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'client_secret' => $client_secret,
            'code' => $code,
        ]));

        $token_data = json_decode($response, true);
        $access_token = $token_data['access_token'];

        // Get user info
        $user_info = file_get_contents("https://graph.facebook.com/me?fields=id,name,email&access_token=$access_token");
        $user_data = json_decode($user_info, true);
        // Now you can save $user_data['id'] (Facebook ID) and other data to your database
    }

    // Here you should check if the user exists in your database.
    // If not, create a new user account using the data from $user_data.

    // After successful login or registration, redirect to the user's landing page.
    header('Location: http://localhost/icsm-creatives/client/booking.php');
    exit();
}

?>
