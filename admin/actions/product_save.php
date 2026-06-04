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
  header('Location: ../product.php', true, 302);
  exit;
}

if (!venki_admin_verify_csrf($_POST['csrf'] ?? null)) {
  $slug = isset($_POST['catalog_slug']) ? trim((string) $_POST['catalog_slug']) : '';
  $cid = isset($_POST['category_id']) ? trim((string) $_POST['category_id']) : '';
  $iid = isset($_POST['item_id']) ? trim((string) $_POST['item_id']) : '';
  if (venki_catalog_slug_valid($slug) && $cid !== '') {
    $q = ['catalog' => $slug, 'category_id' => $cid, 'e' => 'csrf'];
    if ($iid !== '') {
      $q['item_id'] = $iid;
    }
    header('Location: ../product.php?' . http_build_query($q), true, 302);
  } else {
    header('Location: ../product.php?e=csrf', true, 302);
  }
  exit;
}

function venki_find_cat_idx(array $catalog, string $catId): ?int
{
  foreach ($catalog['categories'] as $i => $c) {
    if (isset($c['id']) && (string) $c['id'] === $catId) {
      return (int) $i;
    }
  }
  return null;
}

function venki_find_item_idx(array $items, string $itemId): ?int
{
  foreach ($items as $j => $it) {
    if (is_array($it) && (($it['id'] ?? '') === $itemId)) {
      return (int) $j;
    }
  }
  return null;
}

function venki_product_redirect(string $catalogSlug, string $categoryId, ?string $itemId, array $extra = []): string
{
  $q = [
    'catalog' => $catalogSlug,
    'category_id' => $categoryId,
  ];
  if ($itemId !== null && $itemId !== '') {
    $q['item_id'] = $itemId;
  }
  $q = array_merge($q, $extra);
  return '../product.php?' . http_build_query($q);
}

$catalogSlug = isset($_POST['catalog_slug']) ? trim((string) $_POST['catalog_slug']) : '';
if (!venki_catalog_slug_valid($catalogSlug)) {
  header('Location: ../index.php?e=bad_catalog', true, 302);
  exit;
}

$categoryId = isset($_POST['category_id']) ? trim((string) $_POST['category_id']) : '';
$itemId = isset($_POST['item_id']) ? trim((string) $_POST['item_id']) : '';
$title = isset($_POST['title']) ? trim((string) $_POST['title']) : '';
$imageUrl = isset($_POST['image_url']) ? trim((string) $_POST['image_url']) : '';
$removeImage = !empty($_POST['remove_image']);

if ($categoryId === '' || $title === '') {
  header('Location: ' . venki_product_redirect($catalogSlug, $categoryId, $itemId !== '' ? $itemId : null, ['e' => 'required']), true, 302);
  exit;
}

$catalog = venki_load_catalog_slug($catalogSlug);
if (!isset($catalog['categories']) || !is_array($catalog['categories'])) {
  $catalog['categories'] = [];
}

$cidx = venki_find_cat_idx($catalog, $categoryId);
if ($cidx === null) {
  header('Location: ../index.php?e=not_found', true, 302);
  exit;
}

if ($itemId === '') {
  $newId = 'i-' . bin2hex(random_bytes(4));
  $uploadPath = null;
  if (!empty($_FILES['image_file']['tmp_name'])) {
    $uploadPath = venki_save_uploaded_product_image($_FILES['image_file'], $newId);
    if ($uploadPath === null) {
      header('Location: ' . venki_product_redirect($catalogSlug, $categoryId, null, ['e' => 'upload']), true, 302);
      exit;
    }
  }

  $image = '';
  if ($uploadPath !== null) {
    $image = $uploadPath;
  } elseif ($imageUrl !== '') {
    $image = $imageUrl;
  }

  if (!isset($catalog['categories'][$cidx]['items']) || !is_array($catalog['categories'][$cidx]['items'])) {
    $catalog['categories'][$cidx]['items'] = [];
  }
  $catalog['categories'][$cidx]['items'][] = [
    'id' => $newId,
    'title' => $title,
    'image' => $image,
  ];
  if (!venki_save_catalog_slug($catalogSlug, $catalog)) {
    header('Location: ../index.php?e=cat_save', true, 302);
    exit;
  }
  header('Location: ' . venki_product_redirect($catalogSlug, $categoryId, $newId, ['ok' => '1']), true, 302);
  exit;
}

$items = $catalog['categories'][$cidx]['items'] ?? [];
$iidx = venki_find_item_idx(is_array($items) ? $items : [], $itemId);
if ($iidx === null) {
  header('Location: ../index.php?e=item_not_found', true, 302);
  exit;
}

$old = $catalog['categories'][$cidx]['items'][$iidx];
$oldImage = is_array($old) ? (string) ($old['image'] ?? '') : '';

$catalog['categories'][$cidx]['items'][$iidx]['title'] = $title;

$uploadPath = null;
if (!empty($_FILES['image_file']['tmp_name'])) {
  $uploadPath = venki_save_uploaded_product_image($_FILES['image_file'], $itemId);
  if ($uploadPath === null) {
    header('Location: ' . venki_product_redirect($catalogSlug, $categoryId, $itemId, ['e' => 'upload']), true, 302);
    exit;
  }
}

if ($removeImage) {
  venki_delete_product_image_file($oldImage);
  $catalog['categories'][$cidx]['items'][$iidx]['image'] = '';
} elseif ($uploadPath !== null) {
  venki_delete_product_image_file($oldImage);
  $catalog['categories'][$cidx]['items'][$iidx]['image'] = $uploadPath;
} elseif ($imageUrl !== '') {
  if ($oldImage !== $imageUrl) {
    venki_delete_product_image_file($oldImage);
  }
  $catalog['categories'][$cidx]['items'][$iidx]['image'] = $imageUrl;
}

if (!venki_save_catalog_slug($catalogSlug, $catalog)) {
  header('Location: ../index.php?e=cat_save', true, 302);
  exit;
}

header('Location: ' . venki_product_redirect($catalogSlug, $categoryId, $itemId, ['ok' => '1']), true, 302);
exit;
