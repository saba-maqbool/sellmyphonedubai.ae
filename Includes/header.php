<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <?php require_once(__DIR__ . "/get-site-settings.php"); ?>

    <title><?php echo htmlspecialchars($meta_title ?? $site_settings['site_title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description ?? ''); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords ?? ''); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars(($meta_robots ?? 'index, follow') . ', max-snippet:-1, max-image-preview:large, max-video-preview:-1'); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url ?? ''); ?>">
    <link rel="icon" type="image/webp" href="<?php echo htmlspecialchars($site_settings['favicon'] ?? $site_settings['logo']); ?>">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($site_settings['site_title']); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($meta_title ?? $site_settings['site_title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_description ?? ''); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($meta_image ?? $site_settings['logo']); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url ?? ''); ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($meta_title ?? $site_settings['site_title']); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_description ?? ''); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($meta_image ?? $site_settings['logo']); ?>">

    <?php include ("links.php"); ?>
</head>
<body>
    <?php require("navbar.php"); ?>