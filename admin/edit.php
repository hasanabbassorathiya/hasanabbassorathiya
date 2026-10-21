<?php require __DIR__ . '/guard.php';
$entity = $_GET['entity'] ?? '';
if (!in_array($entity, ['profile','projects','experience','skills','testimonials'], true)) { echo 'unknown entity'; exit; }

$cols = [];
$rows = [];

if ($entity !== 'profile') {
    $tables = ['projects' => 'projects', 'experience' => 'experiences', 'skills' => 'skills', 'testimonials' => 'testimonials'];
    $table = $tables[$entity];
    $data = db()->query("SELECT * FROM `$table` ORDER BY sort_order, id")->fetchAll();
    if (count($data) > 0) $cols = array_keys($data[0]);
    $rows = $data;
} else {
    $rows = db()->query('SELECT k, v FROM profile ORDER BY k')->fetchAll();
    $cols = ['k', 'v'];
}
?><!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit <?=ucfirst($entity)?> — Portfolio CMS</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<nav class="topbar"><a href="dashboard.php" class="back">&larr; Dashboard</a><span class="logo"><?=ucfirst($entity)?></span></nav>
<main>
  <table class="data-table"><thead><tr><?php foreach ($cols as $c): ?><th><?=$c?></th><?php endforeach; ?><th>Actions</th></tr></thead>
    <tbody>
      <?php if (count($rows) === 0): ?><tr><td colspan="<?=count($cols)+1?>"><em>No records yet.</em></td></tr><?php endif; ?>
      <?php foreach ($rows as $r): ?>
        <tr><?php foreach ($cols as $c): ?><td class="cell-<?=$c?>"><?=htmlspecialchars((string)($r[$c] ?? ''))?></td><?php endforeach; ?>
          <td>
            <button class="btn-sm" onclick="editRow(<?=$r['id'] ?? 'null'?>)">Edit</button>
            <?php if ($r['id'] ?? false): ?>
              <button class="btn-sm btn-danger" onclick="delRow(<?=$r['id']?>)">Delete</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <button class="btn-primary" onclick="editRow(null)">+ New</button>
</main>
<script>
const ENTITY = '<?=$entity?>';
function token() { return document.cookie.match(/admin_token=([^;]+)/)?.[1]; }
async function api(method, body) { return fetch('../api/crud.php?entity='+ENTITY + (method==='GET'?'':'&id='), { method, headers: {'Content-Type':'application/json','X-Auth-Token':token()}, body: body ? JSON.stringify(body) : null }); }
function editRow(id) { /* backend returns single row; for MVP inline JS modal pattern, reload to edit page with id param */ location.href='edit.php?entity='+ENTITY + (id?'&id='+id:''); }
async function delRow(id) { if (!confirm('Delete?')) return; await api('DELETE',{}); location.reload(); }
</script>
</body>
</html>