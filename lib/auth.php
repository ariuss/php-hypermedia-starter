<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function auth_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function auth_check(): bool {
    return isset($_SESSION['user']);
}

function auth_login(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user'] = $user;
}

function auth_logout(): void {
    $_SESSION = [];
    session_destroy();
}

function require_auth(): void {
    if (!auth_check()) {
        header("Location: /login", true, 303);
    }
}
