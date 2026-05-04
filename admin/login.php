<?php
session_start();
require_once dirname(__DIR__) . '/config.php';

if (isset($_SESSION['admin'])) {
    header('Location: /admin/'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (trim($_POST['password'] ?? '') === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        header('Location: /admin/'); exit;
    }
    $error = 'Contraseña incorrecta.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — MY ARCADE ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<style>
body { display:flex; align-items:center; justify-content:center; min-height:100vh; }
.login-box {
  width: 380px; padding: 32px;
  background: var(--negro-panel);
  border: 1px solid rgba(0,238,255,0.2);
  border-top: 3px solid var(--cyan);
}
.login-logo {
  font-family:'Bebas Neue',sans-serif;
  font-size:32px; letter-spacing:6px;
  background:linear-gradient(180deg,#fff,var(--cyan));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  background-clip:text; text-align:center; margin-bottom:4px;
}
.login-sub {
  font-family:'Share Tech Mono',monospace;
  font-size:9px; letter-spacing:4px;
  color:var(--magenta); text-align:center; margin-bottom:24px;
}
.login-box input[type=password] {
  width:100%; background:rgba(0,0,20,0.8);
  border:1px solid rgba(0,238,255,0.2);
  color:var(--blanco);
  font-family:'Share Tech Mono',monospace;
  font-size:14px; padding:10px 14px;
  outline:none; letter-spacing:3px; margin-bottom:12px;
}
.login-box input:focus { border-color:var(--cyan); }
.login-box button {
  width:100%; font-family:'Bebas Neue',sans-serif;
  font-size:14px; letter-spacing:4px;
  background:var(--cyan); color:#000; border:none;
  padding:12px; cursor:pointer; transition:background .2s;
}
.login-box button:hover { background:var(--amarillo); }
.login-error {
  font-family:'Share Tech Mono',monospace;
  font-size:10px; color:var(--rojo);
  margin-bottom:12px; letter-spacing:1px;
}
.login-error::before { content:'✕ '; }
</style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">MY ARCADE ZONE</div>
  <div class="login-sub">◄ PANEL DE ADMINISTRACIÓN ►</div>
  <?php if ($error): ?>
  <div class="login-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <input type="password" name="password" placeholder="CONTRASEÑA" autofocus>
    <button type="submit">► ENTRAR</button>
  </form>
</div>
</body>
</html>
