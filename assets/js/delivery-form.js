(function () {
  var form = document.getElementById('deliveryLeadForm');
  if (!form) return;

  var phoneInput = document.getElementById('lead-phone');
  var feedbackEl = document.getElementById('delivery-form-feedback');
  var submitBtn = document.getElementById('delivery-submit-btn');
  var phoneMask = null;

  var BOT_TOKEN = '8689270684:AAEi0oR3UFy3mCTzdbIWOIjOHk7SwQC-GTc';
  var CHAT_ID = '6449169961';

  if (phoneInput && typeof IMask !== 'undefined') {
    phoneMask = IMask(phoneInput, {
      mask: '+{7} (000) 000-00-00',
      lazy: false,
    });
  }

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

  function setFeedback(type, message) {
    if (!feedbackEl) return;
    feedbackEl.textContent = message || '';
    feedbackEl.classList.remove('text-success', 'text-danger', 'text-muted');
    if (type === 'success') feedbackEl.classList.add('text-success');
    else if (type === 'error') feedbackEl.classList.add('text-danger');
    else feedbackEl.classList.add('text-muted');
  }

  function resetPhoneField() {
    if (phoneMask) {
      phoneMask.value = '';
    } else if (phoneInput) {
      phoneInput.value = '';
    }
  }

  function notifyTelegram(name, region, phone) {
    var text =
      'Заявка на прайс (доставка)\n\n' +
      'Имя: ' +
      name +
      '\n' +
      'Регион: ' +
      region +
      '\n' +
      'Телефон: ' +
      phone;
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

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    setFeedback('', '');

    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }

    var name = (document.getElementById('lead-name') || {}).value || '';
    var region = (document.getElementById('lead-region') || {}).value || '';
    var phone = (phoneInput || {}).value || '';
    var honeypot = form.querySelector('[name="website"]');
    var hp = honeypot ? honeypot.value : '';

    if (hp !== '') {
      setFeedback('success', 'Заявка отправлена. Мы свяжемся с вами.');
      form.reset();
      form.classList.remove('was-validated');
      resetPhoneField();
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

    submitBtn.disabled = true;
    setFeedback('muted', 'Отправка…');

    var fd = new FormData(form);

    fetch(apiUrl('api/save-lead.php'), {
      method: 'POST',
      body: fd,
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
          setFeedback('error', msg);
          return;
        }
        setFeedback('success', 'Заявка отправлена. Мы свяжемся с вами.');
        form.reset();
        form.classList.remove('was-validated');
        resetPhoneField();
        notifyTelegram(name.trim(), region.trim(), phone.trim());
      })
      .catch(function () {
        setFeedback(
          'error',
          'Ошибка сети. Проверьте подключение или откройте сайт через PHP-сервер.',
        );
      })
      .finally(function () {
        submitBtn.disabled = false;
      });
  });
})();
