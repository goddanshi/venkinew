<?php
/**
 * Извлекает цены из «прайс венки.pdf» и записывает поле price в catalog_venki.json.
 * Запуск: php scripts/apply_prices_from_pdf.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$pdfGlob = $root . '/*.pdf';
$pdfs = glob($pdfGlob);
if ($pdfs === false || $pdfs === []) {
    fwrite(STDERR, "PDF not found in {$root}\n");
    exit(1);
}
$pdfPath = $pdfs[0];
$catalogPath = $root . '/data/catalog_venki.json';

$tmpTxt = sys_get_temp_dir() . '/venki_price_' . getmypid() . '.txt';
$cmd = 'pdftotext ' . escapeshellarg($pdfPath) . ' ' . escapeshellarg($tmpTxt) . ' 2>&1';
exec($cmd, $out, $code);
if ($code !== 0 || !is_readable($tmpTxt)) {
    fwrite(STDERR, "pdftotext failed: " . implode("\n", $out) . "\n");
    exit(1);
}

$text = file_get_contents($tmpTxt);
@unlink($tmpTxt);

/** @return array<string, int> key = "categoryId|CODE" */
function venki_parse_pdf_prices(string $text): array
{
    $prices = [];
    $lines = preg_split('/\R/u', $text) ?: [];
    $pendingCode = null;
    $pendingCategory = null;

    $normalizeCode = static function (string $raw): string {
        $map = [
            'А' => 'A', 'В' => 'B', 'С' => 'C', 'Д' => 'D',
            'а' => 'A', 'в' => 'B', 'с' => 'C', 'д' => 'D',
            'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D',
            'M' => 'M',
        ];
        return strtr($raw, $map);
    };

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line === 'НАЗВАНИЕ' || $line === 'ЦЕНА за 1шт' || $line === 'ИЗОБРАЖЕНИЕ') {
            continue;
        }

        if (preg_match('/^(?:\d+\.\s+)?Венок\s+([A-ZА-Я]\d+)\s+(\d+)см/u', $line, $m)) {
            $code = $normalizeCode($m[1]);
            $size = (int) $m[2];
            $pendingCategory = match ($size) {
                100 => 'cm100',
                120 => 'cm120',
                150 => 'cm150',
                default => null,
            };
            $pendingCode = $code;
            continue;
        }

        if (preg_match('/^(?:\d+\.\s+)?Корзина\s+([A-ZА-Я]\d+)/u', $line, $m)) {
            $pendingCategory = 'baskets';
            $pendingCode = $normalizeCode($m[1]);
            continue;
        }

        if ($pendingCode !== null && $pendingCategory !== null
            && preg_match('/^([\d\s]+),(\d{2})\s*₽/u', $line, $m)) {
            $amount = (int) preg_replace('/\s+/', '', $m[1]);
            $key = $pendingCategory . '|' . $pendingCode;
            // В PDF дважды «B61»: первый пункт — опечатка, это B60
            if ($key === 'cm120|B61' && !isset($prices['cm120|B60'])) {
                $prices['cm120|B60'] = $amount;
            } elseif ($key === 'cm120|B61') {
                $prices['cm120|B61'] = $amount;
            } else {
                $prices[$key] = $amount;
            }
            $pendingCode = null;
            $pendingCategory = null;
        }
    }

    return $prices;
}

/** @return string|null */
function venki_item_code(string $title): ?string
{
    if (!preg_match('/^([A-ZА-Я]\d+)/u', trim($title), $m)) {
        return null;
    }
    $map = [
        'А' => 'A', 'В' => 'B', 'С' => 'C', 'Д' => 'D',
        'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'M' => 'M',
    ];
    return strtr($m[1], $map);
}

$pdfPrices = venki_parse_pdf_prices($text);
$catalog = json_decode((string) file_get_contents($catalogPath), true);
if (!is_array($catalog) || !isset($catalog['categories']) || !is_array($catalog['categories'])) {
    fwrite(STDERR, "Invalid catalog JSON\n");
    exit(1);
}

$matched = 0;
$missing = [];

foreach ($catalog['categories'] as &$category) {
    $catId = (string) ($category['id'] ?? '');
    if (!isset($category['items']) || !is_array($category['items'])) {
        continue;
    }
    foreach ($category['items'] as &$item) {
        $code = venki_item_code((string) ($item['title'] ?? ''));
        if ($code === null) {
            continue;
        }
        $key = $catId . '|' . $code;
        if (isset($pdfPrices[$key])) {
            $item['price'] = $pdfPrices[$key];
            ++$matched;
        } else {
            $missing[] = $catId . ' / ' . ($item['title'] ?? '');
        }
    }
    unset($item);
}
unset($category);

$json = json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
if ($json === false) {
    fwrite(STDERR, "JSON encode failed\n");
    exit(1);
}
file_put_contents($catalogPath, $json . "\n");

echo "PDF: {$pdfPath}\n";
echo "Prices in PDF: " . count($pdfPrices) . "\n";
echo "Matched in catalog: {$matched}\n";
if ($missing !== []) {
    echo "No price in PDF (" . count($missing) . "):\n";
    foreach ($missing as $row) {
        echo "  - {$row}\n";
    }
}
