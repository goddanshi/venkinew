<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/admin_auth.php';
require_once dirname(__DIR__) . '/lib/store.php';

venki_admin_session_start();

$errors = [
  'lead_save' => 'Не удалось сохранить список заявок.',
  'cat_empty' => 'Заполните поля категории.',
  'cat_exists' => 'Категория с таким ID уже есть.',
  'cat_save' => 'Не удалось сохранить каталог.',
  'label_empty' => 'Укажите название вкладки.',
  'item_empty' => 'Заполните поля товара.',
  'not_found' => 'Запись не найдена.',
  'item_not_found' => 'Товар не найден.',
  'unknown_action' => 'Неизвестное действие.',
  'upload' => 'Ошибка загрузки файла.',
  'csrf' => 'Ошибка безопасности формы.',
  'bad_catalog' => 'Некорректный каталог.',
];

$flash = isset($_GET['e']) && isset($errors[$_GET['e']]) ? $errors[$_GET['e']] : '';

if (venki_admin_logged_in()) {
  $csrf = venki_admin_csrf_token();
  $leads = venki_load_leads();
  usort($leads, static function ($a, $b) {
    $ta = is_array($a) ? strtotime((string) ($a['created_at'] ?? '')) : 0;
    $tb = is_array($b) ? strtotime((string) ($b['created_at'] ?? '')) : 0;
    return $tb <=> $ta;
  });
  $venkiData = venki_load_catalog_slug('venki');
  $categoriesVenki = $venkiData['categories'] ?? [];
  if (!is_array($categoriesVenki)) {
    $categoriesVenki = [];
  }
  $grobyData = venki_load_catalog_slug('groby');
  $categoriesGroby = $grobyData['categories'] ?? [];
  if (!is_array($categoriesGroby)) {
    $categoriesGroby = [];
  }

  header('Content-Type: text/html; charset=utf-8');
  ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Админка — ВГМК</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>
    body { background: #f1f3ef; }
    .admin-nav { background: #1a5f3c; }
    .admin-nav .nav-link { color: rgba(255,255,255,.85); }
    .admin-nav .nav-link.active { color: #fff; font-weight: 600; }
    .card { border: none; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
    .cat-block { border-left: 3px solid #1a5f3c; padding-left: 1rem; margin-bottom: 2rem; }
    .admin-thumb { width: 56px; height: 56px; object-fit: contain; background: #f0f2ee; border-radius: .35rem; }
  </style>
</head>
<body>
  <nav class="admin-nav navbar navbar-dark mb-4">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1 fs-5">ВГМК — админка</span>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Выйти</a>
    </div>
  </nav>

  <div class="container pb-5">
    <?php if ($flash !== '') : ?>
      <div class="alert alert-warning"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <ul class="nav nav-pills mb-4 gap-2 flex-wrap" id="adminTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="leads-tab" data-bs-toggle="pill" data-bs-target="#leads-pane" type="button">Заявки</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="cat-venki-tab" data-bs-toggle="pill" data-bs-target="#cat-venki-pane" type="button">Каталог: Венки</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="cat-groby-tab" data-bs-toggle="pill" data-bs-target="#cat-groby-pane" type="button">Каталог: Гробы</button>
      </li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane fade show active" id="leads-pane" role="tabpanel">
        <div class="card">
          <div class="card-body">
            <h2 class="h5 mb-3">Заявки на прайс</h2>
            <?php if ($leads === []) : ?>
              <p class="text-muted mb-0">Пока нет заявок.</p>
            <?php else : ?>
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead>
                    <tr>
                      <th>Дата</th>
                      <th>Тип</th>
                      <th>Имя</th>
                      <th>Регион</th>
                      <th>Телефон</th>
                      <th>Состав</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($leads as $lead) :
                      if (!is_array($lead)) {
                        continue;
                      }
                      $lid = htmlspecialchars((string) ($lead['id'] ?? ''), ENT_QUOTES, 'UTF-8');
                      $dt = htmlspecialchars((string) ($lead['created_at'] ?? ''), ENT_QUOTES, 'UTF-8');
                      $src = (string) ($lead['source'] ?? '');
                      $isCart = $src === 'cart' && !empty($lead['cart']) && is_array($lead['cart']);
                      $typeLabel = $isCart ? 'Корзина' : 'Прайс';
                      ?>
                      <tr>
                        <td class="text-nowrap small"><?php echo $dt; ?></td>
                        <td><span class="badge <?php echo $isCart ? 'bg-success' : 'bg-secondary'; ?>"><?php echo htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo htmlspecialchars((string) ($lead['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($lead['region'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($lead['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="small" style="max-width: 280px;">
                          <?php if ($isCart) : ?>
                            <ul class="mb-0 ps-3 admin-lead-cart">
                              <?php foreach ($lead['cart'] as $row) :
                                if (!is_array($row)) {
                                  continue;
                                }
                                $cn = venki_catalog_label((string) ($row['catalog'] ?? ''));
                                $tit = (string) ($row['title'] ?? '');
                                $qty = (int) ($row['qty'] ?? 0);
                                $tab = trim((string) ($row['category_label'] ?? ''));
                                ?>
                                <li><?php echo htmlspecialchars($tit, ENT_QUOTES, 'UTF-8'); ?> × <?php echo $qty; ?>
                                  <span class="text-muted">(<?php echo htmlspecialchars($cn, ENT_QUOTES, 'UTF-8'); ?><?php echo $tab !== '' ? ', ' . htmlspecialchars($tab, ENT_QUOTES, 'UTF-8') : ''; ?>)</span>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php else : ?>
                            <span class="text-muted">—</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <form method="post" action="actions/post.php" class="d-inline" onsubmit="return confirm('Удалить заявку?');">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="delete_lead">
                            <input type="hidden" name="id" value="<?php echo $lid; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="cat-venki-pane" role="tabpanel">
        <?php
        $catalogSlug = 'venki';
        $categories = $categoriesVenki;
        $catalogHumanTitle = 'Венки';
        include __DIR__ . '/partials/catalog_manage.php';
        ?>
      </div>

      <div class="tab-pane fade" id="cat-groby-pane" role="tabpanel">
        <?php
        $catalogSlug = 'groby';
        $categories = $categoriesGroby;
        $catalogHumanTitle = 'Гробы';
        include __DIR__ . '/partials/catalog_manage.php';
        ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
  <?php
  exit;
}

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
  if (venki_admin_try_login((string) $_POST['password'])) {
    header('Location: index.php', true, 302);
    exit;
  }
  $loginError = 'Неверный пароль';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Вход — админка ВГМК</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h1 class="h4 mb-4 text-center">Вход в админку</h1>
            <?php if ($loginError !== '') : ?>
              <div class="alert alert-danger py-2"><?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post" action="">
              <div class="mb-3">
                <label class="form-label" for="wp">Пароль</label>
                <input type="password" class="form-control" id="wp" name="password" required autofocus>
              </div>
              <button type="submit" class="btn btn-success w-100">Войти</button>
            </form>
            <p class="small text-muted mt-3 mb-0 text-center">Пароль по умолчанию: <code>admin</code> — смените через <code>config/admin.local.php</code></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
