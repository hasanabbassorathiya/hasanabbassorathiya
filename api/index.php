<?php
declare(strict_types=1);
// Public content endpoint: GET /api/index.php?entity=profile|projects|experience|skills|testimonials
// No auth.sReads only; the SPA maps these to site sections.
require __DIR__ . '/bootstrap.php';

$allowed = ['profile' => 'profile', 'projects' => 'projects', 'experience' => 'experiences',
            'skills' => 'skills', 'testimonials' => 'testimonials'];

$entity = $_GET['entity'] ?? '';
if (!isset($allowed[$entity])) json_out(['error' => 'unknown entity'], 400);

$table = $allowed[$entity];
$pdo = db();
if ($entity === 'profile') {
    $rows = $pdo->query('SELECT k, v FROM profile')->fetchAll();
    json_out(['entity' => 'profile', 'data' => array_column($rows, 'v', 'k')]);
}
$order = in_array($table, ['projects', 'experience' === $entity ? 'experiences' : $table, 'skills', 'testimonials'], true)
    ? ' ORDER BY sort_order ASC, id ASC' : '';
json_out(['entity' => $entity, 'data' => $pdo->query("SELECT * FROM `$table`" . $order)->fetchAll()]);
