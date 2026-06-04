<?php
/**
 * Подвал сайта: навигация, контакты, ИНН, кредиты.
 * Задайте $navBase = 'index.php' на внутренних страницах.
 */
$y = (int) date('Y');
$navBase = isset($navBase) ? (string) $navBase : '';
$h = static function (string $anchor) use ($navBase): string {
  return $navBase === '' ? '#' . $anchor : $navBase . '#' . $anchor;
};
$homeHref = $navBase === '' ? '#home' : $navBase . '#home';
?>
<footer class="site-footer mt-auto" id="contacts">
  <div class="container-site py-5 pb-4">
    <div class="row g-4 g-lg-5">
      <div class="col-lg-4">
        <div class="footer-brand d-flex align-items-center gap-2 mb-3">
          <span class="logo-vgmk logo-vgmk--footer" aria-hidden="true">
            <span class="logo-top">ВГ</span>
            <span class="logo-bottom">МК</span>
          </span>
          <span class="footer-brand-name">Военно-гражданская мемориальная кампания</span>
        </div>
        <p class="footer-lead small mb-0">
          Ритуальные венки и корзины оптом. Доставка по&nbsp;РФ.
        </p>
      </div>

      <div class="col-md-6 col-lg-4">
        <h2 class="footer-heading h6 text-uppercase mb-3 letter-spacing">Разделы</h2>
        <nav class="footer-nav-wrap" aria-label="Навигация в подвале сайта">
          <ul class="list-unstyled footer-nav mb-0">
            <li><a href="<?php echo htmlspecialchars($homeHref, ENT_QUOTES, 'UTF-8'); ?>">Главная</a></li>
            <li><a href="venki.php">Венки</a></li>
            <li><a href="groby.php">Гробы</a></li>
            <li><a href="proizvodstvo.php">Производство</a></li>
            <li><a href="<?php echo htmlspecialchars($h('delivery'), ENT_QUOTES, 'UTF-8'); ?>">Доставка</a></li>
            <li><a href="blog.php">Блог</a></li>
            <li><a href="<?php echo htmlspecialchars($h('contacts'), ENT_QUOTES, 'UTF-8'); ?>">Контакты</a></li>
          </ul>
        </nav>
      </div>

      <div class="col-md-6 col-lg-4">
        <h2 class="footer-heading h6 text-uppercase mb-3 letter-spacing">Контакты</h2>
       
        <ul class="list-unstyled footer-contacts mb-0">
          <li class="d-flex align-items-start gap-2 mb-2">
            <svg class="footer-contact-icon flex-shrink-0 mt-1" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill="currentColor"/>
            </svg>
            <span>
              <a class="footer-tel" href="tel:+798808888805">+7 (988) 088-88-05</a>
              <span class="footer-contact-name">Сергей</span>
            </span>
          </li>
          <li class="d-flex align-items-start gap-2">
            <svg class="footer-contact-icon flex-shrink-0 mt-1" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill="currentColor"/>
            </svg>
            <span>
              <a class="footer-tel" href="tel:+79898198600">+7 (989) 819-86-00</a>
              <span class="footer-contact-name">Иван</span>
            </span>
          </li>
        </ul>
      </div>

      <div class="col-12 footer-address-wrap">
        <div class="footer-address-line">
          <span class="footer-address-line__rule" aria-hidden="true"></span>
          <p class="footer-address mb-0">Краснодарский край ст.Северская ул Западная 41Б</p>
          <span class="footer-address-line__rule" aria-hidden="true"></span>
        </div>
      </div>
    </div>

    <hr class="footer-rule footer-rule--after-address mt-0 mt-md-4 mb-4">

    <div class="footer-bottom row row-cols-1 row-cols-lg-3 align-items-center gy-3">
    
      <div class="col">
        <p class="footer-copy small mb-0">
          © <?php echo $y; ?> ВГМК. Все права защищены.
        </p>
      </div>
      <div class="col text-lg-center">
        <p class="footer-inn small mb-0">
          ИНН&nbsp;234704972371
        </p>
      </div>
      <div class="col text-lg-end">
        <p class="footer-credits small mb-0">
          Разработано в
          <a class="footer-credits-link" href="https://vyatka-project.ru" target="_blank" rel="noopener noreferrer">vyatka-project.ru</a>
        </p>
      </div>
    </div>
  </div>
</footer>
<?php include __DIR__ . '/metrika.php'; ?>
