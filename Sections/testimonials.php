<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$testimonials_section = [
    'kicker' => 'TESTIMONIALS',
    'heading' => 'What Our Customers Say',
    'description' => 'Trusted by Thousands of Satisfied Customers Across Dubai',
];
$testimonial_items = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'testimonials' LIMIT 1");
mysqli_stmt_execute($stmt);
$testimonials_result = mysqli_stmt_get_result($stmt);
if ($testimonials_row = mysqli_fetch_assoc($testimonials_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($testimonials_row[$field])) {
            $testimonials_section[$field] = $testimonials_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $testimonials_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $testimonial_items[] = $item;
    }
}

function render_testimonial_stars($rating) {
    $rating = floatval($rating ?: 5);
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    $out = '';
    for ($i = 0; $i < $full; $i++) {
        $out .= '<i class="fas fa-star"></i>';
    }
    if ($half) {
        $out .= '<i class="fas fa-star-half-alt"></i>';
    }
    return $out;
}

function testimonial_initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper($part[0]);
        }
    }
    return substr($initials, 0, 2);
}
?>
<section class="testimonials" id="testimonials">
        <div class="section-header">
            <span class="section-tag"><?php echo htmlspecialchars($testimonials_section['kicker']); ?></span>
            <h3 class="section-title" style="color:white;"><?php echo htmlspecialchars($testimonials_section['heading']); ?></h3>
            <p class="section-subtitle" style="color:white;"><?php echo htmlspecialchars($testimonials_section['description']); ?></p>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonial_items as $item): ?>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <?php echo render_testimonial_stars($item['icon']); ?>
                </div>
                <p class="testimonial-content">"<?php echo htmlspecialchars($item['content']); ?>"</p>
                <div class="testimonial-author">
                    <div class="author-avatar"><?php echo htmlspecialchars(testimonial_initials($item['title'])); ?></div>
                    <div class="author-info">
                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                        <p><?php echo htmlspecialchars($item['subtitle']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>