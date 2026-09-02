<?php
include("include/db-connect.php");
include("include/auth-check.php");

$upload_dir = "../imgs/";
$allowed_ext = ["jpg", "jpeg", "png", "webp"];
$success_msg = "";
$error_msg = "";

// Order below mirrors the actual section order on samsung-page.php
$section_labels = [
    'samsung_hero'         => ['label' => 'Hero / Intro',      'icon' => 'fa-solid fa-mobile-screen-button'],
    'samsung_catalog'      => ['label' => 'Series Catalog',    'icon' => 'fa-solid fa-layer-group'],
    'samsung_featured'     => ['label' => 'Featured Devices',  'icon' => 'fa-solid fa-star-of-life'],
    'samsung_resale'       => ['label' => 'Resale Value',      'icon' => 'fa-solid fa-arrow-trend-up'],
    'samsung_testimonials' => ['label' => 'Testimonials',      'icon' => 'fa-solid fa-star'],
    'samsung_faq'          => ['label' => 'FAQs',              'icon' => 'fa-solid fa-circle-question'],
];
$editable_sections = ['samsung_hero', 'samsung_catalog', 'samsung_featured', 'samsung_resale', 'samsung_testimonials', 'samsung_faq'];

function get_or_create_section($conn, $key, $defaults = []) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;

    if ($row) {
        return $row;
    }

    $section_name      = $defaults['section_name'] ?? $key;
    $kicker            = $defaults['kicker'] ?? '';
    $heading           = $defaults['heading'] ?? '';
    $heading_highlight = $defaults['heading_highlight'] ?? '';
    $description       = $defaults['description'] ?? '';
    $extra_1           = $defaults['extra_1'] ?? '';
    $extra_2           = $defaults['extra_2'] ?? '';
    $image             = $defaults['image'] ?? '';
    $image_alt         = $defaults['image_alt'] ?? '';
    $button_text       = $defaults['button_text'] ?? '';
    $button_link       = $defaults['button_link'] ?? '';

    $ins = mysqli_prepare($conn, "INSERT INTO home_sections (section_key, section_name, kicker, heading, heading_highlight, description, extra_1, extra_2, image, image_alt, button_text, button_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, "ssssssssssss", $key, $section_name, $kicker, $heading, $heading_highlight, $description, $extra_1, $extra_2, $image, $image_alt, $button_text, $button_link);
    mysqli_stmt_execute($ins);

    $new_id = mysqli_insert_id($conn);
    $stmt2 = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt2, "i", $new_id);
    mysqli_stmt_execute($stmt2);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
}

