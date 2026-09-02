<?php
    $pageTitle = "apple";

    if (empty($meta_title)) {
        $meta_title = "Sell Your Apple Devices in Dubai | Instant Cash for iPhone";
    }
    if (empty($meta_description)) {
        $meta_description = "Sell your iPhone in Dubai for the best price. Get an instant quote, free doorstep pickup, and same-day secure payment.";
    }
?>
<?php require_once ("Sections/apple-hero.php"); ?>
<?php require_once ("Sections/apple-featured.php"); ?>
<?php require_once ("Sections/apple-catalog.php"); ?>
<div class="apple-page-catalog" id="apple-catalog-wrap">
    <div class="section-header" style="text-align:center;">
        <h2 class="section-title" id="apple-step-heading">Which <span>model</span> is your phone</h2>
    </div>
    <?php require_once ("Sections/apple.php"); ?>
</div>
<?php require_once ("Sections/modalform.php"); ?>
<?php require_once ("Sections/a-resalevalue.php"); ?>
<?php require_once ("Sections/process.php"); ?>
<?php require_once ("Sections/a-acceptance.php") ?>
<?php $testimonials_section_key = 'apple_testimonials'; require_once ("Sections/testimonials.php"); ?>
<?php require_once ("Sections/comparison.php"); ?>
<?php $faq_section_key = 'apple_faq'; require_once ("Sections/apple-faq.php"); ?>
<?php require_once ("Sections/apple-cta.php"); ?>