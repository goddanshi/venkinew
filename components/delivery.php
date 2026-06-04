<?php
/**
 * Доставка: форма заявки на прайс.
 */
?>
<section id="delivery" class="delivery-section py-5 py-lg-6">
  <div class="container-site">
    <div class="delivery-card">
      <div class="delivery-card__head">
        <span class="delivery-card__badge">Прайс-лист</span>
        <h2 class="delivery-card__title">
          Оставьте заявку и получите актуальный прайс-лист
        </h2>
        <p class="delivery-card__lead">
          Заполните поля — отправим цены в удобный мессенджер или перезвоним.
        </p>
      </div>

      <form id="deliveryLeadForm" class="delivery-form needs-validation" novalidate>
        <input type="text" name="website" class="delivery-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

        <div class="row g-4 delivery-fields">
          <div class="col-md-4">
            <div class="delivery-field">
              <label for="lead-name" class="delivery-label">Имя</label>
              <input
                type="text"
                class="form-control delivery-control"
                id="lead-name"
                name="name"
                required
                maxlength="120"
                autocomplete="name"
                placeholder="Как к вам обращаться"
              >
              <div class="invalid-feedback">Укажите имя</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="delivery-field">
              <label for="lead-region" class="delivery-label">Регион</label>
              <input
                type="text"
                class="form-control delivery-control"
                id="lead-region"
                name="region"
                required
                maxlength="200"
                autocomplete="address-level1"
                placeholder="Область или город"
              >
              <div class="invalid-feedback">Укажите регион</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="delivery-field">
              <label for="lead-phone" class="delivery-label">Телефон</label>
              <input
                type="tel"
                class="form-control delivery-control"
                id="lead-phone"
                name="phone"
                required
                inputmode="numeric"
                autocomplete="tel"
                placeholder="+7 (___) ___-__-__"
              >
              <div class="invalid-feedback">Введите номер полностью</div>
            </div>
          </div>
        </div>

        <div class="delivery-actions">
          <button type="submit" class="btn delivery-btn-submit" id="delivery-submit-btn">
            <span class="delivery-btn-submit__text">Оставить заявку</span>
            <svg class="delivery-btn-submit__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <div id="delivery-form-feedback" class="delivery-feedback" role="status" aria-live="polite"></div>
        </div>
      </form>
    </div>
  </div>
</section>
