<?php
if (!empty($custom_page['uses_builder'])) {

    $sec_stmt = mysqli_prepare($conn, "SELECT * FROM page_sections WHERE page_id = ? AND status = 'active' ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($sec_stmt, "i", $custom_page['id']);
    mysqli_stmt_execute($sec_stmt);
    $sec_result = mysqli_stmt_get_result($sec_stmt);

    while ($section = mysqli_fetch_assoc($sec_result)) {
        $content = json_decode($section['content'] ?? '{}', true) ?: [];
        $items = [];

        $child_table = null;
        if ($section['section_type'] === 'cards')        $child_table = 'page_section_cards';
        if ($section['section_type'] === 'faq')           $child_table = 'page_section_faqs';
        if ($section['section_type'] === 'testimonials')  $child_table = 'page_section_testimonials';

        if ($child_table) {
            $item_stmt = mysqli_prepare($conn, "SELECT * FROM $child_table WHERE section_id = ? ORDER BY sort_order ASC");
            mysqli_stmt_bind_param($item_stmt, "i", $section['id']);
            mysqli_stmt_execute($item_stmt);
            $item_result = mysqli_stmt_get_result($item_stmt);
            while ($item = mysqli_fetch_assoc($item_result)) {
                $items[] = $item;
            }
        }

        $template = __DIR__ . "/Sections/builder/{$section['section_type']}.php";
        if (file_exists($template)) {
            include $template;
        }
    }

} else {
?>
<section class="dynamic-page-section">
    <div class="dynamic-page-container">
        <?php echo $custom_page['content'] ?? ''; ?>
    </div>
</section>
<?php
}