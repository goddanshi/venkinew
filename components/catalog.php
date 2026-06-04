<?php
/**
 * Каталог: табы и карточки из data/catalog_{slug}.json
 * Перед include задайте $catalogSlug (venki|groby) и опционально $catalogHeading
 */
require_once __DIR__ . '/../lib/store.php';

$allowedSlugs = venki_catalog_slugs();
$rawSlug = isset($catalogSlug) ? (string) $catalogSlug : 'venki';
$catalogSlug = in_array($rawSlug, $allowedSlugs, true) ? $rawSlug : 'venki';
$catalogHeading = isset($catalogHeading) ? (string) $catalogHeading : venki_catalog_label($catalogSlug);

$catalogData = venki_load_catalog_slug($catalogSlug);
$categories = [];
if (is_array($catalogData) && isset($catalogData['categories']) && is_array($catalogData['categories'])) {
  $categories = $catalogData['categories'];
}
$catalogHumanLabel = venki_catalog_label($catalogSlug);
$uid = preg_replace('/[^a-z]/', '', $catalogSlug);
$tabIds = [];
foreach ($categories as $idx => $cat) {
  $tid = isset($cat['id']) && $cat['id'] !== '' ? (string) $cat['id'] : 'cat-' . $idx;
  $tabIds[$idx] = preg_replace('/[^a-zA-Z0-9_-]/', '', $tid) ?: 'cat-' . $idx;
}
?>
<section id="catalog" class="catalog-section py-5 bg-white">
  <div class="container-site">
    <h2 class="catalog-heading h3 fw-bold mb-4 mb-lg-5 text-center text-md-start"><?php echo htmlspecialchars($catalogHeading, ENT_QUOTES, 'UTF-8'); ?></h2>

    <?php if ($categories === []) : ?>
      <p class="text-muted mb-0">Каталог временно пуст. Зайдите позже.</p>
    <?php else : ?>
      <ul class="nav nav-tabs catalog-tabs gap-2 border-0" id="catalogTabs-<?php echo htmlspecialchars($uid, ENT_QUOTES, 'UTF-8'); ?>" role="tablist">
        <?php foreach ($categories as $idx => $cat) :
          $paneId = 'catalog-' . $uid . '-pane-' . $tabIds[$idx];
          $tabBtnId = 'catalog-' . $uid . '-tab-' . $tabIds[$idx];
          $label = isset($cat['label']) ? (string) $cat['label'] : '';
          $isFirst = $idx === 0;
          ?>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link catalog-tab-link <?php echo $isFirst ? 'active' : ''; ?>"
              id="<?php echo htmlspecialchars($tabBtnId, ENT_QUOTES, 'UTF-8'); ?>"
              data-bs-toggle="tab"
              data-bs-target="#<?php echo htmlspecialchars($paneId, ENT_QUOTES, 'UTF-8'); ?>"
              type="button"
              role="tab"
              aria-controls="<?php echo htmlspecialchars($paneId, ENT_QUOTES, 'UTF-8'); ?>"
              aria-selected="<?php echo $isFirst ? 'true' : 'false'; ?>"
            >
              <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
            </button>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="tab-content catalog-tab-content pt-4" id="catalogTabContent-<?php echo htmlspecialchars($uid, ENT_QUOTES, 'UTF-8'); ?>">
        <?php foreach ($categories as $idx => $cat) :
          $paneId = 'catalog-' . $uid . '-pane-' . $tabIds[$idx];
          $items = isset($cat['items']) && is_array($cat['items']) ? $cat['items'] : [];
          $label = isset($cat['label']) ? (string) $cat['label'] : '';
          $isFirst = $idx === 0;
          ?>
          <div
            class="tab-pane fade <?php echo $isFirst ? 'show active' : ''; ?>"
            id="<?php echo htmlspecialchars($paneId, ENT_QUOTES, 'UTF-8'); ?>"
            role="tabpanel"
            aria-labelledby="<?php echo htmlspecialchars('catalog-' . $uid . '-tab-' . $tabIds[$idx], ENT_QUOTES, 'UTF-8'); ?>"
          >
            <div class="row g-4 catalog-grid">
              <?php foreach ($items as $item) :
                $itemId = isset($item['id']) ? (string) $item['id'] : '';
                $title = isset($item['title']) ? (string) $item['title'] : '';
                $image = isset($item['image']) ? (string) $item['image'] : '';
                $price = isset($item['price']) ? (int) $item['price'] : 0;
                $categoryIdForCart = isset($cat['id']) ? (string) $cat['id'] : '';
                ?>
                <div class="col-12 col-md-6 col-lg-3">
                  <article class="catalog-card card h-100 border-0 shadow-sm">
                    <div class="catalog-card-img-wrap">
                      <?php if ($image !== '') : ?>
                        <img
                          class="catalog-card-img card-img-top"
                          src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>"
                          alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                          width="600"
                          height="450"
                          loading="lazy"
                          decoding="async"
                        >
                      <?php else : ?>
                        <div class="catalog-card-placeholder ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted small">
                          Нет фото
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                      <h3 class="catalog-card-title h6 card-title mb-2"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
                      <?php if ($price > 0) : ?>
                        <p class="catalog-card-price mb-3"><?php echo htmlspecialchars(venki_format_price($price), ENT_QUOTES, 'UTF-8'); ?></p>
                      <?php endif; ?>
                      <div class="d-grid gap-2 mt-auto">
                        <button
                          type="button"
                          class="btn btn-catalog-order btn-add-to-cart w-100"
                          data-catalog="<?php echo htmlspecialchars($catalogSlug, ENT_QUOTES, 'UTF-8'); ?>"
                          data-catalog-human="<?php echo htmlspecialchars($catalogHumanLabel, ENT_QUOTES, 'UTF-8'); ?>"
                          data-category-id="<?php echo htmlspecialchars($categoryIdForCart, ENT_QUOTES, 'UTF-8'); ?>"
                          data-category-label="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>"
                          data-item-id="<?php echo htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8'); ?>"
                          data-title="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                          data-image="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                          В корзину
                        </button>
                        <a
                          class="btn btn-outline-secondary btn-sm"
                          href="<?php echo htmlspecialchars(isset($navBase) && $navBase !== '' ? $navBase . '#contacts' : '#contacts', ENT_QUOTES, 'UTF-8'); ?>"
                        >
                          Связаться
                        </a>
                      </div>
                    </div>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
