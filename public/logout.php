<?php

session_start();

// Unset all session variables
$_SESSION = [];

// Delete the session cookie (important!)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect to home page using POST → GET safe redirect
header('Location: /', true, 303);
exit;
