<?php
declare(strict_types=1);
// Admin CRUD: GET/POST/PUT/DELETE /api/crud.php?entity=projects&id=<int>
require __DIR__ . '/bootstrap.php';
require_admin();

$allowed = ['profile' => 'profile', 'projects' => 'projects', 'experience' => 'experiences',
            'skills' => 'skills', 'testimonials' => 'testimonials'];

$entity = $_GET['entity'] ?? '';
if (!isset($allowed[$entity])) json_out(['error' => 'unknown entity'], 400);
$table = $allowed[$entity];

$method = $_SERVER['REQUEST_METHOD'];
$pdo   = db();

// ---- Read all (or one) ----
if ($method === 'GET') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        if ($entity === 'profile') { json_out(['error' => 'profile is key→value'], 400); }
        $st = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
        $st->execute([$id]);
        json_out($st->fetch() ?: ['error' => 'not found'], $st->fetch() ? 200 : 404);
    }
    json_out(['entity' => $entity, 'data' => $pdo->query("SELECT * FROM `$table` ORDER BY sort_order, id")->fetchAll()]);
}

// ---- Create ----
if ($method === 'POST') {
    $b = body();
    if ($entity === 'profile') {
        $k = $b['k'] ?? ''; $v = $b['v'] ?? '';
        if (!$k || $v === '') json_out(['error' => 'k and v required'], 400);
        $pdo->prepare('REPLACE INTO profile (k, v) VALUES (?,?)')->execute([$k, $v]);
        json_out(['ok' => true], 201);
    }
    $keys = array_keys($b);
    $cols = '`' . implode('`,`', $keys) . '`';
    $ph   = rtrim(str_repeat('?,', count($keys)), ',');
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($ph)")->execute(array_values($b));
    json_out(['id' => (int)$pdo->lastInsertId()], 201);
}

// ---- Update ----
if ($method === 'PUT') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) json_out(['error' => 'id required'], 400);
    $b = body();
    if ($entity === 'profile') {
        $k = $b['k'] ?? ''; $v = $b['v'] ?? '';
        if (!$k) json_out(['error' => 'k required'], 400);
        $pdo->prepare('REPLACE INTO profile (k, v) VALUES (?,?)')->execute([$k, $v]);
        json_out(['ok' => true]);
    }
    $sets = implode(',', array_map(fn($k) => "`$k`=?", array_keys($b)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id = ?")->execute([...array_values($b), $id]);
    json_out(['ok' => true]);
}

// ---- Delete ----
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) json_out(['error' => 'id required'], 400);
    if ($entity === 'profile') { json_out(['error' => 'delete not supported for profile'], 400); }
    $pdo->prepare("DELETE FROM `$table` WHERE id = ?")->execute([$id]);
    json_out(['ok' => true]);
}

json_out(['error' => 'method not allowed'], 405);