$hero_defaults = [
    'section_name'      => 'Sell Samsung - Hero',
    'kicker'            => 'SAMSUNG TRADE-IN DUBAI',
    'heading'           => 'Sell Your',
    'heading_highlight' => 'Samsung',
    'extra_1'           => 'Galaxy in Dubai',
    'description'       => 'Sell your Samsung Galaxy in Dubai at the best price — brand new, original, and used Galaxy phones including the latest Galaxy S25 Ultra, Galaxy Z Fold 6/Flip 6, Galaxy S24, Galaxy S23, and Galaxy A Series, all factory unlocked and 100% genuine. Compare the best Samsung price in UAE with exclusive Samsung buyback deals, free doorstep pickup, and easy Samsung trade-in offers, then get same-day cash for your Samsung phone in Dubai.',
    'image'             => 'imgs/samsung-card.png',
    'image_alt'         => 'Sell your Samsung Galaxy phone in Dubai for the best price',
];
$catalog_defaults = [
    'section_name' => 'Sell Samsung - Series Catalog',
    'kicker'      => 'BROWSE BY SERIES',
    'heading'     => 'Shop by <span>Galaxy Series</span>',
    'description' => 'Explore our complete range of Samsung Galaxy series, from the latest flagships to popular previous generations. Discover vibrant AMOLED displays, powerful performance, and a variety of storage options to suit your need. Browse the Galaxy S25, Galaxy Z Fold &amp; Flip, Galaxy S24, Galaxy S23, and Galaxy A Series to compare your options and find the best resale value for your Samsung phone in Dubai.',
];
$featured_defaults = [
    'section_name' => 'Sell Samsung - Featured Devices',
    'kicker'      => 'BEST OF SAMSUNG',
    'heading'     => 'Top Samsung Devices We Buy',
    'description' => 'Sell your Samsung Galaxy in Dubai at competitive market prices with our easy device valuation service. We buy popular Samsung devices including the Galaxy S25 Ultra, Galaxy S25+, Galaxy Z Fold 6, Galaxy Z Flip 6, Galaxy S24 Ultra, and other Galaxy models — all at the best Samsung resale price in UAE.',
];
$resale_defaults = [
    'section_name'      => 'Sell Samsung - Resale Value',
    'kicker'            => 'ABOUT SAMSUNG RESALE VALUE',
    'heading'           => 'Samsung Resale Value in Dubai:',
    'heading_highlight' => 'Everything You Should Know',
    'description'       => "Samsung Galaxy phones are known for their vibrant AMOLED displays, powerful performance, and long-term One UI software updates. This is exactly why the Samsung resale value in Dubai stays competitive across almost every Galaxy series on the market, from the S Series flagships to the Z Fold and Flip line.",
    'extra_1'           => "In Dubai and across the UAE, demand for a used Samsung phone in Dubai remains consistently high. Buyers trust Samsung's build quality, camera performance, and reliability, which keeps Galaxy trade-in value strong even for older models like the Galaxy S22 and S21 Series.",
    'extra_2'           => "Whether you're upgrading to the latest Galaxy flagship or simply want to sell your Samsung in Dubai, knowing your device's current Samsung resale price helps you get the best possible offer at the right time — with free doorstep pickup and same-day payment.",
    'image'             => 'imgs/samsung.png',
    'image_alt'         => 'Samsung Galaxy resale value in Dubai',
    'button_text'       => 'Check Your Samsung Value',
    'button_link'       => '#series-catalog-section',
];
$faq_defaults = [
    'section_name' => 'Sell Samsung - FAQ',
    'kicker'      => 'FAQ',
    'heading'     => 'Frequently Asked Questions',
    'description' => 'Everything you need to know before you sell your Samsung Galaxy in Dubai — pricing, pickup, and payment.',
];
$testimonials_defaults = [
    'section_name' => 'Sell Samsung - Testimonials',
    'kicker'      => 'TESTIMONIALS',
    'heading'     => 'What Our Customers Say',
    'description' => 'Real sellers who traded in their Samsung Galaxy devices with us across Dubai',
];

// ---------- Update Hero / Intro (samsung_hero) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hero'])) {
    $id                = (int) $_POST['section_id'];
    $kicker            = trim($_POST['kicker'] ?? '');
    $heading           = trim($_POST['heading'] ?? '');
    $heading_highlight = trim($_POST['heading_highlight'] ?? '');
    $extra_1           = trim($_POST['extra_1'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $image             = trim($_POST['image'] ?? '');
    $image_alt         = trim($_POST['image_alt'] ?? '');

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('hero_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . $filename)) {
                $image = "imgs/" . $filename;
            }
        } else {
            $error_msg = "Only jpg, jpeg, png, webp images are allowed.";
        }
    }

    if ($error_msg === "") {
        $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, heading_highlight=?, extra_1=?, description=?, image=?, image_alt=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssssi", $kicker, $heading, $heading_highlight, $extra_1, $description, $image, $image_alt, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Hero / intro content updated successfully.";
        } else {
            $error_msg = "Could not update section: " . mysqli_error($conn);
        }
    }
}
// ---------- Update Series Catalog heading (samsung_catalog) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_catalog_heading'])) {
    $id          = (int) $_POST['section_id'];
    $kicker      = trim($_POST['kicker'] ?? '');
    $heading     = trim($_POST['heading'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, description=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $kicker, $heading, $description, $id);

    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Series catalog heading updated successfully.";
    } else {
        $error_msg = "Could not update section: " . mysqli_error($conn);
    }
}

// ---------- Update Featured Devices header (samsung_featured) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_featured'])) {
    $id          = (int) $_POST['section_id'];
    $kicker      = trim($_POST['kicker'] ?? '');
    $heading     = trim($_POST['heading'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, description=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $kicker, $heading, $description, $id);

    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Featured devices heading updated successfully.";
    } else {
        $error_msg = "Could not update section: " . mysqli_error($conn);
    }
}

