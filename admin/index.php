<?php
declare(strict_types=1);
require __DIR__ . '/../api/bootstrap.php';

$token = $_COOKIE['admin_token'] ?? '';
$me = admin_id();  // uses X-AUTH-TOKEN or cookie; bootstrap.php's admin_id() reads X-AUTH-TOKEN header
// Redirect if already authed
if ($me) { header('Location: dashboard.php'); echo '<script>location.href="dashboard.php"</script>'; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';
    if ($email && $pass) {
        $st = db()->prepare('SELECT id, password_hash FROM admins WHERE email = ?');
        $st->execute([$email]);
        $a = $st->fetch();
        if ($a && password_verify($pass . AUTH_PEPPER, $a['password_hash'])) {
            $tk = bin2hex(random_bytes(32));
            $ins = db()->prepare('INSERT INTO auth_tokens (token, admin_id, expires_at) VALUES (?,?, DATE_ADD(NOW(), INTERVAL 7 DAY))');
            $ins->execute([$tk, (int)$a['id']]);
            header('Set-Cookie: admin_token=' . $tk . '; path=/admin; httponly; samesite=lax; max-age=' . (7*86400) . '; ' . ($_SERVER['HTTPS']??'' === 'on' ? 'secure;' : ''));
            header('Location: dashboard.php');
            echo '<script>location.href="dashboard.php"</script>';
            exit;
        }
    }
    echo '<p class="error">Invalid email or password.</p>';
}
?><!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Portfolio CMS</title>
<link rel="stylesheet" href="admin.css">
</head>
<body class="login-page">
<div class="login-card">
  <h1>Portfolio CMS</h1>
  <form method="post" autocomplete="off">
    <label>Email <input type="email" name="email" required autocomplete="username"></label>
    <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
    <button type="submit" class="btn-primary">Sign in</button>
  </form>
  <p class="hint">First time? Run <code>curl -X POST .../auth.php?action=register</code></p>
</div>
</body>
</html>