<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ВГМК — ритуальные венки и гробы от производителя, Краснодарский край</title>
  <meta name="description" content="ВГМК — производитель ритуальных венков и гробов в Краснодарском крае. Оптовые цены, доставка по России. Венки ВГМК, венки и гробы в Краснодаре. Звоните: +7 (988) 088-88-05.">
  <meta name="keywords" content="венки вгмк, венки краснодар, гробы краснодар, ритуальные венки оптом, купить гроб краснодар">
  <link rel="canonical" href="https://venkivgmk.ru/">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/site.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
  <?php include __DIR__ . '/components/header.php'; ?>

  <main class="flex-grow-1">
    <?php include __DIR__ . '/components/hero.php'; ?>

    <?php include __DIR__ . '/components/usp.php'; ?>

    <?php include __DIR__ . '/components/production_teaser.php'; ?>

    <?php include __DIR__ . '/components/catalog_hub.php'; ?>
    <?php include __DIR__ . '/components/delivery.php'; ?>
  </main>

  <?php include __DIR__ . '/components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>
  <script src="assets/js/delivery-form.js"></script>
  <script src="assets/js/cart.js"></script>
  <script>
    (function () {
      var navEl = document.getElementById('mainNavbar');
      if (!navEl || typeof bootstrap === 'undefined') return;
      document.querySelectorAll('#mainNavbar .nav-link[href^="#"], #contacts.site-footer a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function () {
          if (window.matchMedia('(max-width: 991.98px)').matches && navEl.classList.contains('show')) {
            bootstrap.Collapse.getOrCreateInstance(navEl).hide();
          }
        });
      });
    })();
  </script>
</body>
</html>
