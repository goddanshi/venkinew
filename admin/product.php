<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/admin_auth.php';
require_once dirname(__DIR__) . '/lib/store.php';

venki_admin_session_start();

if (!venki_admin_logged_in()) {
  header('Location: index.php', true, 302);
  exit;
}

$csrf = venki_admin_csrf_token();

$errorsUi = [
  'required' => 'Укажите название товара.',
  'upload' => 'Файл не подходит. Допустимы JPEG, PNG, WebP, GIF до 5 МБ.',
  'csrf' => 'Сессия устарела. Откройте форму снова.',
  'bad_catalog' => 'Некорректный каталог.',
];

$catalogSlug = isset($_GET['catalog']) ? trim((string) $_GET['catalog']) : 'venki';
if (!venki_catalog_slug_valid($catalogSlug)) {
  header('Location: index.php?e=bad_catalog', true, 302);
  exit;
}

$categoryId = isset($_GET['category_id']) ? trim((string) $_GET['category_id']) : '';
$itemId = isset($_GET['item_id']) ? trim((string) $_GET['item_id']) : '';

if ($categoryId === '') {
  header('Location: index.php?e=cat_empty', true, 302);
  exit;
}

$catalog = venki_load_catalog_slug($catalogSlug);
if (!isset($catalog['categories']) || !is_array($catalog['categories'])) {
  $catalog['categories'] = [];
}
$catLabel = '';
$item = null;

foreach ($catalog['categories'] ?? [] as $c) {
  if (!is_array($c) || (($c['id'] ?? '') !== $categoryId)) {
    continue;
  }
  $catLabel = (string) ($c['label'] ?? $categoryId);
  if ($itemId !== '') {
    foreach ($c['items'] ?? [] as $it) {
      if (is_array($it) && (($it['id'] ?? '') === $itemId)) {
        $item = $it;
        break;
      }
    }
  }
  break;
}

if ($catLabel === '') {
  header('Location: index.php?e=not_found', true, 302);
  exit;
}

if ($itemId !== '' && (!is_array($item))) {
  header('Location: index.php?e=item_not_found', true, 302);
  exit;
}

$isNew = $itemId === '';
$pageTitle = $isNew ? 'Новый товар' : 'Редактирование товара';
$titleVal = $isNew ? '' : (string) ($item['title'] ?? '');
$imageVal = $isNew ? '' : (string) ($item['image'] ?? '');

function venki_admin_img_preview(?string $src): string
{
  if ($src === null || trim($src) === '') {
    return '';
  }
  $s = trim($src);
  if (preg_match('#^https?://#i', $s)) {
    return $s;
  }
  return '../' . ltrim($s, '/');
}

$flashErr = isset($_GET['e']) && isset($errorsUi[$_GET['e']]) ? $errorsUi[$_GET['e']] : '';
$flashOk = isset($_GET['ok']) && $_GET['ok'] === '1';
$catalogSlugEsc = htmlspecialchars($catalogSlug, ENT_QUOTES, 'UTF-8');
$catalogTypeLabel = htmlspecialchars(venki_catalog_label($catalogSlug), ENT_QUOTES, 'UTF-8');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> — ВГМК</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>
    body { background: #f1f3ef; }
    .admin-nav { background: #1a5f3c; }
    .preview-img { max-width: 280px; max-height: 220px; object-fit: contain; border-radius: .5rem; background: #eee; }
  </style>
</head>
<body>
  <nav class="admin-nav navbar navbar-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand mb-0 h1 fs-5" href="index.php">← В админку</a>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Выйти</a>
    </div>
  </nav>

  <div class="container pb-5" style="max-width: 720px;">
    <p class="text-muted small mb-1"><?php echo $catalogTypeLabel; ?> — <?php echo htmlspecialchars($catLabel, ENT_QUOTES, 'UTF-8'); ?></p>
    <h1 class="h4 mb-4"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

    <?php if ($flashOk) : ?>
      <div class="alert alert-success py-2">Сохранено</div>
    <?php endif; ?>
    <?php if ($flashErr !== '') : ?>
      <div class="alert alert-danger py-2"><?php echo htmlspecialchars($flashErr, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (!$isNew && is_array($item)) :
      $previewSrc = venki_admin_img_preview($imageVal);
      ?>
      <?php if ($previewSrc !== '') : ?>
        <div class="mb-3">
          <div class="small text-muted mb-1">Текущее фото</div>
          <img class="preview-img d-block" src="<?php echo htmlspecialchars($previewSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="">
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <form method="post" action="actions/product_save.php" enctype="multipart/form-data" class="card border-0 shadow-sm">
      <div class="card-body">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="catalog_slug" value="<?php echo $catalogSlugEsc; ?>">
        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($categoryId, ENT_QUOTES, 'UTF-8'); ?>">
        <?php if (!$isNew) : ?>
          <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8'); ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label" for="title">Название</label>
          <input type="text" class="form-control" id="title" name="title" required maxlength="200"
            value="<?php echo htmlspecialchars($titleVal, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label" for="image_file">Фото товара</label>
          <input type="file" class="form-control" id="image_file" name="image_file"
            accept="image/jpeg,image/png,image/webp,image/gif">
          <div class="form-text">JPEG, PNG, WebP или GIF, до 5 МБ. Если загружаете файл, он заменит текущее изображение.</div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="image_url">Или ссылка на картинку</label>
          <input type="text" class="form-control" id="image_url" name="image_url"
            value="<?php echo htmlspecialchars((preg_match('#^https?://#i', $imageVal) ? $imageVal : ''), ENT_QUOTES, 'UTF-8'); ?>"
            placeholder="https://…">
          <div class="form-text">Необязательно, если загрузили файл выше.</div>
        </div>

        <?php if (!$isNew && $imageVal !== '') : ?>
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
            <label class="form-check-label" for="remove_image">Удалить изображение</label>
          </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 align-items-center">
          <button type="submit" class="btn btn-success">Сохранить</button>
          <a class="btn btn-outline-secondary" href="index.php">К списку товаров</a>
        </div>
      </div>
    </form>

    <?php if (!$isNew) : ?>
      <form method="post" action="actions/post.php" class="mt-3" onsubmit="return confirm('Удалить товар навсегда?');">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="delete_item">
        <input type="hidden" name="catalog_slug" value="<?php echo $catalogSlugEsc; ?>">
        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($categoryId, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-outline-danger btn-sm">Удалить товар</button>
      </form>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