// ---------- Update Resale Value (samsung_resale) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_resale'])) {
    $id                = (int) $_POST['section_id'];
    $kicker            = trim($_POST['kicker'] ?? '');
    $heading           = trim($_POST['heading'] ?? '');
    $heading_highlight = trim($_POST['heading_highlight'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $extra_1           = trim($_POST['extra_1'] ?? '');
    $extra_2           = trim($_POST['extra_2'] ?? '');
    $image_alt         = trim($_POST['image_alt'] ?? '');
    $button_text       = trim($_POST['button_text'] ?? '');
    $button_link       = trim($_POST['button_link'] ?? '');

    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('resale_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = "imgs/" . $filename;
            }
        } else {
            $error_msg = "Only jpg, jpeg, png, webp images are allowed.";
        }
    }

    if ($error_msg === "") {
        if ($image_path) {
            $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, heading_highlight=?, description=?, extra_1=?, extra_2=?, image=?, image_alt=?, button_text=?, button_link=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssssssssi", $kicker, $heading, $heading_highlight, $description, $extra_1, $extra_2, $image_path, $image_alt, $button_text, $button_link, $id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, heading_highlight=?, description=?, extra_1=?, extra_2=?, image_alt=?, button_text=?, button_link=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssssssi", $kicker, $heading, $heading_highlight, $description, $extra_1, $extra_2, $image_alt, $button_text, $button_link, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Resale value section updated successfully.";
        } else {
            $error_msg = "Could not update section: " . mysqli_error($conn);
        }
    }
}

// ---------- Add Catalog Series Card ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_catalog'])) {
    $section_id  = (int) $_POST['section_id'];
    $title       = trim($_POST['title'] ?? '');
    $link        = trim($_POST['link'] ?? '');
    $button_link = trim($_POST['button_link'] ?? '');
    $image       = trim($_POST['image'] ?? '');

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('series_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . $filename)) {
                $image = "imgs/" . $filename;
            }
        } else {
            $error_msg = "Only jpg, jpeg, png, webp images are allowed.";
        }
    }

    if ($error_msg === "") {
        $order_result = mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM home_section_items WHERE section_id = " . $section_id);
        $next_order = mysqli_fetch_assoc($order_result)['next_order'];

        $stmt = mysqli_prepare($conn, "INSERT INTO home_section_items (section_id, title, image, link, subtitle, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issssi", $section_id, $title, $image, $link, $button_link, $next_order);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Series card added successfully.";
        } else {
            $error_msg = "Could not add series card: " . mysqli_error($conn);
        }
    }
}

// ---------- Edit Catalog Series Card ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_catalog'])) {
    $item_id     = (int) $_POST['item_id'];
    $title       = trim($_POST['title'] ?? '');
    $link        = trim($_POST['link'] ?? '');
    $button_link = trim($_POST['button_link'] ?? '');
    $image       = trim($_POST['image'] ?? '');

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('series_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . $filename)) {
                $image = "imgs/" . $filename;
            }
        } else {
            $error_msg = "Only jpg, jpeg, png, webp images are allowed.";
        }
    }

    if ($error_msg === "") {
        $stmt = mysqli_prepare($conn, "UPDATE home_section_items SET title=?, image=?, link=?, subtitle=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $title, $image, $link, $button_link, $item_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Series card updated successfully.";
        } else {
            $error_msg = "Could not update series card: " . mysqli_error($conn);
        }
    }
}
// ---------- Delete Catalog Series Card ----------
if (isset($_GET['delete_catalog'])) {
    $item_id = (int) $_GET['delete_catalog'];
    $stmt = mysqli_prepare($conn, "DELETE FROM home_section_items WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    header("Location: samsung-page.php?section=samsung_catalog&deleted=1");
    exit;
}

// ---------- Add FAQ ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_faq'])) {
    $section_id = (int) $_POST['section_id'];
    $question   = trim($_POST['title'] ?? '');
    $answer     = trim($_POST['subtitle'] ?? '');

    $order_result = mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM home_section_items WHERE section_id = " . $section_id);
    $next_order = mysqli_fetch_assoc($order_result)['next_order'];

    $stmt = mysqli_prepare($conn, "INSERT INTO home_section_items (section_id, title, subtitle, sort_order) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issi", $section_id, $question, $answer, $next_order);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "FAQ added successfully.";
    } else {
        $error_msg = "Could not add FAQ: " . mysqli_error($conn);
    }
}

