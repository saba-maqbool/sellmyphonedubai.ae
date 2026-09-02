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

// Check for a dynamically created custom page (Pages CRUD in admin)
$system_pages = ['home', 'about', 'apple', 'samsung', 'blog', 'blog-details', 'testimonials', 'contact'];
$custom_page  = null;

if (!in_array($page, $system_pages)) {
    $cp_stmt = mysqli_prepare($conn, "SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    mysqli_stmt_bind_param($cp_stmt, "s", $page);
    mysqli_stmt_execute($cp_stmt);
    $cp_result  = mysqli_stmt_get_result($cp_stmt);
    $custom_page = $cp_result ? mysqli_fetch_assoc($cp_result) : null;

    if ($custom_page) {
        $og_title       = $custom_page['og_title'] ?: $meta_title;
        $og_description = $custom_page['og_description'] ?: $meta_description;
        $og_image       = $custom_page['og_image'] ?: $meta_image;
        $meta_title       = $custom_page['meta_title'] ?: $custom_page['title'];
        $meta_description = $custom_page['meta_description'] ?: $meta_description;
        $meta_keywords    = $custom_page['meta_keywords'] ?: $meta_keywords;
        $meta_robots      = $custom_page['meta_robots'] ?: $meta_robots;
        if (!empty($custom_page['canonical_url'])) {
            $canonical_url = $custom_page['canonical_url'];
        }
    }
}

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

    case 'samsung':
        include 'samsung-page.php';
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
        if ($custom_page) {
            include 'page.php';
        } else {
            $meta_robots = "noindex, nofollow";
            include '404.php';
        }
        break;
}

$page_content = ob_get_clean();

include 'Includes/header.php';
echo $page_content;
include 'Includes/footer.php';

?>