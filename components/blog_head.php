<?php
/**
 * Общий <head> для блог-статей.
 * Переменные: $pageTitle, $pageDescription, $pageKeywords, $canonicalUrl
 */
$pageTitle       = isset($pageTitle)       ? $pageTitle       : 'Блог ВГМК';
$pageDescription = isset($pageDescription) ? $pageDescription : 'Статьи о ритуальной продукции — венках, гробах и похоронных принадлежностях от ВГМК.';
$pageKeywords    = isset($pageKeywords)    ? $pageKeywords    : '';
$canonicalUrl    = isset($canonicalUrl)    ? $canonicalUrl    : '';
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if ($pageKeywords): ?>
  <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>
  <?php if ($canonicalUrl): ?>
  <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="/assets/css/site.css">
  <link rel="stylesheet" href="/assets/css/blog.css">
</head>
