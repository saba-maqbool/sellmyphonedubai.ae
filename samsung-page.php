<?php
    $pageTitle = "samsung";

    if (empty($meta_title)) {
        $meta_title = "Sell Samsung Galaxy in Dubai | Best Price for Used Samsung Phones";
    }
    if (empty($meta_description)) {
        $meta_description = "Sell your Samsung Galaxy in Dubai for the best price — S25, S24, Z Fold/Flip & A Series. Get an instant quote, free doorstep pickup, and same-day secure cash payment.";
    }
?>
<?php require_once ("Sections/samsung-hero.php"); ?>
<?php require_once ("Sections/samsung-catalog.php"); ?>
<div class="samsung-page-catalog" id="samsung-catalog-wrap">
    <div class="section-header" style="text-align:center;">
        <h2 class="section-title" id="samsung-step-heading">Which <span>model</span> is your phone</h2>
    </div>
    <?php require_once ("Sections/samsung.php"); ?>
</div>
<?php require_once ("Sections/modalform.php"); ?>
<?php require_once ("Sections/samsung-featured.php"); ?>
<?php require_once ("Sections/s-resalevalue.php"); ?>
<?php require_once ("Sections/process.php"); ?>
<?php require_once ("Sections/s-acceptance.php") ?>
<?php $testimonials_section_key = 'samsung_testimonials'; require_once ("Sections/testimonials.php"); ?>
<?php require_once ("Sections/comparison.php"); ?>
<?php $faq_section_key = 'samsung_faq'; require_once ("Sections/samsung-faq.php"); ?>
<?php require_once ("Sections/samsung-cta.php"); ?>