<?php
declare(strict_types=1);

/**
 * Для встроенного сервера: php -S localhost:8080 router.php
 * Открывает /admin и /admin/ без «index.php» в адресе.
 */
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($uri === '/admin' || $uri === '/admin/') {
  require __DIR__ . '/admin/index.php';
  return true;
}
if ($uri === '/venki' || $uri === '/venki/') {
  require __DIR__ . '/venki.php';
  return true;
}
if ($uri === '/groby' || $uri === '/groby/') {
  require __DIR__ . '/groby.php';
  return true;
}
if ($uri === '/proizvodstvo' || $uri === '/proizvodstvo/') {
  require __DIR__ . '/proizvodstvo.php';
  return true;
}
if ($uri === '/blog' || $uri === '/blog/') {
  require __DIR__ . '/blog.php';
  return true;
}
if ($uri === '/blog/venki-vgmk' || $uri === '/blog/venki-vgmk/') {
  require __DIR__ . '/blog/venki-vgmk.php';
  return true;
}
if ($uri === '/blog/venki-krasnodar' || $uri === '/blog/venki-krasnodar/') {
  require __DIR__ . '/blog/venki-krasnodar.php';
  return true;
}
if ($uri === '/blog/groby-krasnodar' || $uri === '/blog/groby-krasnodar/') {
  require __DIR__ . '/blog/groby-krasnodar.php';
  return true;
}

return false;
