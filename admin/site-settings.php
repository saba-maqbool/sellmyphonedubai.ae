<?php
include("include/db-connect.php");
include("include/auth-check.php");

$success_msg = "";
$error_msg = "";
$upload_dir = "../imgs/";
$allowed_ext = ["jpg", "jpeg", "png", "webp"];

// Make sure a row exists so UPDATE always has something to update
$check = mysqli_query($conn, "SELECT id FROM site_settings WHERE id = 1 LIMIT 1");
if ($check && mysqli_num_rows($check) === 0) {
    mysqli_query($conn, "INSERT INTO site_settings (id) VALUES (1)");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title             = trim($_POST['site_title'] ?? '');
    $nav_home_label         = trim($_POST['nav_home_label'] ?? '');
    $nav_about_label        = trim($_POST['nav_about_label'] ?? '');
    $nav_quote_label        = trim($_POST['nav_quote_label'] ?? '');
    $nav_blogs_label        = trim($_POST['nav_blogs_label'] ?? '');
    $nav_testimonials_label = trim($_POST['nav_testimonials_label'] ?? '');
    $nav_contact_label      = trim($_POST['nav_contact_label'] ?? '');
    $footer_about_text      = trim($_POST['footer_about_text'] ?? '');
    $footer_phone           = trim($_POST['footer_phone'] ?? '');
    $footer_whatsapp        = trim($_POST['footer_whatsapp'] ?? '');
    $footer_email           = trim($_POST['footer_email'] ?? '');
    $footer_address         = trim($_POST['footer_address'] ?? '');
    $facebook_url           = trim($_POST['facebook_url'] ?? '');
    $instagram_url          = trim($_POST['instagram_url'] ?? '');
    $twitter_url            = trim($_POST['twitter_url'] ?? '');
    $linkedin_url           = trim($_POST['linkedin_url'] ?? '');
    $copyright_text         = trim($_POST['copyright_text'] ?? '');

    if ($site_title === '') {
        $error_msg = "Site title is required.";
    } else {
        // Handle logo upload (optional - only replace if a new file is sent)
        $logo_sql = "";
        $logo_path = null;

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_ext)) {
                $filename = 'logo_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $filename)) {
                    $logo_path = "imgs/" . $filename;
                }
            } else {
                $error_msg = "Logo must be a jpg, jpeg, png or webp file.";
            }
        }

        // Handle favicon upload (optional - only replace if a new file is sent)
        $favicon_path = null;

        if ($error_msg === '' && isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_ext)) {
                $filename = 'favicon_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['favicon']['tmp_name'], $upload_dir . $filename)) {
                    $favicon_path = "imgs/" . $filename;
                }
            } else {
                $error_msg = "Favicon must be a jpg, jpeg, png or webp file.";
            }
        }

        if ($error_msg === '') {
            // Build the SET clause dynamically so logo/favicon are only touched when a new file was uploaded
            $set_fields = "site_title=?, nav_home_label=?, nav_about_label=?, nav_quote_label=?,
                    nav_blogs_label=?, nav_testimonials_label=?, nav_contact_label=?,
                    footer_about_text=?, footer_phone=?, footer_whatsapp=?, footer_email=?, footer_address=?,
                    facebook_url=?, instagram_url=?, twitter_url=?, linkedin_url=?, copyright_text=?";
            $types  = "sssssssssssssssss";
            $params = [
                $site_title, $nav_home_label, $nav_about_label, $nav_quote_label,
                $nav_blogs_label, $nav_testimonials_label, $nav_contact_label,
                $footer_about_text, $footer_phone, $footer_whatsapp, $footer_email, $footer_address,
                $facebook_url, $instagram_url, $twitter_url, $linkedin_url, $copyright_text
            ];

            if ($logo_path) {
                $set_fields .= ", logo=?";
                $types      .= "s";
                $params[]    = $logo_path;
            }
            if ($favicon_path) {
                $set_fields .= ", favicon=?";
                $types      .= "s";
                $params[]    = $favicon_path;
            }

            $stmt = mysqli_prepare($conn, "UPDATE site_settings SET $set_fields WHERE id=1");
            mysqli_stmt_bind_param($stmt, $types, ...$params);

            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Site settings updated successfully.";
            } else {
                $error_msg = "Could not update settings: " . mysqli_stmt_error($stmt);
            }
        }
    }
}

