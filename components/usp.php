<?php
/**
 * УТП: преимущества сотрудничества.
 */
$uspItems = [
  [
    'title' => 'Наш подход',
    'text' => 'Мы ценим каждого клиента и готовы предложить индивидуальные условия.',
  ],
  [
    'title' => 'Оперативность',
    'text' => 'Быстро привезем продукцию в любой район города или ближайшей области.',
  ],
  [
    'title' => 'Актуальность',
    'text' => 'Мы следим за всеми новинками в ритуальной флористике.',
  ],
  [
    'title' => 'Наличие товаров',
    'text' => 'Большой выбор продукции всегда в наличии на нашем складе.',
  ],
];
?>
<section class="usp-section py-5 bg-light">
  <div class="container-site">
    <h2 class="usp-heading h3 fw-bold text-center text-md-start mb-4 mb-lg-5">
      Сотрудничество с нами это:
    </h2>
    <div class="row g-4">
      <?php foreach ($uspItems as $index => $item) :
        $num = $index + 1;
        ?>
        <div class="col-12 col-md-6 col-lg-3">
          <article class="usp-card h-100">
            <div class="usp-card-inner">
              <span class="usp-num" aria-hidden="true"><?php echo (int) $num; ?></span>
              <h3 class="usp-title h5 fw-bold"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p class="usp-text mb-0"><?php echo htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
