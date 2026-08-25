<?php
if (!isset($conn)) {
    require_once(__DIR__ . "/../admin/include/db-connect.php");
}

$whatsapp_link = '#';
$wa_result = mysqli_query($conn, "SELECT whatsapp FROM contact_info WHERE id = 1 LIMIT 1");
if ($wa_result && $wa_row = mysqli_fetch_assoc($wa_result)) {
    if (!empty($wa_row['whatsapp']) && $wa_row['whatsapp'] !== '#') {
        $whatsapp_link = $wa_row['whatsapp'];
    }
}