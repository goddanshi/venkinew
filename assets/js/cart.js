(function () {
  var STORAGE_KEY = 'venki_cart_v1';
  var BOT_TOKEN = '8689270684:AAEi0oR3UFy3mCTzdbIWOIjOHk7SwQC-GTc';
  var CHAT_ID = '6449169961';

  function apiUrl(path) {
    try {
      return new URL(path, window.location.href).href;
    } catch (e) {
      return path;
    }
  }

  function telegramApiUrl(method) {
    return 'https://api.telegram.org/bot' + BOT_TOKEN + '/' + method;
  }

  function lineKey(item) {
    return (
      item.catalog +
      '|' +
      item.category_id +
      '|' +
      item.item_id
    );
  }

  function loadCart() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return [];
      var data = JSON.parse(raw);
      return Array.isArray(data) ? data : [];
    } catch (e) {
      return [];
    }
  }

  function saveCart(items) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    updateBadge();
    renderCart();
  }

  function totalQty(items) {
    return items.reduce(function (s, it) {
      return s + (it.qty || 0);
    }, 0);
  }

  function updateBadge() {
    var n = totalQty(loadCart());
    document.querySelectorAll('[data-cart-badge]').forEach(function (el) {
      el.textContent = n > 99 ? '99+' : String(n);
      el.classList.toggle('d-none', n === 0);
    });
  }

  function renderCart() {
    var items = loadCart();
    var emptyEl = document.getElementById('cart-empty-state');
    var wrapEl = document.getElementById('cart-lines-wrap');
    var listEl = document.getElementById('cart-lines');
    var checkoutEl = document.getElementById('cart-checkout-block');

    if (!listEl || !emptyEl || !wrapEl || !checkoutEl) return;

    if (items.length === 0) {
      emptyEl.classList.remove('d-none');
      wrapEl.classList.add('d-none');
      checkoutEl.classList.add('d-none');
      listEl.innerHTML = '';
      return;
    }

    emptyEl.classList.add('d-none');
    wrapEl.classList.remove('d-none');
    checkoutEl.classList.remove('d-none');

    listEl.innerHTML = '';
    items.forEach(function (it, idx) {
      var li = document.createElement('li');
      li.className = 'cart-line card border-0 shadow-sm mb-2';
      var catHuman = it.catalog_human || it.catalog;
      var catPart =
        '<span class="text-muted">' +
        escapeHtml(catHuman) +
        '</span>';
      if (it.category_label) {
        catPart +=
          ' · <span class="text-muted small">' +
          escapeHtml(it.category_label) +
          '</span>';
      }
      li.innerHTML =
        '<div class="card-body p-2 p-sm-3">' +
        '<div class="d-flex gap-2">' +
        (it.image
          ? '<img class="cart-line-thumb flex-shrink-0 rounded" src="' +
            escapeAttr(it.image) +
            '" width="56" height="56" alt="">'
          : '<div class="cart-line-thumb-placeholder flex-shrink-0 rounded bg-light text-muted small d-flex align-items-center justify-content-center">нет</div>') +
        '<div class="flex-grow-1 min-w-0">' +
        '<div class="small fw-semibold text-truncate">' +
        escapeHtml(it.title || '') +
        '</div>' +
        '<div class="small">' +
        catPart +
        '</div>' +
        '<div class="d-flex align-items-center gap-2 mt-2">' +
        '<div class="btn-group btn-group-sm" role="group" aria-label="Количество">' +
        '<button type="button" class="btn btn-outline-secondary cart-qty-minus" data-idx="' +
        idx +
        '">−</button>' +
        '<span class="btn btn-outline-secondary disabled px-2">' +
        it.qty +
        '</span>' +
        '<button type="button" class="btn btn-outline-secondary cart-qty-plus" data-idx="' +
        idx +
        '">+</button>' +
        '</div>' +
        '<button type="button" class="btn btn-link btn-sm text-danger text-decoration-none ms-auto cart-remove" data-idx="' +
        idx +
        '">Удалить</button>' +
        '</div></div></div></div>';
      listEl.appendChild(li);
    });

    listEl.querySelectorAll('.cart-qty-minus').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var i = parseInt(btn.getAttribute('data-idx'), 10);
        changeQty(i, -1);
      });
    });
    listEl.querySelectorAll('.cart-qty-plus').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var i = parseInt(btn.getAttribute('data-idx'), 10);
        changeQty(i, 1);
      });
    });
    listEl.querySelectorAll('.cart-remove').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var i = parseInt(btn.getAttribute('data-idx'), 10);
        removeAt(i);
      });
    });
  }

  function escapeHtml(s) {
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  function escapeAttr(s) {
    return escapeHtml(s).replace(/"/g, '&quot;');
  }

  function changeQty(index, delta) {
    var items = loadCart();
    if (!items[index]) return;
    items[index].qty = Math.min(
      999,
      Math.max(1, (items[index].qty || 1) + delta),
    );
    saveCart(items);
  }

  function removeAt(index) {
    var items = loadCart();
    items.splice(index, 1);
    saveCart(items);
  }

  function addToCart(payload) {
    var items = loadCart();
    var key = lineKey(payload);
    var found = false;
    for (var i = 0; i < items.length; i++) {
      if (lineKey(items[i]) === key) {
        items[i].qty = Math.min(
          999,
          (items[i].qty || 1) + (payload.qty || 1),
        );
        found = true;
        break;
      }
    }
    if (!found) {
      items.push({
        catalog: payload.catalog,
        category_id: payload.category_id,
        item_id: payload.item_id,
        title: payload.title,
        category_label: payload.category_label || '',
        catalog_human: payload.catalog_human || '',
        image: payload.image || '',
        qty: Math.min(999, Math.max(1, payload.qty || 1)),
      });
    }
    saveCart(items);
  }

  function bindCatalogButtons() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.btn-add-to-cart');
      if (!btn) return;
      e.preventDefault();
      var catalog = btn.getAttribute('data-catalog') || '';
      var categoryId = btn.getAttribute('data-category-id') || '';
      var itemId = btn.getAttribute('data-item-id') || '';
      var title = btn.getAttribute('data-title') || '';
      var categoryLabel = btn.getAttribute('data-category-label') || '';
      var catalogHuman = btn.getAttribute('data-catalog-human') || '';
      var image = btn.getAttribute('data-image') || '';
      if (!catalog || !categoryId || !itemId || !title) return;
      addToCart({
        catalog: catalog,
        category_id: categoryId,
        item_id: itemId,
        title: title,
        category_label: categoryLabel,
        catalog_human: catalogHuman,
        image: image,
        qty: 1,
      });
      updateBadge();
      var oc = document.getElementById('venkiCartOffcanvas');
      if (oc && typeof bootstrap !== 'undefined') {
        var inst = bootstrap.Offcanvas.getOrCreateInstance(oc);
        inst.show();
      }
    });
  }

  function setFeedback(el, type, message) {
    if (!el) return;
    el.textContent = message || '';
    el.classList.remove('text-success', 'text-danger', 'text-muted');
    if (type === 'success') el.classList.add('text-success');
    else if (type === 'error') el.classList.add('text-danger');
    else el.classList.add('text-muted');
  }

  function notifyTelegramCart(name, region, phone, items) {
    var lines = items
      .map(function (it) {
        var cn =
          it.catalog_human === 'Венки' || it.catalog_human === 'Гробы'
            ? it.catalog_human
            : it.catalog;
        return (
          '• ' +
          (it.title || '') +
          ' × ' +
          it.qty +
          ' (' +
          cn +
          (it.category_label ? ', ' + it.category_label : '') +
          ')'
        );
      })
      .join('\n');
    var text =
      'Заявка из корзины\n\n' +
      'Имя: ' +
      name +
      '\nРегион: ' +
      region +
      '\nТелефон: ' +
      phone +
      '\n\nТовары:\n' +
      lines;
    fetch(telegramApiUrl('sendMessage'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json; charset=utf-8' },
      body: JSON.stringify({
        chat_id: CHAT_ID,
        text: text,
        disable_web_page_preview: true,
      }),
    }).catch(function () {});
  }

  function bindCheckoutForm() {
    var form = document.getElementById('cart-checkout-form');
    if (!form) return;

    var phoneInput = document.getElementById('cart-lead-phone');
    var feedbackEl = document.getElementById('cart-form-feedback');
    var submitBtn = document.getElementById('cart-submit-btn');
    var phoneMask = null;

    if (phoneInput && typeof IMask !== 'undefined') {
      phoneMask = IMask(phoneInput, {
        mask: '+{7} (000) 000-00-00',
        lazy: false,
      });
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      setFeedback(feedbackEl, '', '');

      var items = loadCart();
      if (items.length === 0) {
        setFeedback(feedbackEl, 'error', 'Корзина пуста.');
        return;
      }

      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
      }

      var name = (document.getElementById('cart-lead-name') || {}).value || '';
      var region =
        (document.getElementById('cart-lead-region') || {}).value || '';
      var phone = (phoneInput || {}).value || '';
      var hp = (document.getElementById('cart-honeypot') || {}).value || '';

      if (hp !== '') {
        setFeedback(feedbackEl, 'success', 'Заявка отправлена. Мы свяжемся с вами.');
        form.reset();
        form.classList.remove('was-validated');
        if (phoneMask) phoneMask.value = '';
        else if (phoneInput) phoneInput.value = '';
        return;
      }

      var digits = phone.replace(/\D/g, '');
      if (digits.length !== 11 || digits[0] !== '7') {
        form.classList.add('was-validated');
        if (phoneInput) phoneInput.setCustomValidity('Введите полный номер');
        phoneInput && phoneInput.reportValidity();
        return;
      }
      if (phoneInput) phoneInput.setCustomValidity('');

      var payloadCart = items.map(function (it) {
        return {
          catalog: it.catalog,
          category_id: it.category_id,
          item_id: it.item_id,
          title: it.title,
          category_label: it.category_label || '',
          qty: it.qty,
        };
      });

      submitBtn.disabled = true;
      setFeedback(feedbackEl, 'muted', 'Отправка…');

      fetch(apiUrl('api/save-lead.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json; charset=utf-8' },
        body: JSON.stringify({
          name: name.trim(),
          region: region.trim(),
          phone: phone.trim(),
          website: '',
          cart: payloadCart,
        }),
        credentials: 'same-origin',
      })
        .then(function (res) {
          return res.json().catch(function () {
            return { ok: false, error: 'Ответ сервера не JSON' };
          });
        })
        .then(function (data) {
          if (!data || !data.ok) {
            var msg =
              (data && data.error) ||
              'Не удалось сохранить заявку. Попробуйте позже.';
            setFeedback(feedbackEl, 'error', msg);
            return;
          }
          setFeedback(
            feedbackEl,
            'success',
            'Заявка отправлена. Мы свяжемся с вами.',
          );
          saveCart([]);
          form.reset();
          form.classList.remove('was-validated');
          if (phoneMask) phoneMask.value = '';
          else if (phoneInput) phoneInput.value = '';
          notifyTelegramCart(
            name.trim(),
            region.trim(),
            phone.trim(),
            items,
          );
          var oc = document.getElementById('venkiCartOffcanvas');
          if (oc && typeof bootstrap !== 'undefined') {
            var oci = bootstrap.Offcanvas.getInstance(oc);
            if (oci) oci.hide();
          }
        })
        .catch(function () {
          setFeedback(
            feedbackEl,
            'error',
            'Ошибка сети. Проверьте подключение.',
          );
        })
        .finally(function () {
          submitBtn.disabled = false;
        });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    bindCatalogButtons();
    bindCheckoutForm();
    updateBadge();
    renderCart();

    var navCartBtn = document.getElementById('navCartBtn');
    var navEl = document.getElementById('mainNavbar');
    if (navCartBtn && navEl && typeof bootstrap !== 'undefined') {
      navCartBtn.addEventListener('click', function () {
        if (
          window.matchMedia('(max-width: 991.98px)').matches &&
          navEl.classList.contains('show')
        ) {
          var col = bootstrap.Collapse.getInstance(navEl);
          if (col) col.hide();
        }
      });
    }
  });
})();
