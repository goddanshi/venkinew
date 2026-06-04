<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Метод не поддерживается'], JSON_UNESCAPED_UNICODE);
  exit;
}

require_once dirname(__DIR__) . '/lib/store.php';

$strLen = static function (string $s): int {
  return function_exists('mb_strlen') ? (int) mb_strlen($s, 'UTF-8') : strlen($s);
};

/**
 * @return list<array<string, mixed>>|null
 */
function venki_api_normalize_cart_lines(mixed $raw): ?array
{
  if ($raw === null) {
    return null;
  }
  if (is_string($raw)) {
    $raw = json_decode($raw, true);
  }
  if (!is_array($raw) || $raw === []) {
    return null;
  }
  $out = [];
  foreach ($raw as $row) {
    if (!is_array($row)) {
      return null;
    }
    $cat = isset($row['catalog']) ? trim((string) $row['catalog']) : '';
    if (!venki_catalog_slug_valid($cat)) {
      return null;
    }
    $cid = isset($row['category_id']) ? trim((string) $row['category_id']) : '';
    $iid = isset($row['item_id']) ? trim((string) $row['item_id']) : '';
    $title = isset($row['title']) ? trim((string) $row['title']) : '';
    $qty = isset($row['qty']) ? (int) $row['qty'] : 0;
    $catLabel = isset($row['category_label']) ? trim((string) $row['category_label']) : '';
    if ($cid === '' || $iid === '' || $title === '' || $qty < 1 || $qty > 999) {
      return null;
    }
    $len = static function (string $s): int {
      return function_exists('mb_strlen') ? (int) mb_strlen($s, 'UTF-8') : strlen($s);
    };
    if ($len($title) > 200 || $len($cid) > 80 || $len($iid) > 80 || $len($catLabel) > 120) {
      return null;
    }
    $out[] = [
      'catalog' => $cat,
      'category_id' => $cid,
      'item_id' => $iid,
      'title' => $title,
      'category_label' => $catLabel,
      'qty' => $qty,
    ];
  }

  return $out;
}

$data = null;
if (!empty($_POST)) {
  $data = $_POST;
}
if (!is_array($data)) {
  $raw = file_get_contents('php://input');
  if ($raw !== '' && $raw !== false) {
    $jsonFlags = defined('JSON_INVALID_UTF8_SUBSTITUTE') ? JSON_INVALID_UTF8_SUBSTITUTE : 0;
    $decoded = json_decode($raw, true, 512, $jsonFlags);
    if (is_array($decoded)) {
      $data = $decoded;
    } else {
      $ct = $_SERVER['CONTENT_TYPE'] ?? '';
      if (stripos($ct, 'application/x-www-form-urlencoded') !== false) {
        $parsed = [];
        parse_str($raw, $parsed);
        if (!empty($parsed)) {
          $data = $parsed;
        }
      }
    }
  }
}

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Некорректные данные'], JSON_UNESCAPED_UNICODE);
  exit;
}

$name = isset($data['name']) ? trim((string) $data['name']) : '';
$region = isset($data['region']) ? trim((string) $data['region']) : '';
$phoneRaw = isset($data['phone']) ? trim((string) $data['phone']) : '';
$honeypot = isset($data['website']) ? trim((string) $data['website']) : '';

if ($honeypot !== '') {
  echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($name === '' || $strLen($name) > 120) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Укажите имя'], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($region === '' || $strLen($region) > 200) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Укажите регион'], JSON_UNESCAPED_UNICODE);
  exit;
}

$digits = preg_replace('/\D/', '', $phoneRaw);
if (strlen($digits) !== 11 || $digits[0] !== '7') {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Укажите полный номер в формате +7'], JSON_UNESCAPED_UNICODE);
  exit;
}

$phoneFmt = '+7 (' . substr($digits, 1, 3) . ') ' . substr($digits, 4, 3) . '-' . substr($digits, 7, 2) . '-' . substr($digits, 9, 2);

$cartLines = null;
if (array_key_exists('cart', $data)) {
  $cartLines = venki_api_normalize_cart_lines($data['cart']);
  if ($cartLines === null) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Укажите корректный состав заказа или отправьте заявку без корзины'], JSON_UNESCAPED_UNICODE);
    exit;
  }
}

$lead = [
  'id' => bin2hex(random_bytes(8)),
  'created_at' => date('c'),
  'name' => $name,
  'region' => $region,
  'phone' => $phoneFmt,
  'source' => $cartLines !== null ? 'cart' : 'price',
];

if ($cartLines !== null) {
  $lead['cart'] = $cartLines;
}

if (!venki_append_lead($lead)) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Не удалось сохранить заявку'], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode(['ok' => true, 'id' => $lead['id']], JSON_UNESCAPED_UNICODE);
