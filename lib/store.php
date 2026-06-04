<?php
declare(strict_types=1);

require_once __DIR__ . '/paths.php';

function venki_catalog_slugs(): array
{
  return ['venki', 'groby'];
}

function venki_catalog_slug_valid(string $slug): bool
{
  return in_array($slug, venki_catalog_slugs(), true);
}

function venki_catalog_label(string $slug): string
{
  $map = [
    'venki' => 'Венки',
    'groby' => 'Гробы',
  ];
  return $map[$slug] ?? $slug;
}

function venki_format_price(int $rub): string
{
  return number_format($rub, 0, ',', "\u{00A0}") . "\u{00A0}₽";
}

function venki_catalog_data_path(string $slug): string
{
  return VENKI_DATA . '/catalog_' . $slug . '.json';
}

function venki_load_catalog_slug(string $slug): array
{
  if (!venki_catalog_slug_valid($slug)) {
    return ['categories' => []];
  }
  $p = venki_catalog_data_path($slug);
  if (!is_readable($p)) {
    return ['categories' => []];
  }
  $raw = file_get_contents($p);
  if ($raw === false) {
    return ['categories' => []];
  }
  $data = json_decode($raw, true);
  return is_array($data) ? $data : ['categories' => []];
}

function venki_save_catalog_slug(string $slug, array $data): bool
{
  if (!venki_catalog_slug_valid($slug)) {
    return false;
  }
  if (!isset($data['categories']) || !is_array($data['categories'])) {
    return false;
  }
  $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  if ($json === false) {
    return false;
  }
  return venki_atomic_write(venki_catalog_data_path($slug), $json);
}

function venki_leads_path(): string
{
  return VENKI_DATA . '/leads.json';
}

function venki_load_leads(): array
{
  $p = venki_leads_path();
  if (!is_readable($p)) {
    return [];
  }
  $raw = file_get_contents($p);
  if ($raw === false) {
    return [];
  }
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function venki_save_leads(array $leads): bool
{
  $json = json_encode(array_values($leads), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  if ($json === false) {
    return false;
  }
  return venki_atomic_write(venki_leads_path(), $json);
}

function venki_append_lead(array $lead): bool
{
  $leads = venki_load_leads();
  $leads[] = $lead;
  return venki_save_leads($leads);
}

function venki_atomic_write(string $path, string $content): bool
{
  $dir = dirname($path);
  if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
  }
  $tmp = $path . '.' . bin2hex(random_bytes(4)) . '.tmp';
  $fp = fopen($tmp, 'wb');
  if ($fp === false) {
    return false;
  }
  if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    @unlink($tmp);
    return false;
  }
  fwrite($fp, $content);
  fflush($fp);
  flock($fp, LOCK_UN);
  fclose($fp);
  if (!rename($tmp, $path)) {
    @unlink($tmp);
    return false;
  }
  return true;
}
