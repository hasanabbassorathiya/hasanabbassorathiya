<?php require __DIR__ . '/guard.php'; ?><!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — Portfolio CMS</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<nav class="topbar"><span class="logo">Portfolio CMS</span><a href="logout.php" class="logout">Sign out</a></nav>
<main class="dashboard">
  <h1>Dashboard</h1>
  <div class="cards" id="stats"></div>
  <nav class="entity-nav">
    <?php foreach (['profile', 'projects', 'experience', 'skills', 'testimonials'] as $e): ?>
      <a href="edit.php?entity=<?=$e?>" class="entity-card" data-entity="<?=$e?>">
        <h2><?=ucfirst($e)?></h2>
        <p class="count" id="cnt-<?=$e?>">—</p>
      </a>
    <?php endforeach; ?>
  </nav>
</main>
<script>
(async () => {
  const tk = document.cookie.match(/admin_token=([^;]+)/)?.[1];
  const hdr = { headers: { 'X-Auth-Token': tk } };
  for (const e of ['profile','projects','experience','skills','testimonials']) {
    const r = await fetch('../api/index.php?entity=' + e, hdr);
    if (r.ok) { const d = await r.json(); document.getElementById('cnt-' + e).textContent = d.data?.length ?? '—'; }
  }
})();
</script>
</body>
</html>