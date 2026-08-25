<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$contact = [
    'phone'          => '+971 50 216 6562',
    'email'          => 'info@sellphonedubai.com',
    'address'        => "Al Quoz First behind BMW showroom,\nSheikh Zayed Road, Dubai",
    'hours_weekday'  => 'Sun - Thu: 9AM - 10PM',
    'hours_weekend'  => 'Fri - Sat: 10AM - 8PM',
    'facebook'       => '#',
    'instagram'      => '#',
    'twitter'        => '#',
    'linkedin'       => '#',
    'whatsapp'       => '#',
];

$info_result = mysqli_query($conn, "SELECT * FROM contact_info WHERE id = 1 LIMIT 1");
if ($info_result && $info_row = mysqli_fetch_assoc($info_result)) {
    foreach ($contact as $key => $default) {
        if (isset($info_row[$key]) && $info_row[$key] !== '') {
            $contact[$key] = $info_row[$key];
        }
    }
}
?>
<section class="contact-info">
    <div class="div-info">
        <div class="contant-header">
            <h2 class="contact-title">Get In Touch With Us</h2>
            <p class="contact-subtile">Have questions? We'd love to hear from you. Send us a message <br> and we'll respond as soon as possible.</p>
        </div>
        <div class="d-flex mb-5">
            <div class="me-4">
                <div class="rounded-3 d-flex justify-content-center align-items-center icon-box">
                    <i class="fa-solid fa-phone fs-4"></i>
                </div>
            </div>

            <div>
                <h4 class="h4c">Call Us</h4>
                <p class="mb-0 p-c"><?php echo htmlspecialchars($contact['phone']); ?></p>
            </div>
        </div>

        <div class="d-flex mb-5">
            <div class="me-4">
                <div class=" rounded-3 d-flex justify-content-center align-items-center icon-box">
                    <i class="fa-solid fa-envelope fs-4"></i>
                </div>
            </div>

            <div>
                <h4 class="h4c">Email Us</h4>
                <p class="mb-0 p-c">
                    <?php echo htmlspecialchars($contact['email']); ?>
                </p>
            </div>
        </div>

        <div class="d-flex mb-4">
            <div class="me-4">
                <div class="rounded-3 d-flex justify-content-center align-items-center icon-box">
                    <i class="fa-solid fa-location-dot fs-4"></i>
                </div>
            </div>

            <div>
                <h4 class="h4c">Visit Us</h4>
                <p class="p-c">
                    <?php echo nl2br(htmlspecialchars($contact['address'])); ?>
                </p>
            </div>
        </div>

        <div class="d-flex mb-4">
            <div class="me-4">
                <div class="rounded-3 d-flex justify-content-center align-items-center icon-box">
                    <i class="fa-solid fa-clock fs-4"></i>
                </div>
            </div>

            <div>
                <h4 class="h4c">Working Hours</h4>
                <p class="mb-0 p-c">
                    <?php echo htmlspecialchars($contact['hours_weekday']); ?>
                </p>

                <p class=" p-c">
                    <?php echo htmlspecialchars($contact['hours_weekend']); ?>
                </p>
            </div>
        </div>

        <h4 class="mb-4 h4f">Follow Us</h4>

        <div class="d-flex gap-3 btn-div">

            <a href="<?php echo htmlspecialchars($contact['facebook']); ?>" target="_blank" rel="noopener" class="btn-info rounded-3">
                <i class="fab fa-facebook-f"></i>
            </a>

            <a href="<?php echo htmlspecialchars($contact['instagram']); ?>" target="_blank" rel="noopener" class="btn-info rounded-3">
                <i class="fab fa-instagram"></i>
            </a>

            <a href="<?php echo htmlspecialchars($contact['twitter']); ?>" target="_blank" rel="noopener" class="btn-info rounded-3">
                <i class="fab fa-twitter"></i>
            </a>

            <a href="<?php echo htmlspecialchars($contact['linkedin']); ?>" target="_blank" rel="noopener" class="btn-info rounded-3">
                <i class="fab fa-linkedin-in"></i>
            </a>

            <a href="<?php echo htmlspecialchars($contact['whatsapp']); ?>" target="_blank" rel="noopener" class="btn-info rounded-3">
                <i class="fab fa-whatsapp"></i>
            </a>

        </div>
    </div>
</section>