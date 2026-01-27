<?php

function get_db(): PDO {
    $db = new PDO('sqlite:' . __DIR__ . '/../storage/database/app.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("PRAGMA journal_mode = WAL;");
    $db->exec("PRAGMA foreign_keys = ON;");
    $db->exec("PRAGMA busy_timeout = 5000;");
    $db->exec("PRAGMA synchronous = NORMAL;");

    return $db;
}
