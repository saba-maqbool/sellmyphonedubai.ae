<?php
include("include/db-connect.php");
include("include/auth-check.php");

$page_id = (int) ($_GET['page_id'] ?? 0);
$upload_dir = "../imgs/";
$allowed_ext = ["jpg", "jpeg", "png", "webp"];
$success_msg = "";
$error_msg = "";

$section_labels = [
    'rich_text'    => ['label' => 'Rich Text / HTML',           'icon' => 'fa-solid fa-align-left'],
    'kicker_title' => ['label' => 'Kicker + Title + Subtitle',  'icon' => 'fa-solid fa-heading'],
    'text_image'   => ['label' => 'Text + Image',               'icon' => 'fa-solid fa-image'],
    'cards'        => ['label' => 'Cards Grid',                 'icon' => 'fa-solid fa-table-cells'],
    'faq'          => ['label' => 'FAQ',                        'icon' => 'fa-solid fa-circle-question'],
    'testimonials' => ['label' => 'Testimonials',               'icon' => 'fa-solid fa-comment-dots'],
    'cta'          => ['label' => 'CTA Banner',                 'icon' => 'fa-solid fa-bullhorn'],
    'custom_html'  => ['label' => 'Custom HTML',                'icon' => 'fa-solid fa-code'],
];

$page_stmt = mysqli_prepare($conn, "SELECT * FROM pages WHERE id = ?");
mysqli_stmt_bind_param($page_stmt, "i", $page_id);
mysqli_stmt_execute($page_stmt);
$page_row = mysqli_stmt_get_result($page_stmt)->fetch_assoc();
if (!$page_row) { die("Page not found."); }

function bx_upload_image($field, $upload_dir, $allowed_ext, $prefix) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid($prefix . '_') . '.' . $ext;
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $upload_dir . $filename)) {
                return "imgs/" . $filename;
            }
        }
    }
    return null;
}

// ---------- ADD SECTION ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_section'])) {
    $type = $_POST['section_type'] ?? '';
    if (!isset($section_labels[$type])) {
        $error_msg = "Invalid section type.";
    } else {
        $content = [];
        switch ($type) {
            case 'rich_text':
            case 'custom_html':
                $content = [
                    'kicker'   => trim($_POST['kicker'] ?? ''),
                    'heading'  => trim($_POST['heading'] ?? ''),
                    'subtitle' => trim($_POST['subtitle'] ?? ''),
                    'html'     => $_POST['html'] ?? '',
                ];
                break;
            case 'kicker_title':
                $content = [
                    'kicker'   => trim($_POST['kicker'] ?? ''),
                    'heading'  => trim($_POST['heading'] ?? ''),
                    'subtitle' => trim($_POST['subtitle'] ?? ''),
                ];
                break;
            case 'text_image':
                $img = bx_upload_image('image', $upload_dir, $allowed_ext, 'ti');
                $content = [
                    'kicker'         => trim($_POST['kicker'] ?? ''),
                    'heading'        => trim($_POST['heading'] ?? ''),
                    'description'    => trim($_POST['description'] ?? ''),
                    'button_text'    => trim($_POST['button_text'] ?? ''),
                    'button_link'    => trim($_POST['button_link'] ?? ''),
                    'image'          => $img,
                    'image_position' => ($_POST['image_position'] ?? 'right') === 'left' ? 'left' : 'right',
                ];
                break;
            case 'cards':
                $content = [
                    'kicker'        => trim($_POST['kicker'] ?? ''),
                    'heading'       => trim($_POST['heading'] ?? ''),
                    'subtitle'      => trim($_POST['subtitle'] ?? ''),
                    'cards_per_row' => max(2, min(4, (int) ($_POST['cards_per_row'] ?? 3))),
                ];
                break;
            case 'faq':
            case 'testimonials':
                $content = [
                    'kicker'   => trim($_POST['kicker'] ?? ''),
                    'heading'  => trim($_POST['heading'] ?? ''),
                    'subtitle' => trim($_POST['subtitle'] ?? ''),
                ];
                break;
            case 'cta':
                $content = [
                    'kicker'      => trim($_POST['kicker'] ?? ''),
                    'heading'     => trim($_POST['heading'] ?? ''),
                    'subtitle'    => trim($_POST['subtitle'] ?? ''),
                    'button_text' => trim($_POST['button_text'] ?? ''),
                    'button_link' => trim($_POST['button_link'] ?? ''),
                ];
                break;
        }

        $order_res = mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM page_sections WHERE page_id = " . (int) $page_id);
        $next_order = mysqli_fetch_assoc($order_res)['next_order'];
        $json = json_encode($content);

        $stmt = mysqli_prepare($conn, "INSERT INTO page_sections (page_id, section_type, content, sort_order) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issi", $page_id, $type, $json, $next_order);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_query($conn, "UPDATE pages SET uses_builder = 1 WHERE id = " . (int) $page_id);
            header("Location: page-builder.php?page_id=" . $page_id . "&added=1");
            exit;
        } else {
            $error_msg = "Could not add section: " . mysqli_error($conn);
        }
    }
}

