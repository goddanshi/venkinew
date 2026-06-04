<?php
$navBase = 'index.php';
$pageTitle       = 'Блог ВГМК — статьи о ритуальной продукции';
$pageDescription = 'Полезные статьи о венках, гробах и похоронных принадлежностях от производителя ВГМК. Советы по выбору, доставка по Краснодарскому краю и всей России.';
$pageKeywords    = 'венки вгмк, венки краснодар, гробы краснодар, ритуальная продукция';
$canonicalUrl    = 'https://venkivgmk.ru/blog.php';
?>
<!DOCTYPE html>
<html lang="ru">
<?php include __DIR__ . '/components/blog_head.php'; ?>
<body class="bg-light d-flex flex-column min-vh-100">
  <?php include __DIR__ . '/components/header.php'; ?>

  <main class="flex-grow-1">
    <section class="blog-hero">
      <div class="container-site">
        <p class="blog-hero__subtitle mb-2" style="opacity:.7;font-size:.85rem;">
          <a href="index.php" style="color:#fff;text-decoration:none;">Главная</a> &rsaquo; Блог
        </p>
        <h1 class="blog-hero__title">Блог ВГМК</h1>
        <p class="blog-hero__subtitle">Статьи о ритуальной продукции: венки, гробы, доставка по&nbsp;России</p>
      </div>
    </section>

    <section class="py-5">
      <div class="container-site">
        <div class="row g-4">

          <div class="col-md-6 col-lg-4">
            <article class="blog-card">
              <div class="blog-card__body">
                <span class="blog-card__badge">Венки</span>
                <h2 class="blog-card__title">
                  <a href="blog/venki-vgmk.php" class="stretched-link text-decoration-none text-dark">
                    Венки ВГМК: покупка ритуальных венков от производителя
                  </a>
                </h2>
                <p class="blog-card__excerpt">
                  ВГМК — собственное производство ритуальных венков в Краснодарском крае. Рассказываем, почему покупать напрямую у производителя выгоднее и как оформить заказ.
                </p>
              </div>
              <div class="blog-card__footer">
                <span>Июнь 2025</span>
                <a href="blog/venki-vgmk.php" class="blog-card__link">Читать &rarr;</a>
              </div>
            </article>
          </div>

          <div class="col-md-6 col-lg-4">
            <article class="blog-card">
              <div class="blog-card__body">
                <span class="blog-card__badge">Краснодар</span>
                <h2 class="blog-card__title">
                  <a href="blog/venki-krasnodar.php" class="stretched-link text-decoration-none text-dark">
                    Венки в Краснодаре: где купить и как заказать доставку
                  </a>
                </h2>
                <p class="blog-card__excerpt">
                  Где в Краснодаре купить качественные ритуальные венки оптом и в розницу? Обзор вариантов и доставка от ВГМК по всему Краснодарскому краю.
                </p>
              </div>
              <div class="blog-card__footer">
                <span>Июнь 2025</span>
                <a href="blog/venki-krasnodar.php" class="blog-card__link">Читать &rarr;</a>
              </div>
            </article>
          </div>

          <div class="col-md-6 col-lg-4">
            <article class="blog-card">
              <div class="blog-card__body">
                <span class="blog-card__badge">Гробы</span>
                <h2 class="blog-card__title">
                  <a href="blog/groby-krasnodar.php" class="stretched-link text-decoration-none text-dark">
                    Гробы в Краснодаре: выбор, цены и доставка от производителя
                  </a>
                </h2>
                <p class="blog-card__excerpt">
                  Полный обзор ассортимента гробов от ВГМК. Как выбрать подходящую модель, из чего складывается цена и как быстро оформить доставку в Краснодаре.
                </p>
              </div>
              <div class="blog-card__footer">
                <span>Июнь 2025</span>
                <a href="blog/groby-krasnodar.php" class="blog-card__link">Читать &rarr;</a>
              </div>
            </article>
          </div>

        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>
  <script src="assets/js/cart.js"></script>
</body>
</html>
