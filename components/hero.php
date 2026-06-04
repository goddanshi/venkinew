<?php
/**
 * Hero: главный экран — хаки-фон, текст слева, изображение справа.
 */
$heroImageUrl = 'assets/venki.png';
?>
<section id="home" class="hero-section">
  <div class="container-site">
    <div class="hero-panel">
      <div class="row align-items-center g-0">
        <div class="col-lg-6">
          <div class="hero-content px-4 px-sm-5 py-5 py-lg-5">
            <p class="hero-kicker mb-2 mb-md-3">венки и гробы оптом и в розницу</p>
            <h1 class="hero-title mb-3 mb-md-4">Ритуальные венки и гробы</h1>
            <p class="hero-lead mb-4 mb-md-5">
              Широкий ассортимент ритуальных венков и корзин. Доставка по РФ.
            </p>
            <div class="d-flex flex-wrap gap-2 gap-md-3">
              <a class="btn btn-hero-cta btn-lg" href="venki.php">Венки</a>
              <a class="btn btn-hero-outline btn-lg" href="groby.php">Гробы</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="hero-image-wrap px-3 px-lg-0 pb-4 pb-lg-0 pt-0 pt-lg-0">
            <img
              class="hero-image img-fluid"
              src="<?php echo htmlspecialchars($heroImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
              width="800"
              height="600"
              alt="Ритуальные венки"
              decoding="async"
              fetchpriority="high"
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
