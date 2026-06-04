<?php
/**
 * Шапка сайта: лого ВГМК, навигация, контакты.
 * Задайте $navBase = 'index.php' на внутренних страницах (venki.php, groby.php, proizvodstvo.php); на главной не задавайте или ''.
 */
$navBase = isset($navBase) ? (string) $navBase : '';
$h = static function (string $anchor) use ($navBase): string {
  return $navBase === '' ? '#' . $anchor : $navBase . '#' . $anchor;
};
$homeHref = $navBase === '' ? '#home' : $navBase . '#home';
?>
<header class="site-header bg-white border-bottom shadow-sm sticky-top">
  <nav class="navbar navbar-expand-lg navbar-light py-2 py-md-3" aria-label="Основное меню">
    <div class="container-site">
      <a class="navbar-brand d-flex align-items-center gap-2 gap-md-3 me-auto me-lg-0" href="<?php echo htmlspecialchars($homeHref, ENT_QUOTES, 'UTF-8'); ?>" aria-label="На главную">
        <span class="logo-vgmk" aria-hidden="true">
          <span class="logo-top">ВГ</span>
          <span class="logo-bottom">МК</span>
        </span>
        <span class="d-none d-sm-inline text-brand mb-0">Военно-гражданская мемориальная кампания</span>
      </a>

      <button
        class="navbar-toggler border-0 shadow-none"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#mainNavbar"
        aria-controls="mainNavbar"
        aria-expanded="false"
        aria-label="Открыть меню"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav my-3 my-lg-0 text-center text-lg-start align-items-lg-center me-lg-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($homeHref, ENT_QUOTES, 'UTF-8'); ?>">Главная</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="venki.php">Венки</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="groby.php">Гробы</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="proizvodstvo.php">Производство</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($h('delivery'), ENT_QUOTES, 'UTF-8'); ?>">Доставка</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="blog.php">Блог</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($h('contacts'), ENT_QUOTES, 'UTF-8'); ?>">Контакты</a>
          </li>
        </ul>

        <div class="d-flex flex-column flex-lg-row align-items-center align-items-lg-center gap-2 gap-lg-3 ms-lg-auto mt-2 mt-lg-0 pt-3 pt-lg-0 border-top border-lg-0 border-secondary-subtle">
          <button
            type="button"
            class="btn btn-cart-icon position-relative p-2 order-first order-lg-0 flex-shrink-0"
            id="navCartBtn"
            data-bs-toggle="offcanvas"
            data-bs-target="#venkiCartOffcanvas"
            aria-controls="venkiCartOffcanvas"
            title="Корзина"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
              <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-nav-badge d-none" data-cart-badge="" aria-live="polite">0</span>
          </button>

        <div class="header-contacts justify-content-lg-end w-100 w-lg-auto">
          <div class="header-contact-row">
            <svg class="phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill="currentColor"/>
            </svg>
            <a href="tel:+798808888805">+7 (988) 088-88-05</a>
            <span class="contact-name">Сергей</span>
          </div>
          <div class="header-contact-row">
            <svg class="phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill="currentColor"/>
            </svg>
            <a href="tel:+79898198600">+7 (989) 819-86-00</a>
            <span class="contact-name">Иван</span>
          </div>
        </div>
        </div>
      </div>
    </div>
  </nav>
  <?php include __DIR__ . '/cart_offcanvas.php'; ?>
</header>
