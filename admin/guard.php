<?php
declare(strict_types=1);
// Guard — require valid admin_token cookie
require __DIR__ . '/../api/bootstrap.php';
$token = $_COOKIE['admin_token'] ?? '';
if ($token === '') { header('Location: index.php'); echo '<script>location.href="index.php"</script>'; exit; }
$st = db()->prepare('SELECT admin_id, expires_at > NOW() AS live FROM auth_tokens WHERE token = ?');
$st->execute([$token]);
$row = $st->fetch();
if (!$row || !$row['live']) {
  header('Set-Cookie: admin_token=; path=/admin; max-age=0');
  header('Location: index.php');
  echo '<script>location.href="index.php"</script>';
  exit;
}
$_admin_id = (int)$row['admin_id'];