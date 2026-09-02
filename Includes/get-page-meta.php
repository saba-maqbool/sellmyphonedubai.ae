<?php
if (!isset($conn)) {
    require_once(__DIR__ . "/../admin/include/db-connect.php");
}

function load_page_meta($conn, $page_key) {
    global $meta_title, $meta_description, $meta_keywords, $meta_robots, $meta_image, $canonical_url;

    $stmt = mysqli_prepare($conn, "SELECT * FROM page_meta WHERE page_key = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $page_key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;

    if ($row) {
        if (!empty($row['meta_title']))       $meta_title = $row['meta_title'];
        if (!empty($row['meta_description'])) $meta_description = $row['meta_description'];
        if (!empty($row['meta_keywords']))    $meta_keywords = $row['meta_keywords'];
        if (!empty($row['meta_robots']))      $meta_robots = $row['meta_robots'];
        if (!empty($row['og_image']))         $meta_image = $row['og_image'];
        if (!empty($row['canonical_url']))    $canonical_url = $row['canonical_url'];
    }
}