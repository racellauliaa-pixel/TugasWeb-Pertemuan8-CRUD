<?php

session_start();

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/functions.php';

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    die(
        '<div style="font-family:sans-serif;max-width:600px;margin:60px auto;padding:20px;' .
        'background:#fee;border:1px solid #c00;border-radius:8px;">' .
        '<h2 style="color:#c00;margin-top:0;">Koneksi database gagal</h2>' .
        '<p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>' .
        '<p>Pastikan MySQL/MariaDB sudah jalan dan database <code>inventaris_db</code> ' .
        'sudah di-import dari <code>schema.sql</code>.</p>' .
        '</div>'
    );
}