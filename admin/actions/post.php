<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/lib/admin_auth.php';
require_once dirname(__DIR__, 2) . '/lib/store.php';
require_once dirname(__DIR__, 2) . '/lib/uploads.php';

venki_admin_session_start();

if (!venki_admin_logged_in()) {
  header('Location: ../index.php', true, 302);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit;
}

if (!venki_admin_verify_csrf($_POST['csrf'] ?? null)) {
  http_response_code(403);
  echo 'Недействительный токен формы. Обновите страницу.';
  exit;
}

$action = isset($_POST['action']) ? (string) $_POST['action'] : '';
$redirect = '../index.php';
$err = null;

function venki_post_resolve_catalog_slug(): ?string
{
  $s = isset($_POST['catalog_slug']) ? trim((string) $_POST['catalog_slug']) : '';
  return venki_catalog_slug_valid($s) ? $s : null;
}

function venki_cat_find_index(array $catalog, string $catId): ?int
{
  foreach ($catalog['categories'] as $i => $c) {
    if (isset($c['id']) && (string) $c['id'] === $catId) {
      return (int) $i;
    }
  }
  return null;
}

switch ($action) {
  case 'delete_lead':
    $id = isset($_POST['id']) ? (string) $_POST['id'] : '';
    if ($id !== '') {
      $leads = venki_load_leads();
      $leads = array_values(array_filter($leads, static function ($l) use ($id) {
        return !is_array($l) || (($l['id'] ?? '') !== $id);
      }));
      if (!venki_save_leads($leads)) {
        $err = 'lead_save';
      }
    }
    break;

  case 'add_category':
    $slug = venki_post_resolve_catalog_slug();
    if ($slug === null) {
      $err = 'bad_catalog';
      break;
    }
    $cid = isset($_POST['category_id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $_POST['category_id']) : '';
    $label = isset($_POST['label']) ? trim((string) $_POST['label']) : '';
    if ($cid === '' || $label === '') {
      $err = 'cat_empty';
      break;
    }
    $catalog = venki_load_catalog_slug($slug);
    if (!isset($catalog['categories']) || !is_array($catalog['categories'])) {
      $catalog['categories'] = [];
    }
    foreach ($catalog['categories'] as $c) {
      if (($c['id'] ?? '') === $cid) {
        $err = 'cat_exists';
        break 2;
      }
    }
    $catalog['categories'][] = [
      'id' => $cid,
      'label' => $label,
      'items' => [],
    ];
    if (!venki_save_catalog_slug($slug, $catalog)) {
      $err = 'cat_save';
    }
    break;

  case 'delete_category':
    $slug = venki_post_resolve_catalog_slug();
    if ($slug === null) {
      $err = 'bad_catalog';
      break;
    }
    $catId = isset($_POST['category_id']) ? (string) $_POST['category_id'] : '';
    if ($catId === '') {
      break;
    }
    $catalog = venki_load_catalog_slug($slug);
    if (!isset($catalog['categories']) || !is_array($catalog['categories'])) {
      $catalog['categories'] = [];
    }
    $delIdx = venki_cat_find_index($catalog, $catId);
    if ($delIdx !== null && isset($catalog['categories'][$delIdx]['items'])) {
      foreach ($catalog['categories'][$delIdx]['items'] as $it) {
        if (is_array($it)) {
          venki_delete_product_image_file((string) ($it['image'] ?? ''));
        }
      }
    }
    $catalog['categories'] = array_values(array_filter(
      $catalog['categories'],
      static function ($c) use ($catId) {
        return !is_array($c) || (($c['id'] ?? '') !== $catId);
      }
    ));
    if (!venki_save_catalog_slug($slug, $catalog)) {
      $err = 'cat_save';
    }
    break;

  case 'save_category_label':
    $slug = venki_post_resolve_catalog_slug();
    if ($slug === null) {
      $err = 'bad_catalog';
      break;
    }
    $catId = isset($_POST['category_id']) ? (string) $_POST['category_id'] : '';
    $label = isset($_POST['label']) ? trim((string) $_POST['label']) : '';
    if ($catId === '' || $label === '') {
      $err = 'label_empty';
      break;
    }
    $catalog = venki_load_catalog_slug($slug);
    if (!isset($catalog['categories']) || !is_array($catalog['categories'])) {
      $catalog['categories'] = [];
    }
    $idx = venki_cat_find_index($catalog, $catId);
    if ($idx === null) {
      $err = 'not_found';
      break;
    }
    $catalog['categories'][$idx]['label'] = $label;
    if (!venki_save_catalog_slug($slug, $catalog)) {
      $err = 'cat_save';
    }
    break;

  case 'delete_item':
    $slug = venki_post_resolve_catalog_slug();
    if ($slug === null) {
      $err = 'bad_catalog';
      break;
    }
    $catId = isset($_POST['category_id']) ? (string) $_POST['category_id'] : '';
    $itemId = isset($_POST['item_id']) ? (string) $_POST['item_id'] : '';
    if ($catId === '' || $itemId === '') {
      break;
    }
    $catalog = venki_load_catalog_slug($slug);
    if (!isset($catalog['categories']) || !is_array($catalog['categories'])) {
      $catalog['categories'] = [];
    }
    $cidx = venki_cat_find_index($catalog, $catId);
    if ($cidx === null) {
      $err = 'not_found';
      break;
    }
    $items = $catalog['categories'][$cidx]['items'] ?? [];
    foreach (is_array($items) ? $items : [] as $it) {
      if (is_array($it) && (($it['id'] ?? '') === $itemId)) {
        venki_delete_product_image_file((string) ($it['image'] ?? ''));
        break;
      }
    }
    $catalog['categories'][$cidx]['items'] = array_values(array_filter(
      is_array($items) ? $items : [],
      static function ($it) use ($itemId) {
        return !is_array($it) || (($it['id'] ?? '') !== $itemId);
      }
    ));
    if (!venki_save_catalog_slug($slug, $catalog)) {
      $err = 'cat_save';
    }
    break;

  default:
    $err = 'unknown_action';
}

$q = $err ? ('?e=' . rawurlencode($err)) : '';
header('Location: ' . $redirect . $q, true, 302);
exit;
