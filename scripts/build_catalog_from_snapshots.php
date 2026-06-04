<?php
/**
 * Парсинг сохранённых HTML venkioptom.ru (uc_post_grid_style_one_item).
 * Запуск: php scripts/build_catalog_from_snapshots.php
 *
 * Перед запуском скачайте страницы во временные файлы в корень проекта:
 * curl -sL 'URL' -o tmp_....html
 */
declare(strict_types=1);

$root = dirname(__DIR__);

$snapshots = [
    [
        'file' => $root . '/tmp_venkioptom.ru-katalog-venki-optom-100.html',
        'category_id' => 'cm100',
        'label' => '100 см',
    ],
    [
        'file' => $root . '/tmp_venkioptom.ru-katalog-venki-optom-120.html',
        'category_id' => 'cm120',
        'label' => '120 см',
    ],
    [
        'file' => $root . '/tmp_venkioptom.ru-katalog-venki-optom-150.html',
        'category_id' => 'cm150',
        'label' => '150 см',
    ],
    [
        'file' => $root . '/tmp_venkioptom.ru-katalog-korziny-optom.html',
        'category_id' => 'baskets',
        'label' => 'Корзины',
    ],
];

function venki_slug_item_id(string $categoryId, string $title, string $imageUrl): string
{
    $safeCat = preg_replace('/[^a-z0-9_-]/', '', $categoryId);
    $hash = substr(md5($categoryId . '|' . $title . '|' . $imageUrl), 0, 12);

    return $safeCat . '-' . $hash;
}

/**
 * @return list<array{title: string, image: string}>
 */
function venki_parse_grid_html(string $html): array
{
    $items = [];
    $chunks = preg_split('/<div class="uc_post_grid_style_one_item"/', $html);
    if ($chunks === false) {
        return [];
    }
    array_shift($chunks);

    foreach ($chunks as $chunk) {
        if (!preg_match('/data-bg="([^"]+)"/', $chunk, $bg)) {
            continue;
        }
        $img = html_entity_decode($bg[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($img === '') {
            continue;
        }
        if (!preg_match('/<div class="uc_title">(.*?)<\/div>/s', $chunk, $tit)) {
            continue;
        }
        $title = trim(html_entity_decode(strip_tags($tit[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $title = preg_replace('/\s+/u', ' ', $title);
        if ($title === '') {
            continue;
        }
        if (preg_match('/<div class="uc_title">.*?<\/div>\s*<div[^>]*>\s*([^<]+?)\s*<\/div>/s', $chunk, $note)) {
            $extra = trim(html_entity_decode($note[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($extra !== '' && $extra !== $title) {
                $title .= ' — ' . $extra;
            }
        }
        $items[] = ['title' => $title, 'image' => $img];
    }

    return $items;
}

$categories = [];

foreach ($snapshots as $snap) {
    if (!is_readable($snap['file'])) {
        fwrite(STDERR, "Нет файла: {$snap['file']}\n");
        exit(1);
    }
    $html = file_get_contents($snap['file']);
    if ($html === false) {
        fwrite(STDERR, "Не удалось прочитать {$snap['file']}\n");
        exit(1);
    }
    $parsed = venki_parse_grid_html($html);
    $rows = [];
    foreach ($parsed as $row) {
        $rows[] = [
            'id' => venki_slug_item_id($snap['category_id'], $row['title'], $row['image']),
            'title' => $row['title'],
            'image' => $row['image'],
        ];
    }
    $categories[] = [
        'id' => $snap['category_id'],
        'label' => $snap['label'],
        'items' => $rows,
    ];
}

$out = ['categories' => $categories];
$json = json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
if ($json === false) {
    fwrite(STDERR, "json_encode failed\n");
    exit(1);
}

$target = $root . '/data/catalog_venki.json';
if (file_put_contents($target, $json . "\n") === false) {
    fwrite(STDERR, "Не удалось записать $target\n");
    exit(1);
}

echo "OK: " . $target . " (" . strlen($json) . " bytes)\n";
foreach ($categories as $c) {
    echo "  - {$c['label']}: " . count($c['items']) . " позиций\n";
}
