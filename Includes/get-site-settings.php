<?php
if (!isset($conn)) {
    require_once(__DIR__ . "/../admin/include/db-connect.php");
}

// Safe defaults, used if the DB row is missing or a field is empty
$site_settings = [
    'site_title'             => 'SellMyPhoneDubai',
    'logo'                   => 'imgs/new-logo.webp',
    'logo_alt'               => 'SellMyPhoneDubai',
    'favicon'                => 'imgs/new-logo.webp',
    'favicon_alt'            => 'SellMyPhoneDubai',
    'nav_home_label'         => 'Home',
    'nav_about_label'        => 'About',
    'nav_quote_label'        => 'Get Instant Quote',
    'nav_blogs_label'        => 'Blogs',
    'nav_testimonials_label' => 'Testimonials',
    'nav_contact_label'      => 'Contact Us',
    'footer_about_text'      => "Dubai's trusted platform for selling used phones with instant cash payment and free pickup service across all areas.",
    'footer_phone'           => '+971 50 216 6562',
    'footer_whatsapp'        => '+971 50 216 6562',
    'footer_email'           => 'info@sellmyphonedubai.ae',
    'footer_address'         => 'Al Quoz 3rd, Showroom No 33, Sheikh Zayed Road, Dubai',
    'facebook_url'           => '#',
    'instagram_url'          => '#',
    'twitter_url'            => '#',
    'linkedin_url'           => '#',
    'copyright_text'         => '© 2026 sellmyphonedubai. All rights reserved.',
];

$settings_result = mysqli_query($conn, "SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
if ($settings_result && $row = mysqli_fetch_assoc($settings_result)) {
    foreach ($site_settings as $key => $default_value) {
        if (isset($row[$key]) && $row[$key] !== '') {
            $site_settings[$key] = $row[$key];
        }
    }
}