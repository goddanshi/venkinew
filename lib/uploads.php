<?php
declare(strict_types=1);

require_once __DIR__ . '/paths.php';

const VENKI_UPLOAD_MAX_BYTES = 5 * 1024 * 1024;

function venki_uploads_products_dir(): string
{
  return VENKI_ROOT . '/uploads/products';
}

function venki_uploads_real_dir(): ?string
{
  $d = realpath(venki_uploads_products_dir());
  return $d !== false ? $d : null;
}

function venki_is_local_product_image(?string $stored): bool
{
  if ($stored === null || $stored === '') {
    return false;
  }
  return (bool) preg_match('#^/?uploads/products/[^/]+$#', $stored);
}

function venki_delete_product_image_file(?string $stored): void
{
  if (!venki_is_local_product_image($stored)) {
    return;
  }
  $rel = ltrim($stored, '/');
  $full = VENKI_ROOT . '/' . $rel;
  $realFile = realpath($full);
  $base = venki_uploads_real_dir();
  if ($realFile === false || $base === null || strpos($realFile, $base) !== 0 || !is_file($realFile)) {
    return;
  }
  @unlink($realFile);
}

/**
 * @param array<string,mixed> $file элемент $_FILES['field']
 */
function venki_save_uploaded_product_image(array $file, string $itemId): ?string
{
  if (!isset($file['error']) || (int) $file['error'] !== UPLOAD_ERR_OK) {
    return null;
  }
  if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
    return null;
  }
  if (isset($file['size']) && (int) $file['size'] > VENKI_UPLOAD_MAX_BYTES) {
    return null;
  }

  $mime = null;
  if (function_exists('finfo_open')) {
    $f = finfo_open(FILEINFO_MIME_TYPE);
    if ($f !== false) {
      $mime = finfo_file($f, $file['tmp_name']);
      finfo_close($f);
    }
  }
  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
  ];
  if ($mime === null || !isset($allowed[$mime])) {
    return null;
  }
  $ext = $allowed[$mime];

  $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '', $itemId);
  if ($safeId === '') {
    $safeId = 'item';
  }
  $name = $safeId . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

  $dir = venki_uploads_products_dir();
  if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
    return null;
  }

  $dest = $dir . '/' . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) {
    return null;
  }

  return 'uploads/products/' . $name;
}