// ---------- EDIT SECTION ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_section'])) {
    $section_id = (int) ($_POST['section_id'] ?? 0);
    $type = $_POST['section_type'] ?? '';

    $ex_stmt = mysqli_prepare($conn, "SELECT content FROM page_sections WHERE id = ? AND page_id = ?");
    mysqli_stmt_bind_param($ex_stmt, "ii", $section_id, $page_id);
    mysqli_stmt_execute($ex_stmt);
    $ex_row = mysqli_stmt_get_result($ex_stmt)->fetch_assoc();
    $content = $ex_row ? (json_decode($ex_row['content'], true) ?: []) : [];

    switch ($type) {
        case 'rich_text':
        case 'custom_html':
            $content['kicker'] = trim($_POST['kicker'] ?? '');
            $content['heading'] = trim($_POST['heading'] ?? '');
            $content['subtitle'] = trim($_POST['subtitle'] ?? '');
            $content['html'] = $_POST['html'] ?? '';
            break;
        case 'kicker_title':
            $content['kicker'] = trim($_POST['kicker'] ?? '');
            $content['heading'] = trim($_POST['heading'] ?? '');
            $content['subtitle'] = trim($_POST['subtitle'] ?? '');
            break;
        case 'text_image':
            $img = bx_upload_image('image', $upload_dir, $allowed_ext, 'ti');
            $content['kicker'] = trim($_POST['kicker'] ?? '');
            $content['heading'] = trim($_POST['heading'] ?? '');
            $content['description'] = trim($_POST['description'] ?? '');
            $content['button_text'] = trim($_POST['button_text'] ?? '');
            $content['button_link'] = trim($_POST['button_link'] ?? '');
            if ($img) $content['image'] = $img;
            $content['image_position'] = ($_POST['image_position'] ?? 'right') === 'left' ? 'left' : 'right';
            break;
        case 'cards':
            $content['kicker'] = trim($_POST['kicker'] ?? '');
            $content['heading'] = trim($_POST['heading'] ?? '');
            $content['subtitle'] = trim($_POST['subtitle'] ?? '');
            $content['cards_per_row'] = max(2, min(4, (int) ($_POST['cards_per_row'] ?? 3)));
            break;
        case 'faq':
        case 'testimonials':
            $content['kicker'] = trim($_POST['kicker'] ?? '');
            $content['heading'] = trim($_POST['heading'] ?? '');
            $content['subtitle'] = trim($_POST['subtitle'] ?? '');
            break;
        case 'cta':
            $content['kicker'] = trim($_POST['kicker'] ?? '');
            $content['heading'] = trim($_POST['heading'] ?? '');
            $content['subtitle'] = trim($_POST['subtitle'] ?? '');
            $content['button_text'] = trim($_POST['button_text'] ?? '');
            $content['button_link'] = trim($_POST['button_link'] ?? '');
            break;
    }

    $json = json_encode($content);
    $stmt = mysqli_prepare($conn, "UPDATE page_sections SET content = ? WHERE id = ? AND page_id = ?");
    mysqli_stmt_bind_param($stmt, "sii", $json, $section_id, $page_id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: page-builder.php?page_id=" . $page_id . "&updated=1");
        exit;
    } else {
        $error_msg = "Could not update section: " . mysqli_error($conn);
    }
}

