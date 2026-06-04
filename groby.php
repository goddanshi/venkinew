<?php
$navBase = 'index.php';
$catalogSlug = 'groby';
$catalogHeading = 'Гробы';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Гробы в Краснодаре — купить гроб от производителя ВГМК</title>
  <meta name="description" content="Купить гробы в Краснодаре от производителя ВГМК. Эконом, стандарт, VIP, военные. Оптовые цены, доставка день в день по Краснодару и Краснодарскому краю.">
  <meta name="keywords" content="гробы краснодар, купить гроб краснодар, гробы оптом краснодар, гроб от производителя">
  <link rel="canonical" href="https://venkivgmk.ru/groby.php">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/site.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
  <?php include __DIR__ . '/components/header.php'; ?>

  <main class="flex-grow-1">
    <?php include __DIR__ . '/components/catalog.php'; ?>
  </main>

  <?php include __DIR__ . '/components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>
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
