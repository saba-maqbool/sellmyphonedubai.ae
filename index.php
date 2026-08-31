<?php

require_once(__DIR__ . "/admin/include/db-connect.php");
require_once(__DIR__ . "/Includes/get-page-meta.php");

$page = $_GET['page'] ?? 'home';
$meta_title       = "Sell My Phone Dubai | Instant Cash for Used iPhone & Samsung";
$meta_description = "Sell your used iPhone, Samsung or any smartphone in Dubai for the best price. Get an instant quote, free doorstep pickup, and same-day secure payment.";
$meta_keywords    = "sell phone dubai, sell iphone dubai, sell samsung dubai, sell used phone dubai, cash for phones dubai, we buy phones dubai, phone buyer dubai, sell mobile dubai";
$meta_robots      = "index, follow";
$meta_image       = "imgs/hero.webp";
$canonical_url    = "https://sellmyphonedubai.com/" . ($page === 'home' ? '' : $page);

load_page_meta($conn, $page);

ob_start();

switch ($page) {

    case 'home':
        include 'home.php';
        break;

    case 'about':
        include 'about.php';
        break;
    
    case 'apple':
        include 'apple-page.php';
        break;

    case 'blog':
        include 'blog.php';
        break;

    case 'blog-details':
        include 'blog-details.php';
        break;

    case 'testimonials':
        include 'testimonials.php';
        break;

    case 'contact':
        include 'contact.php';
        break;

    default:
        $meta_robots = "noindex, nofollow";
        include '404.php';
        break;
}

$page_content = ob_get_clean();

include 'Includes/header.php';
echo $page_content;
include 'Includes/footer.php';

?>