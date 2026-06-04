<?php
declare(strict_types=1);

/**
 * Ожидает: $catalogSlug ('venki'|'groby'), $categories (array), $csrf (string), $catalogHumanTitle (string)
 */
if (!isset($catalogSlug, $categories, $csrf, $catalogHumanTitle)) {
  return;
}
$catalogSlugEsc = htmlspecialchars((string) $catalogSlug, ENT_QUOTES, 'UTF-8');
?>
<div class="card mb-4">
  <div class="card-body">
    <h2 class="h5 mb-3"><?php echo htmlspecialchars($catalogHumanTitle, ENT_QUOTES, 'UTF-8'); ?> — новая вкладка</h2>
    <form method="post" action="actions/post.php" class="row g-2 align-items-end">
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="action" value="add_category">
      <input type="hidden" name="catalog_slug" value="<?php echo $catalogSlugEsc; ?>">
      <div class="col-md-3">
        <label class="form-label small">ID (латиница)</label>
        <input type="text" name="category_id" class="form-control form-control-sm" placeholder="cm180" required pattern="[a-zA-Z0-9_-]+">
      </div>
      <div class="col-md-5">
        <label class="form-label small">Название вкладки</label>
        <input type="text" name="label" class="form-control form-control-sm" placeholder="Название" required>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-success btn-sm">Добавить вкладку</button>
      </div>
    </form>
  </div>
</div>

<?php foreach ($categories as $cat) :
  if (!is_array($cat)) {
    continue;
  }
  $cid = (string) ($cat['id'] ?? '');
  $clabel = (string) ($cat['label'] ?? '');
  $items = isset($cat['items']) && is_array($cat['items']) ? $cat['items'] : [];
  $cidEsc = htmlspecialchars($cid, ENT_QUOTES, 'UTF-8');
  ?>
  <div class="card mb-4 cat-block">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <form method="post" action="actions/post.php" class="row g-2 align-items-end flex-grow-1">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="action" value="save_category_label">
          <input type="hidden" name="catalog_slug" value="<?php echo $catalogSlugEsc; ?>">
          <input type="hidden" name="category_id" value="<?php echo $cidEsc; ?>">
          <div class="col-auto">
            <span class="badge bg-secondary"><?php echo $cidEsc; ?></span>
          </div>
          <div class="col-md-4">
            <label class="form-label small mb-0">Название</label>
            <input type="text" name="label" class="form-control form-control-sm" value="<?php echo htmlspecialchars($clabel, ENT_QUOTES, 'UTF-8'); ?>" required>
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary btn-sm">Сохранить</button>
          </div>
        </form>
        <form method="post" action="actions/post.php" onsubmit="return confirm('Удалить вкладку и все товары в ней?');">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="action" value="delete_category">
          <input type="hidden" name="catalog_slug" value="<?php echo $catalogSlugEsc; ?>">
          <input type="hidden" name="category_id" value="<?php echo $cidEsc; ?>">
          <button type="submit" class="btn btn-outline-danger btn-sm">Удалить вкладку</button>
        </form>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="h6 text-muted mb-0">Товары</h3>
        <a class="btn btn-success btn-sm" href="product.php?catalog=<?php echo $catalogSlugEsc; ?>&amp;category_id=<?php echo $cidEsc; ?>">+ Добавить товар</a>
      </div>

      <?php if ($items === []) : ?>
        <p class="text-muted small mb-3">Пока нет товаров.</p>
      <?php else : ?>
        <div class="table-responsive border rounded bg-white">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:72px"></th>
                <th>Название</th>
                <th style="width:140px"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it) :
                if (!is_array($it)) {
                  continue;
                }
                $iid = (string) ($it['id'] ?? '');
                $tit = (string) ($it['title'] ?? '');
                $img = (string) ($it['image'] ?? '');
                $thumbSrc = '';
                if ($img !== '') {
                  $thumbSrc = preg_match('#^https?://#i', $img) ? $img : ('../' . ltrim($img, '/'));
                }
                $editHref = 'product.php?' . http_build_query([
                  'catalog' => $catalogSlug,
                  'category_id' => $cid,
                  'item_id' => $iid,
                ]);
                ?>
                <tr>
                  <td>
                    <?php if ($thumbSrc !== '') : ?>
                      <img class="admin-thumb" src="<?php echo htmlspecialchars($thumbSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="">
                    <?php else : ?>
                      <div class="admin-thumb d-flex align-items-center justify-content-center small text-muted">нет</div>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($tit, ENT_QUOTES, 'UTF-8'); ?></td>
                  <td class="text-end">
                    <a class="btn btn-outline-primary btn-sm" href="<?php echo htmlspecialchars($editHref, ENT_QUOTES, 'UTF-8'); ?>">Редактировать</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

<?php if ($categories === []) : ?>
  <p class="text-muted">Нет вкладок — добавьте первую выше.</p>
<?php endif; ?>
