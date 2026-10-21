<?php
declare(strict_types=1);
// Admin auth endpoints: POST /api/auth.php (login) + POST /api/auth.php?action=register (first admin)
require __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') json_out(['error' => 'method not allowed'], 405);

$action = $_GET['action'] ?? 'login';
$b = body();

// ---- Register first admin (idempotent) ----
if ($action === 'register') {
    $st = db()->query('SELECT COUNT(*) FROM admins');
    if ((int)$st->fetchColumn() > 0) json_out(['error' => 'admin already exists'], 403);
    if (empty($b['email']) || empty($b['password']) || strlen($b['password']) < 8) {
        json_out(['error' => 'email and min 8-char password required'], 400);
    }
    $hash = password_hash($b['password'] . AUTH_PEPPER, PASSWORD_ARGON2ID);
    $ins = db()->prepare('INSERT INTO admins (email, password_hash) VALUES (?,?)');
    $ins->execute([$b['email'], $hash]);
    json_out(['ok' => true], 201);
}

// ---- Login ----
if (empty($b['email']) || empty($b['password'])) json_out(['error' => 'credentials required'], 400);
$st = db()->prepare('SELECT id, password_hash FROM admins WHERE email = ?');
$st->execute([$b['email']]);
$admin = $st->fetch();

if (!$admin || !password_verify($b['password'] . AUTH_PEPPER, $admin['password_hash'])) {
    json_out(['error' => 'invalid email or password'], 401);
}

$token = bin2hex(random_bytes(32));
$ttl = 7 * 24 * 3600;
$ins = db()->prepare('INSERT INTO auth_tokens (token, admin_id, expires_at) VALUES (?,?, DATE_ADD(NOW(), INTERVAL 7 DAY))');
$ins->execute([$token, (int)$admin['id']]);
json_out(['token' => $token, 'expires_in' => $ttl]);