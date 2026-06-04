<?php
declare(strict_types=1);

$galleryPath = dirname(__DIR__) . '/data/production_gallery.json';
$galleryImages = [];
if (is_readable($galleryPath)) {
  $raw = file_get_contents($galleryPath);
  if ($raw !== false) {
    $data = json_decode($raw, true);
    if (is_array($data) && isset($data['images']) && is_array($data['images'])) {
      $galleryImages = $data['images'];
    }
  }
}
?>
<section class="production-hero py-5 bg-white border-bottom">
  <div class="container-site">
    <nav class="production-breadcrumb small text-muted mb-3" aria-label="Навигация">
      <a href="<?php echo htmlspecialchars(isset($navBase) && $navBase !== '' ? $navBase . '#home' : '#home', ENT_QUOTES, 'UTF-8'); ?>">Главная</a>
      <span class="mx-1" aria-hidden="true">/</span>
      <span class="text-body">Производство</span>
    </nav>
    <h1 class="production-title h2 fw-bold mb-3">Собственное производство</h1>
    <p class="production-lead lead text-muted mb-0 col-lg-10">
      Мы изготавливаем ритуальные венки, корзины и сопутствующую продукцию на собственных мощностях — от подбора материалов до финальной проверки перед отправкой.
    </p>
  </div>
</section>

<section class="production-article py-5">
  <div class="container-site">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-9">
        <div class="production-prose">
          <h2 class="h4 fw-semibold mb-3">Венки и цветочные композиции</h2>
          <p>
            Производство венков ведётся по отработанным технологиям: каркас, надежное крепление искусственных и натуральных элементов,
            симметрия композиции и аккуратная обработка лент и фурнитуры. Мы выпускаем модели разных диаметров — от компактных 100&nbsp;см до представительных 150&nbsp;см,
            а также корзины и комбинированные решения для траурных церемоний и оптовых поставок.
          </p>
          <p>
            Сырьё и расходные материалы закупаются у проверенных поставщиков; партии проходят входной контроль. На каждом этапе — от заготовки до упаковки —
            изделие проверяется на прочность креплений и внешний вид, чтобы продукция достойно выглядела в самый ответственный момент.
          </p>

          <h2 class="h4 fw-semibold mb-3 mt-5">Гробы и деревообработка</h2>
          <p>
            Участок по изготовлению гробов объединяет столярную обработку, подготовку поверхностей и финишное оформление. Используются влагостойкие покрытия и фурнитура,
            рассчитанные на условия транспортировки и эксплуатации. Конструкции собираются с учётом требований к нагрузке и габаритам — как для стандартных моделей, так и под индивидуальные заказы.
          </p>
          <p>
            Мы стремимся к тому, чтобы и венки, и гробы отражали уважение к традиции и одновременно соответствовали ожиданиям заказчиков по срокам и объёму партий.
            При необходимости менеджеры подскажут по ассортименту в каталогах <a href="venki.php">«Венки»</a> и <a href="groby.php">«Гробы»</a> и помогут согласовать поставку по России.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ($galleryImages !== []) :
  $galleryRows = [];
  foreach ($galleryImages as $img) {
    if (!is_array($img)) {
      continue;
    }
    $src = isset($img['src']) ? (string) $img['src'] : '';
    if ($src === '') {
      continue;
    }
    $galleryRows[] = [
      'idx' => count($galleryRows),
      'src' => $src,
      'alt' => isset($img['alt']) ? (string) $img['alt'] : '',
      'caption' => isset($img['caption']) ? (string) $img['caption'] : '',
    ];
  }
  if ($galleryRows === []) {
    // пустой или некорректный список в JSON
  } else {
  ?>
<section class="production-gallery-section py-5 bg-light border-top border-bottom">
  <div class="container-site">
    <h2 class="h3 fw-bold mb-2 text-center">Фотогалерея</h2>
    <p class="text-muted text-center mb-4 mb-lg-5 col-lg-8 mx-auto small">
      Фрагменты производства: сборка венков, корзины и подготовка продукции к отгрузке.
    </p>
    <div class="production-gallery-grid">
      <?php foreach ($galleryRows as $row) :
        $srcEsc = htmlspecialchars($row['src'], ENT_QUOTES, 'UTF-8');
        $altEsc = htmlspecialchars($row['alt'], ENT_QUOTES, 'UTF-8');
        $capEsc = htmlspecialchars($row['caption'], ENT_QUOTES, 'UTF-8');
        $modalId = 'productionGalleryModal' . $row['idx'];
        ?>
        <figure class="production-gallery-card">
          <button
            type="button"
            class="production-gallery-trigger"
            data-bs-toggle="modal"
            data-bs-target="#<?php echo htmlspecialchars($modalId, ENT_QUOTES, 'UTF-8'); ?>"
            aria-label="Открыть фото: <?php echo $altEsc !== '' ? $altEsc : 'Изображение'; ?>"
          >
            <span class="production-gallery-aspect">
              <img src="<?php echo $srcEsc; ?>" alt="<?php echo $altEsc; ?>" loading="lazy" decoding="async" width="800" height="600">
            </span>
            <span class="production-gallery-zoom" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
              </svg>
            </span>
          </button>
          <?php if ($row['caption'] !== '') : ?>
            <figcaption class="production-gallery-caption"><?php echo $capEsc; ?></figcaption>
          <?php endif; ?>
        </figure>
      <?php endforeach; ?>
    </div>

    <?php foreach ($galleryRows as $row) :
      $srcEsc = htmlspecialchars($row['src'], ENT_QUOTES, 'UTF-8');
      $altEsc = htmlspecialchars($row['alt'], ENT_QUOTES, 'UTF-8');
      $capEsc = htmlspecialchars($row['caption'], ENT_QUOTES, 'UTF-8');
      $modalId = 'productionGalleryModal' . $row['idx'];
      $titleEsc = $row['caption'] !== '' ? $capEsc : ($row['alt'] !== '' ? $altEsc : 'Фото');
      ?>
      <div class="modal fade production-gallery-modal" id="<?php echo htmlspecialchars($modalId, ENT_QUOTES, 'UTF-8'); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
          <div class="modal-content bg-dark border-0">
            <div class="modal-header border-secondary border-opacity-25 py-2">
              <p class="modal-title text-white small mb-0"><?php echo $titleEsc; ?></p>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body p-2 p-sm-3 text-center">
              <img class="img-fluid production-gallery-modal-img rounded-1" src="<?php echo $srcEsc; ?>" alt="<?php echo $altEsc; ?>">
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php
  }
endif;
?>
