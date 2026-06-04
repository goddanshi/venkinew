<?php
declare(strict_types=1);

/**
 * Учётные данные Telegram: положите рядом файл telegram.local.php (см. telegram.local.example.php)
 * или задайте переменные окружения TG_BOT_TOKEN и TG_CHAT_ID.
 */
$local = __DIR__ . '/telegram.local.php';
if (is_readable($local)) {
  $cfg = require $local;
  if (is_array($cfg)) {
    return $cfg;
  }
}

return [
  'bot_token' => (string) (getenv('TG_BOT_TOKEN') ?: ''),
  'chat_id' => (string) (getenv('TG_CHAT_ID') ?: ''),
];
