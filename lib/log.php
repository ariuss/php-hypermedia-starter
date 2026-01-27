<?php

enum LogLevel: string {
    case DEBUG   = 'debug';
    case INFO    = 'info';
    case WARNING = 'warning';
    case ERROR   = 'error';
}

function log_event(LogLevel $level, string $message, array $context = []): void {
    $contextStr = $context
        ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        : '';
    
    $line = sprintf(
        "[%s] %s: %s%s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level->value),
        $message,
        $contextStr
    );

    error_log($line, 3, __DIR__ . '/../storage/logs/app.log');
}

// Example: log_event(LogLevel::INFO, 'Application started');
