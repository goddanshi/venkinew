<?php
declare(strict_types=1);

require_once __DIR__ . '/paths.php';

function venki_admin_session_start(): void
{
  if (session_status() === PHP_SESSION_NONE) {
    session_name('venkinew_admin');
    session_start();
  }
}

function venki_admin_csrf_token(): string
{
  venki_admin_session_start();
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function venki_admin_verify_csrf(?string $token): bool
{
  venki_admin_session_start();
  return is_string($token)
    && isset($_SESSION['csrf'])
    && hash_equals($_SESSION['csrf'], $token);
}

function venki_admin_logged_in(): bool
{
  venki_admin_session_start();
  return !empty($_SESSION['admin_ok']);
}

function venki_admin_try_login(string $password): bool
{
  $cfg = require VENKI_CONFIG . '/admin.php';
  $hash = isset($cfg['password_hash']) ? (string) $cfg['password_hash'] : '';
  if ($hash === '' || $password === '') {
    return false;
  }
  if (!password_verify($password, $hash)) {
    return false;
  }
  venki_admin_session_start();
  session_regenerate_id(true);
  $_SESSION['admin_ok'] = true;
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return true;
}

function venki_admin_logout(): void
{
  venki_admin_session_start();
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();
}

function venki_admin_require(): void
{
  if (!venki_admin_logged_in()) {
    header('Location: /admin/index.php', true, 302);
    exit;
  }
}
