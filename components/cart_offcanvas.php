<?php
declare(strict_types=1);
?>
<div class="offcanvas offcanvas-end cart-offcanvas" tabindex="-1" id="venkiCartOffcanvas" aria-labelledby="venkiCartOffcanvasLabel">
  <div class="offcanvas-header border-bottom">
    <h2 class="offcanvas-title h5 mb-0" id="venkiCartOffcanvasLabel">Корзина</h2>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <div id="cart-empty-state" class="text-muted small py-4 text-center">
      Корзина пуста. Добавьте товары из каталогов «Венки» или «Гробы».
    </div>
    <div id="cart-lines-wrap" class="flex-grow-1 d-none">
      <ul id="cart-lines" class="list-unstyled cart-lines mb-0"></ul>
    </div>

    <div id="cart-checkout-block" class="mt-auto pt-3 border-top d-none">
      <p class="small fw-semibold mb-2">Оформить заявку</p>
      <form id="cart-checkout-form" class="cart-checkout-form" novalidate>
        <input type="text" name="website" id="cart-honeypot" class="position-absolute opacity-0" style="left:-9999px;width:1px;height:1px;" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="mb-2">
          <label class="form-label small mb-0" for="cart-lead-name">Имя</label>
          <input type="text" class="form-control form-control-sm" id="cart-lead-name" name="name" required maxlength="120" autocomplete="name">
        </div>
        <div class="mb-2">
          <label class="form-label small mb-0" for="cart-lead-region">Регион доставки</label>
          <input type="text" class="form-control form-control-sm" id="cart-lead-region" name="region" required maxlength="200" autocomplete="address-level1">
        </div>
        <div class="mb-3">
          <label class="form-label small mb-0" for="cart-lead-phone">Телефон</label>
          <input type="tel" class="form-control form-control-sm" id="cart-lead-phone" name="phone" required placeholder="+7 (___) ___-__-__" autocomplete="tel">
        </div>
        <p id="cart-form-feedback" class="small mb-2 min-h-feedback" role="status"></p>
        <button type="submit" class="btn btn-success w-100" id="cart-submit-btn">Отправить заявку</button>
      </form>
    </div>
  </div>
</div>
