<?php
declare(strict_types=1);

/**
 * Пароль админки по умолчанию: admin
 * Переопределите через config/admin.local.php: ['password_hash' => '...']
 */
$local = dirname(__DIR__) . '/config/admin.local.php';
if (is_readable($local)) {
  $cfg = require $local;
  if (is_array($cfg) && !empty($cfg['password_hash'])) {
    return $cfg;
  }
}

return [
  'password_hash' => '$2y$12$STUzyrg0AeGltCR/9o2PCeXTrQdUV6KxUxhWQSByNhTBHWjiQxSKm',
];