// ---------- Edit FAQ ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_faq'])) {
    $item_id  = (int) $_POST['item_id'];
    $question = trim($_POST['title'] ?? '');
    $answer   = trim($_POST['subtitle'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE home_section_items SET title=?, subtitle=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $question, $answer, $item_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "FAQ updated successfully.";
    } else {
        $error_msg = "Could not update FAQ: " . mysqli_error($conn);
    }
}

// ---------- Delete FAQ ----------
if (isset($_GET['delete_faq'])) {
    $item_id = (int) $_GET['delete_faq'];
    $stmt = mysqli_prepare($conn, "DELETE FROM home_section_items WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    header("Location: samsung-page.php?section=samsung_faq&deleted=1");
    exit;
}

// ---------- Add Testimonial ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_testimonial'])) {
    $section_id = (int) $_POST['section_id'];
    $name       = trim($_POST['name'] ?? '');
    $location   = trim($_POST['location'] ?? '');
    $rating     = trim($_POST['rating'] ?? '5');
    $review     = trim($_POST['review'] ?? '');

    $order_result = mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM home_section_items WHERE section_id = " . $section_id);
    $next_order = mysqli_fetch_assoc($order_result)['next_order'];

    $stmt = mysqli_prepare($conn, "INSERT INTO home_section_items (section_id, icon, title, subtitle, content, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssi", $section_id, $rating, $name, $location, $review, $next_order);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Testimonial added successfully.";
    } else {
        $error_msg = "Could not add testimonial: " . mysqli_error($conn);
    }
}

// ---------- Edit Testimonial ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_testimonial'])) {
    $item_id  = (int) $_POST['item_id'];
    $name     = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $rating   = trim($_POST['rating'] ?? '5');
    $review   = trim($_POST['review'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE home_section_items SET icon=?, title=?, subtitle=?, content=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $rating, $name, $location, $review, $item_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Testimonial updated successfully.";
    } else {
        $error_msg = "Could not update testimonial: " . mysqli_error($conn);
    }
}