// ---------- DELETE / DUPLICATE SECTION ----------
if (isset($_GET['delete_section'])) {
    $section_id = (int) $_GET['delete_section'];
    $stmt = mysqli_prepare($conn, "DELETE FROM page_sections WHERE id = ? AND page_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $section_id, $page_id);
    mysqli_stmt_execute($stmt);
    header("Location: page-builder.php?page_id=" . $page_id . "&deleted=1");
    exit;
}

if (isset($_GET['duplicate_section'])) {
    $section_id = (int) $_GET['duplicate_section'];
    $src_stmt = mysqli_prepare($conn, "SELECT * FROM page_sections WHERE id = ? AND page_id = ?");
    mysqli_stmt_bind_param($src_stmt, "ii", $section_id, $page_id);
    mysqli_stmt_execute($src_stmt);
    $src = mysqli_stmt_get_result($src_stmt)->fetch_assoc();

    if ($src) {
        $order_res = mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM page_sections WHERE page_id = " . (int) $page_id);
        $next_order = mysqli_fetch_assoc($order_res)['next_order'];

        $ins = mysqli_prepare($conn, "INSERT INTO page_sections (page_id, section_type, content, sort_order) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($ins, "issi", $page_id, $src['section_type'], $src['content'], $next_order);
        mysqli_stmt_execute($ins);
        $new_id = mysqli_insert_id($conn);

        $child_map = ['cards' => 'page_section_cards', 'faq' => 'page_section_faqs', 'testimonials' => 'page_section_testimonials'];
        if (isset($child_map[$src['section_type']])) {
            $table = $child_map[$src['section_type']];
            $child_res = mysqli_query($conn, "SELECT * FROM $table WHERE section_id = " . (int) $section_id . " ORDER BY sort_order ASC");
            while ($child = mysqli_fetch_assoc($child_res)) {
                unset($child['id']);
                $child['section_id'] = $new_id;
                $cols = implode(",", array_keys($child));
                $placeholders = implode(",", array_fill(0, count($child), "?"));
                $types = str_repeat("s", count($child));
                $vals = array_values($child);
                $cstmt = mysqli_prepare($conn, "INSERT INTO $table ($cols) VALUES ($placeholders)");
                mysqli_stmt_bind_param($cstmt, $types, ...$vals);
                mysqli_stmt_execute($cstmt);
            }
        }
    }
    header("Location: page-builder.php?page_id=" . $page_id . "&duplicated=1");
    exit;
}

// ---------- CARD ITEMS ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_card'])) {
    $section_id = (int) $_POST['section_id'];
    $img = bx_upload_image('image', $upload_dir, $allowed_ext, 'card');
    $n = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS n FROM page_section_cards WHERE section_id = " . $section_id))['n'];
    $title = trim($_POST['title'] ?? ''); $desc = trim($_POST['description'] ?? ''); $link = trim($_POST['link'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $stmt = mysqli_prepare($conn, "INSERT INTO page_section_cards (section_id, title, description, image, icon, link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssssi", $section_id, $title, $desc, $img, $icon, $link, $n);
    mysqli_stmt_execute($stmt);
    header("Location: page-builder.php?page_id=" . $page_id); exit;
}
if (isset($_GET['delete_card'])) {
    $id = (int) $_GET['delete_card'];
    $stmt = mysqli_prepare($conn, "DELETE FROM page_section_cards WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: page-builder.php?page_id=" . $page_id); exit;
}

// ---------- FAQ ITEMS ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_faq'])) {
    $section_id = (int) $_POST['section_id'];
    $n = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS n FROM page_section_faqs WHERE section_id = " . $section_id))['n'];
    $q = trim($_POST['question'] ?? ''); $a = trim($_POST['answer'] ?? '');
    $stmt = mysqli_prepare($conn, "INSERT INTO page_section_faqs (section_id, question, answer, sort_order) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issi", $section_id, $q, $a, $n);
    mysqli_stmt_execute($stmt);
    header("Location: page-builder.php?page_id=" . $page_id); exit;
}
if (isset($_GET['delete_faq'])) {
    $id = (int) $_GET['delete_faq'];
    $stmt = mysqli_prepare($conn, "DELETE FROM page_section_faqs WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: page-builder.php?page_id=" . $page_id); exit;
}

// ---------- TESTIMONIAL ITEMS ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_testimonial'])) {
    $section_id = (int) $_POST['section_id'];
    $img = bx_upload_image('image', $upload_dir, $allowed_ext, 'test');
    $n = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS n FROM page_section_testimonials WHERE section_id = " . $section_id))['n'];
    $name = trim($_POST['name'] ?? ''); $desig = trim($_POST['designation'] ?? '');
    $review = trim($_POST['review'] ?? ''); $rating = (int) ($_POST['rating'] ?? 5);
    $stmt = mysqli_prepare($conn, "INSERT INTO page_section_testimonials (section_id, name, designation, image, rating, review, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssisi", $section_id, $name, $desig, $img, $rating, $review, $n);
    mysqli_stmt_execute($stmt);
    header("Location: page-builder.php?page_id=" . $page_id); exit;
}
if (isset($_GET['delete_testimonial'])) {
    $id = (int) $_GET['delete_testimonial'];
    $stmt = mysqli_prepare($conn, "DELETE FROM page_section_testimonials WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: page-builder.php?page_id=" . $page_id); exit;
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

$sections = [];
$sec_stmt = mysqli_prepare($conn, "SELECT * FROM page_sections WHERE page_id = ? ORDER BY sort_order ASC");
mysqli_stmt_bind_param($sec_stmt, "i", $page_id);
mysqli_stmt_execute($sec_stmt);
$sec_result = mysqli_stmt_get_result($sec_stmt);
while ($row = mysqli_fetch_assoc($sec_result)) {
    $row['content_decoded'] = json_decode($row['content'] ?? '{}', true) ?: [];
    $row['items'] = [];
    $child_map = ['cards' => 'page_section_cards', 'faq' => 'page_section_faqs', 'testimonials' => 'page_section_testimonials'];
    if (isset($child_map[$row['section_type']])) {
        $table = $child_map[$row['section_type']];
        $ci = mysqli_prepare($conn, "SELECT * FROM $table WHERE section_id = ? ORDER BY sort_order ASC");
        mysqli_stmt_bind_param($ci, "i", $row['id']);
        mysqli_stmt_execute($ci);
        $cr = mysqli_stmt_get_result($ci);
        while ($c = mysqli_fetch_assoc($cr)) $row['items'][] = $c;
    }
    $sections[] = $row;
}
?>
<div class="main-content">
<div class="container-fluid" style="padding:24px;">
    <a href="pages.php" style="text-decoration:none; color:#0f2565; font-weight:600; font-size:14px;"><i class="fa-solid fa-arrow-left"></i> Back to Pages</a>
    <h3 style="margin:10px 0 20px;">Sections — <?php echo htmlspecialchars($page_row['title']); ?>
        <a href="../index.php?page=<?php echo urlencode($page_row['slug']); ?>" target="_blank" class="btn btn-sm" style="background:#eef1f5; color:#0f2565;"><i class="fa-solid fa-eye"></i> Preview</a>
    </h3>

    <?php if ($success_msg): ?><div class="alert alert-success"><?php echo $success_msg; ?></div><?php endif; ?>
    <?php if ($error_msg): ?><div class="alert alert-danger"><?php echo $error_msg; ?></div><?php endif; ?>

    <div id="sectionsList">
        <?php foreach ($sections as $s): ?>
            <div class="card mb-3 section-item" data-id="<?php echo $s['id']; ?>" style="border-radius:14px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa-solid fa-grip-vertical drag-handle" style="cursor:grab; margin-right:10px; color:#aaa;"></i>
                        <i class="<?php echo $section_labels[$s['section_type']]['icon'] ?? 'fa-solid fa-cube'; ?>"></i>
                        <strong><?php echo $section_labels[$s['section_type']]['label'] ?? $s['section_type']; ?></strong>
                        <span style="color:#8A93A6; font-size:13px;">— <?php echo htmlspecialchars($s['content_decoded']['heading'] ?? ($s['content_decoded']['kicker'] ?? '')); ?></span>
                    </div>
                    <div>
                        <button class="btn btn-sm" style="background:#eef1f5;" data-bs-toggle="modal" data-bs-target="#editSection<?php echo $s['id']; ?>"><i class="fa-solid fa-pen"></i> Edit</button>
                        <a href="?page_id=<?php echo $page_id; ?>&duplicate_section=<?php echo $s['id']; ?>" class="btn btn-sm" style="background:#eef1f5;"><i class="fa-solid fa-copy"></i></a>
                        <a href="?page_id=<?php echo $page_id; ?>&delete_section=<?php echo $s['id']; ?>" class="btn btn-sm" style="background:#fdeaea; color:#c0392b;" onclick="return confirm('Delete this section?');"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
            </div>

            <!-- Edit modal -->
            <div class="modal fade" id="editSection<?php echo $s['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content" style="border-radius:14px;">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="section_id" value="<?php echo $s['id']; ?>">
                            <input type="hidden" name="section_type" value="<?php echo $s['section_type']; ?>">
                            <div class="modal-header"><h5>Edit — <?php echo $section_labels[$s['section_type']]['label']; ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <?php include('section/fields/' . $s['section_type'] . '.php'); ?>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="edit_section" class="btn" style="background:#0B1E3F; color:#fff;">Save</button>
                            </div>
                        </form>

                        <?php if (in_array($s['section_type'], ['cards', 'faq', 'testimonials'])): ?>
                        <div class="p-3 border-top">
                            <h6>Items</h6>
                            <?php include('section/items/' . $s['section_type'] . '.php'); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button class="btn" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addSectionModal"><i class="fa-solid fa-plus"></i> Add Section</button>
</div>
</div>

<!-- Add section: type picker -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <div class="modal-header"><h5>Choose a section type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body d-flex flex-wrap gap-2">
                <?php foreach ($section_labels as $key => $lbl): ?>
                    <button type="button" class="btn" style="background:#f6f7f9; flex:1 1 45%;" data-bs-toggle="modal" data-bs-target="#newSection_<?php echo $key; ?>" data-bs-dismiss="modal">
                        <i class="<?php echo $lbl['icon']; ?>"></i> <?php echo $lbl['label']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add section: one modal per type -->
<?php foreach ($section_labels as $key => $lbl): ?>
<div class="modal fade" id="newSection_<?php echo $key; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="section_type" value="<?php echo $key; ?>">
                <div class="modal-header"><h5>Add — <?php echo $lbl['label']; ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <?php $s = ['content_decoded' => []]; include('section/fields/' . $key . '.php'); ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_section" class="btn" style="background:#0B1E3F; color:#fff;">Add Section</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
document.addEventListener('shown.bs.modal', function (e) {
    e.target.querySelectorAll('textarea.html-editor-field').forEach(function (ta) {
        if (CKEDITOR.instances[ta.id]) return;
        CKEDITOR.replace(ta.id, {
            height: 300,
            toolbarGroups: [
                { name: 'clipboard', groups: ['clipboard', 'undo'] },
                { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                { name: 'links' },
                { name: 'insert' },
                '/',
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align'] },
                { name: 'styles' },
                { name: 'colors' },
                { name: 'tools' }
            ],
            removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Flash,Smiley,PageBreak,Iframe,ShowBlocks,About'
        });
    });
});

document.addEventListener('submit', function (e) {
    if (!(e.target instanceof HTMLFormElement)) return;
    e.target.querySelectorAll('textarea.html-editor-field').forEach(function (ta) {
        if (CKEDITOR.instances[ta.id]) CKEDITOR.instances[ta.id].updateElement();
    });
});
</script>

<!-- Drag & drop reorder -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
new Sortable(document.getElementById('sectionsList'), {
    handle: '.drag-handle',
    animation: 150,
    onEnd: function () {
        var order = [];
        document.querySelectorAll('#sectionsList .section-item').forEach(function (el) {
            order.push(el.dataset.id);
        });
        fetch('save-section-order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'page_id=<?php echo $page_id; ?>&order=' + encodeURIComponent(JSON.stringify(order))
        });
    }
});
</script>

</body>
</html>