$result = mysqli_query($conn, "SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
$s = mysqli_fetch_assoc($result) ?: [];
function sv($s, $key) { return htmlspecialchars($s[$key] ?? ''); }
?>
<?php include("include/a-header.php"); ?>
<?php include("section/sidebar.php"); ?>

<div class="main-content">

    <div class="content-header">
        <h2><i class="fa-solid fa-gear"></i> Site Settings</h2>
        <p class="text-muted">Logo, browser title, navbar labels, and footer content — shown across every page.</p>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4" style="border-radius:14px;">

        <h5 class="mb-3">Branding</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Site Title</label>
                <input type="text" name="site_title" class="form-control" value="<?php echo sv($s, 'site_title'); ?>" required>
                <small class="text-muted">Used in the browser tab and footer.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Logo</label>
                <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <?php if (!empty($s['logo'])): ?>
                    <img src="../<?php echo sv($s, 'logo'); ?>" style="height:40px; margin-top:8px;">
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Favicon</label>
                <input type="file" name="favicon" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <small class="text-muted">Shown in the browser tab. Square image works best (e.g. 32x32 / 64x64).</small>
                <?php if (!empty($s['favicon'])): ?>
                    <img src="../<?php echo sv($s, 'favicon'); ?>" style="height:32px; margin-top:8px;">
                <?php endif; ?>
            </div>
        </div>

        <hr>
        <h5 class="mb-3">Navbar Menu Labels</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Home</label>
                <input type="text" name="nav_home_label" class="form-control" value="<?php echo sv($s, 'nav_home_label'); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">About</label>
                <input type="text" name="nav_about_label" class="form-control" value="<?php echo sv($s, 'nav_about_label'); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Get Instant Quote</label>
                <input type="text" name="nav_quote_label" class="form-control" value="<?php echo sv($s, 'nav_quote_label'); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Blogs</label>
                <input type="text" name="nav_blogs_label" class="form-control" value="<?php echo sv($s, 'nav_blogs_label'); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Testimonials</label>
                <input type="text" name="nav_testimonials_label" class="form-control" value="<?php echo sv($s, 'nav_testimonials_label'); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="nav_contact_label" class="form-control" value="<?php echo sv($s, 'nav_contact_label'); ?>">
            </div>
        </div>

        <hr>
        <h5 class="mb-3">Footer</h5>
        <div class="mb-3">
            <label class="form-label">About Text</label>
            <textarea name="footer_about_text" class="form-control" rows="3"><?php echo sv($s, 'footer_about_text'); ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="footer_phone" class="form-control" value="<?php echo sv($s, 'footer_phone'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">WhatsApp Number (display only)</label>
                <input type="text" name="footer_whatsapp" class="form-control" value="<?php echo sv($s, 'footer_whatsapp'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="footer_email" class="form-control" value="<?php echo sv($s, 'footer_email'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="footer_address" class="form-control" value="<?php echo sv($s, 'footer_address'); ?>">
            </div>
        </div>

        <hr>
        <h5 class="mb-3">Social Links</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-facebook-f"></i> Facebook URL</label>
                <input type="text" name="facebook_url" class="form-control" value="<?php echo sv($s, 'facebook_url'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-instagram"></i> Instagram URL</label>
                <input type="text" name="instagram_url" class="form-control" value="<?php echo sv($s, 'instagram_url'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-twitter"></i> Twitter URL</label>
                <input type="text" name="twitter_url" class="form-control" value="<?php echo sv($s, 'twitter_url'); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-linkedin-in"></i> LinkedIn URL</label>
                <input type="text" name="linkedin_url" class="form-control" value="<?php echo sv($s, 'linkedin_url'); ?>">
            </div>
        </div>

        <hr>
        <div class="mb-3">
            <label class="form-label">Copyright Text</label>
            <input type="text" name="copyright_text" class="form-control" value="<?php echo sv($s, 'copyright_text'); ?>">
        </div>

        <button type="submit" class="btn btn-primary mt-2">Save Settings</button>
    </form>
</div>

</body>
</html>