// ---------- Delete Testimonial ----------
if (isset($_GET['delete_testimonial'])) {
    $item_id = (int) $_GET['delete_testimonial'];
    $stmt = mysqli_prepare($conn, "DELETE FROM home_section_items WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    header("Location: samsung-page.php?section=samsung_testimonials&deleted=1");
    exit;
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

// Re-fetch all sections fresh (in case one was just updated)
$hero_section          = get_or_create_section($conn, 'samsung_hero', $hero_defaults);
$catalog_section       = get_or_create_section($conn, 'samsung_catalog', $catalog_defaults);
$featured_section      = get_or_create_section($conn, 'samsung_featured', $featured_defaults);
$resale_section        = get_or_create_section($conn, 'samsung_resale', $resale_defaults);
$faq_section           = get_or_create_section($conn, 'samsung_faq', $faq_defaults);
$testimonials_section  = get_or_create_section($conn, 'samsung_testimonials', $testimonials_defaults);

$active_key = $_GET['section'] ?? 'samsung_hero';
if (!in_array($active_key, $editable_sections)) {
    $active_key = 'samsung_hero';
}

$faq_items = [];
$items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
mysqli_stmt_bind_param($items_stmt, "i", $faq_section['id']);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);
while ($row = mysqli_fetch_assoc($items_result)) {
    $faq_items[] = $row;
}

$testimonial_items = [];
$t_items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
mysqli_stmt_bind_param($t_items_stmt, "i", $testimonials_section['id']);
mysqli_stmt_execute($t_items_stmt);
$t_items_result = mysqli_stmt_get_result($t_items_stmt);
while ($row = mysqli_fetch_assoc($t_items_result)) {
    $testimonial_items[] = $row;
}

$catalog_items = [];
$cat_items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
mysqli_stmt_bind_param($cat_items_stmt, "i", $catalog_section['id']);
mysqli_stmt_execute($cat_items_stmt);
$cat_items_result = mysqli_stmt_get_result($cat_items_stmt);
while ($row = mysqli_fetch_assoc($cat_items_result)) {
    $catalog_items[] = $row;
}
?>
<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Sell Samsung Page</h1>
            <p class="current-date">Manage the content for the standalone /sell-samsung-phone-dubai page</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Section picker -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mb-4">
        <?php foreach ($section_labels as $key => $meta): ?>
            <div class="col">
                <a href="samsung-page.php?section=<?php echo $key; ?>"
                   class="card h-100 text-decoration-none"
                   style="border-radius:14px; padding:16px; text-align:center; border:2px solid <?php echo $active_key === $key ? '#0B1E3F' : '#eee'; ?>;">
                    <i class="<?php echo $meta['icon']; ?>" style="font-size:22px; color:#0B1E3F;"></i>
                    <p style="margin:8px 0 0; font-weight:600; color:#0B1E3F; font-size:14px;"><?php echo $meta['label']; ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <p style="color:#797979; font-size:13px; margin-top:-10px; margin-bottom:20px;">
        Pricing and models are already managed under <a href="pricing.php">Pricing</a> and <a href="models.php">Models</a> — no separate data entry needed here.
    </p>

    <?php if ($active_key === 'samsung_hero'): ?>

        <!-- Hero / Intro -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-mobile-screen-button me-2"></i>Hero Heading &amp; Intro Text</h5>

            <form method="POST" action="samsung-page.php?section=samsung_hero">
                <input type="hidden" name="section_id" value="<?php echo (int) $hero_section['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small tag above heading)</label>
                        <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($hero_section['kicker']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading (before highlight)</label>
                        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($hero_section['heading']); ?>" placeholder="e.g. Sell Your">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Highlighted word (colored)</label>
                        <input type="text" name="heading_highlight" class="form-control" value="<?php echo htmlspecialchars($hero_section['heading_highlight']); ?>" placeholder="e.g. Samsung">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading (after highlight)</label>
                        <input type="text" name="extra_1" class="form-control" value="<?php echo htmlspecialchars($hero_section['extra_1']); ?>" placeholder="e.g. Galaxy in Dubai">
                    </div>
                    <div class="col-12">
                        <p style="font-size:11px; color:#aaa; margin:-6px 0 0;">Preview: <?php echo htmlspecialchars($hero_section['heading']); ?> <strong style="color:#0B1E3F;"><?php echo htmlspecialchars($hero_section['heading_highlight']); ?></strong> <?php echo htmlspecialchars($hero_section['extra_1']); ?></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Intro text (paragraph under the heading)</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($hero_section['description']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Hero image path</label>
                        <input type="text" name="image" class="form-control" value="<?php echo htmlspecialchars($hero_section['image']); ?>" placeholder="imgs/samsung-card.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Hero image alt text</label>
                        <input type="text" name="image_alt" class="form-control" value="<?php echo htmlspecialchars($hero_section['image_alt']); ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" name="update_hero" class="btn" style="background:#0B1E3F; color:#fff;">Save Hero / Intro</button>
                </div>
            </form>
        </div>

    <?php elseif ($active_key === 'samsung_catalog'): ?>

        <!-- Series Catalog header text -->
        <div class="card" style="border-radius:14px; padding:24px; margin-bottom:20px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-layer-group me-2"></i>Series Catalog Heading</h5>
            <form method="POST" action="samsung-page.php?section=samsung_catalog">
                <input type="hidden" name="section_id" value="<?php echo (int) $catalog_section['id']; ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Tag (small label above heading)</label>
                        <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($catalog_section['kicker']); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading</label>
                        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($catalog_section['heading']); ?>" placeholder="Shop by <span>Galaxy Series</span>">
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($catalog_section['description']); ?></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" name="update_catalog_heading" class="btn" style="background:#0B1E3F; color:#fff;">Save Heading</button>
                </div>
            </form>
        </div>

        <!-- Series Cards -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="margin:0;"><i class="fa-solid fa-layer-group me-2"></i>Series Cards</h5>
                <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addCatalogModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Series Card
                </button>
            </div>
            <p style="color:#797979; font-size:12.5px; margin-top:-6px;">"Filter keyword" must match the start of the model names in Models (e.g. "galaxy s25" matches "Galaxy S25 Ultra").</p>
            <?php if (empty($catalog_items)): ?>
                <p style="color:#797979; margin:0;">No series cards yet. Add your first one above.</p>
            <?php endif; ?>

            <div class="row g-3">
                <?php foreach ($catalog_items as $item): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card" style="border-radius:12px; padding:14px;">
                            <?php if (!empty($item['image'])): ?>
                                <img src="../<?php echo htmlspecialchars($item['image']); ?>" style="width:100%; height:110px; object-fit:cover; border-radius:8px; margin-bottom:8px;">
                            <?php endif; ?>
                            <h6 style="margin:0 0 4px;"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <small style="color:#797979; display:block;">Filter: <?php echo htmlspecialchars($item['link']); ?></small>
                            <small style="color:#797979; display:block;">View All link: <?php echo !empty($item['subtitle']) ? htmlspecialchars($item['subtitle']) : '<span style="color:#c0392b;">not set</span>'; ?></small>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editCatalogModal"
                                    data-id="<?php echo (int) $item['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                    data-link="<?php echo htmlspecialchars($item['link']); ?>"
                                    data-button-link="<?php echo htmlspecialchars($item['subtitle'] ?? ''); ?>"
                                    data-image="<?php echo htmlspecialchars($item['image']); ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="samsung-page.php?delete_catalog=<?php echo (int) $item['id']; ?>"
                                   class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                                   onclick="return confirm('Delete this series card?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add Catalog Series Modal -->
        <div class="modal fade" id="addCatalogModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="samsung-page.php?section=samsung_catalog" enctype="multipart/form-data">
                        <input type="hidden" name="section_id" value="<?php echo (int) $catalog_section['id']; ?>">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add Series Card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Series Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Galaxy S25 Series" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Filter keyword</label>
                                    <input type="text" name="link" class="form-control" placeholder="e.g. galaxy s25" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Image</label>
                                    <input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                    <input type="hidden" name="image" value="">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_catalog" class="btn" style="background:#0B1E3F; color:#fff;">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Catalog Series Modal -->
                <div class="modal fade" id="editCatalogModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="samsung-page.php?section=samsung_catalog" enctype="multipart/form-data">
                        <input type="hidden" name="item_id" id="editCatalogId" value="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit Series Card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Series Title</label>
                                    <input type="text" name="title" id="editCatalogTitle" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Filter keyword</label>
                                    <input type="text" name="link" id="editCatalogLink" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">"View All" button link (optional)</label>
                                    <input type="text" name="button_link" id="editCatalogButtonLink" class="form-control" placeholder="e.g. https://... or leave blank to filter on this page">
                                    <p style="font-size:11px; color:#aaa; margin:4px 0 0;">If set, the "View All" button opens this link. If left blank, it filters the models on this page using the keyword above.</p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Replace image (optional)</label>
                                    <input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                    <input type="hidden" name="image" id="editCatalogImage" value="">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit_catalog" class="btn" style="background:#0B1E3F; color:#fff;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var editCatalogModal = document.getElementById('editCatalogModal');
                editCatalogModal.addEventListener('show.bs.modal', function (event) {
                    var btn = event.relatedTarget;
                    document.getElementById('editCatalogId').value = btn.getAttribute('data-id');
                    document.getElementById('editCatalogTitle').value = btn.getAttribute('data-title');
                    document.getElementById('editCatalogLink').value = btn.getAttribute('data-link');
                    document.getElementById('editCatalogButtonLink').value = btn.getAttribute('data-button-link');
                    document.getElementById('editCatalogImage').value = btn.getAttribute('data-image');
                });
            });
        </script>
        
    <?php elseif ($active_key === 'samsung_featured'): ?>

        <!-- Featured Devices heading (the models themselves are pulled live from Models/Pricing) -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-star-of-life me-2"></i>Featured Devices Heading</h5>
            <p style="color:#797979; font-size:12.5px; margin-top:-10px;">The device cards below this heading update automatically from <a href="models.php">Models</a> and <a href="pricing.php">Pricing</a> — only the heading text is edited here.</p>

            <form method="POST" action="samsung-page.php?section=samsung_featured">
                <input type="hidden" name="section_id" value="<?php echo (int) $featured_section['id']; ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Tag (small label above heading)</label>
                        <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($featured_section['kicker']); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading</label>
                        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($featured_section['heading']); ?>" placeholder="e.g. Top Samsung Devices We Buy">
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($featured_section['description']); ?></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" name="update_featured" class="btn" style="background:#0B1E3F; color:#fff;">Save Heading</button>
                </div>
            </form>
        </div>

    <?php elseif ($active_key === 'samsung_resale'): ?>

        <!-- Resale Value -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-arrow-trend-up me-2"></i>Resale Value Section</h5>

            <form method="POST" action="samsung-page.php?section=samsung_resale" enctype="multipart/form-data">
                <input type="hidden" name="section_id" value="<?php echo (int) $resale_section['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Tag (small label above heading)</label>
                        <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($resale_section['kicker']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading (first line)</label>
                        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($resale_section['heading']); ?>" placeholder="e.g. Samsung Resale Value in Dubai:">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading (highlighted second line)</label>
                        <input type="text" name="heading_highlight" class="form-control" value="<?php echo htmlspecialchars($resale_section['heading_highlight']); ?>" placeholder="e.g. Everything You Should Know">
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Paragraph 1</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($resale_section['description']); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Paragraph 2</label>
                        <textarea name="extra_1" class="form-control" rows="3"><?php echo htmlspecialchars($resale_section['extra_1']); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Paragraph 3</label>
                        <textarea name="extra_2" class="form-control" rows="3"><?php echo htmlspecialchars($resale_section['extra_2']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Button text</label>
                        <input type="text" name="button_text" class="form-control" value="<?php echo htmlspecialchars($resale_section['button_text']); ?>" placeholder="e.g. Check Your Samsung Value">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Button link</label>
                        <input type="text" name="button_link" class="form-control" value="<?php echo htmlspecialchars($resale_section['button_link']); ?>" placeholder="#series-catalog-section">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Image</label>
                        <?php if (!empty($resale_section['image'])): ?>
                            <div class="mb-2"><img src="../<?php echo htmlspecialchars($resale_section['image']); ?>" style="max-height:70px; border-radius:8px;"></div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Image alt text</label>
                        <input type="text" name="image_alt" class="form-control" value="<?php echo htmlspecialchars($resale_section['image_alt']); ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" name="update_resale" class="btn" style="background:#0B1E3F; color:#fff;">Save Resale Value Section</button>
                </div>
            </form>
        </div>

    <?php elseif ($active_key === 'samsung_faq'): ?>

        <!-- FAQs -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="margin:0;"><i class="fa-solid fa-circle-question me-2"></i>FAQs</h5>
                <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                    <i class="fa-solid fa-plus me-1"></i> Add FAQ
                </button>
            </div>

            <?php if (empty($faq_items)): ?>
                <p style="color:#797979; margin:0;">No FAQs yet. Add your first question above.</p>
            <?php endif; ?>

            <?php foreach ($faq_items as $item): ?>
                <div class="card" style="border-radius:12px; padding:14px; margin-bottom:10px;">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h6 style="margin:0 0 4px;"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                        </div>
                        <div class="d-flex gap-2" style="flex-shrink:0;">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#editFaqModal"
                                data-id="<?php echo (int) $item['id']; ?>"
                                data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="samsung-page.php?delete_faq=<?php echo (int) $item['id']; ?>"
                               class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this FAQ?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add FAQ Modal -->
        <div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="samsung-page.php?section=samsung_faq">
                        <input type="hidden" name="section_id" value="<?php echo (int) $faq_section['id']; ?>">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add FAQ</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Question</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. How much is my Samsung worth?" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Answer</label>
                                    <textarea name="subtitle" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_faq" class="btn" style="background:#0B1E3F; color:#fff;">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit FAQ Modal -->
        <div class="modal fade" id="editFaqModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="samsung-page.php?section=samsung_faq">
                        <input type="hidden" name="item_id" id="editFaqId" value="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit FAQ</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Question</label>
                                    <input type="text" name="title" id="editFaqTitle" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Answer</label>
                                    <textarea name="subtitle" id="editFaqSubtitle" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit_faq" class="btn" style="background:#0B1E3F; color:#fff;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var editModal = document.getElementById('editFaqModal');
                editModal.addEventListener('show.bs.modal', function (event) {
                    var btn = event.relatedTarget;
                    document.getElementById('editFaqId').value = btn.getAttribute('data-id');
                    document.getElementById('editFaqTitle').value = btn.getAttribute('data-title');
                    document.getElementById('editFaqSubtitle').value = btn.getAttribute('data-subtitle');
                });
            });
        </script>

    <?php elseif ($active_key === 'samsung_testimonials'): ?>

        <!-- Testimonials -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="margin:0;"><i class="fa-solid fa-star me-2"></i>Testimonials</h5>
                <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Testimonial
                </button>
            </div>

            <?php if (empty($testimonial_items)): ?>
                <p style="color:#797979; margin:0;">No testimonials yet. Add your first review above.</p>
            <?php endif; ?>

            <?php foreach ($testimonial_items as $item): ?>
                <div class="card" style="border-radius:12px; padding:14px; margin-bottom:10px;">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h6 style="margin:0 0 4px;"><?php echo htmlspecialchars($item['title']); ?> <small style="color:#797979; font-weight:400;">— <?php echo htmlspecialchars($item['subtitle']); ?></small></h6>
                            <small style="color:#e8a94e;"><i class="fa-solid fa-star"></i> <?php echo htmlspecialchars($item['icon']); ?>/5</small>
                            <p style="margin:6px 0 0; color:#555; font-size:13.5px;"><?php echo htmlspecialchars($item['content']); ?></p>
                        </div>
                        <div class="d-flex gap-2" style="flex-shrink:0;">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#editTestimonialModal"
                                data-id="<?php echo (int) $item['id']; ?>"
                                data-name="<?php echo htmlspecialchars($item['title']); ?>"
                                data-location="<?php echo htmlspecialchars($item['subtitle']); ?>"
                                data-rating="<?php echo htmlspecialchars($item['icon']); ?>"
                                data-review="<?php echo htmlspecialchars($item['content']); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="samsung-page.php?delete_testimonial=<?php echo (int) $item['id']; ?>"
                               class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this testimonial?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add Testimonial Modal -->
        <div class="modal fade" id="addTestimonialModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="samsung-page.php?section=samsung_testimonials">
                        <input type="hidden" name="section_id" value="<?php echo (int) $testimonials_section['id']; ?>">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add Testimonial</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Customer Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Ahmed Khan" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Location</label>
                                    <input type="text" name="location" class="form-control" placeholder="e.g. Downtown Dubai">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Rating (out of 5)</label>
                                    <select name="rating" class="form-control">
                                        <option value="5">5</option>
                                        <option value="4.5">4.5</option>
                                        <option value="4">4</option>
                                        <option value="3.5">3.5</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Review</label>
                                    <textarea name="review" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_testimonial" class="btn" style="background:#0B1E3F; color:#fff;">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Testimonial Modal -->
        <div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="samsung-page.php?section=samsung_testimonials">
                        <input type="hidden" name="item_id" id="editTestimonialId" value="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit Testimonial</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Customer Name</label>
                                    <input type="text" name="name" id="editTestimonialName" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Location</label>
                                    <input type="text" name="location" id="editTestimonialLocation" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Rating (out of 5)</label>
                                    <select name="rating" id="editTestimonialRating" class="form-control">
                                        <option value="5">5</option>
                                        <option value="4.5">4.5</option>
                                        <option value="4">4</option>
                                        <option value="3.5">3.5</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Review</label>
                                    <textarea name="review" id="editTestimonialReview" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit_testimonial" class="btn" style="background:#0B1E3F; color:#fff;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var editTestimonialModal = document.getElementById('editTestimonialModal');
                editTestimonialModal.addEventListener('show.bs.modal', function (event) {
                    var btn = event.relatedTarget;
                    document.getElementById('editTestimonialId').value = btn.getAttribute('data-id');
                    document.getElementById('editTestimonialName').value = btn.getAttribute('data-name');
                    document.getElementById('editTestimonialLocation').value = btn.getAttribute('data-location');
                    document.getElementById('editTestimonialRating').value = btn.getAttribute('data-rating');
                    document.getElementById('editTestimonialReview').value = btn.getAttribute('data-review');
                });
            });
        </script>

    <?php endif; ?>

</div>
</body>
</html>