<?php
include("include/db-connect.php");
include("include/auth-check.php");

$success_msg = "";
$error_msg = "";
$upload_dir = "../imgs/";
$allowed_ext = ["jpg", "jpeg", "png", "webp"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_model'])) {
    $brand = trim($_POST['brand']);
    $model_name = trim($_POST['model_name']);

    if ($brand === '' || $model_name === '') {
        $error_msg = "Brand and model name are required.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "Please upload an image.";
    } else {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            $error_msg = "Only jpg, jpeg, png, webp images are allowed.";
        } else {
            $filename = uniqid('model_') . '.' . $ext;
            $target_path = $upload_dir . $filename;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $error_msg = "Could not upload the image. Check that admin/../imgs/ is writable.";
            } else {
                $image_path = "imgs/" . $filename;
                $stmt = mysqli_prepare($conn, "INSERT INTO models (brand, model_name, image) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sss", $brand, $model_name, $image_path);

                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Model '$model_name' added successfully.";
                } else {
                    $error_msg = "Could not add model: " . mysqli_error($conn);
                }
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $find = mysqli_prepare($conn, "SELECT image FROM models WHERE id = ?");
    mysqli_stmt_bind_param($find, "i", $id);
    mysqli_stmt_execute($find);
    $found = mysqli_stmt_get_result($find);
    $row = mysqli_fetch_assoc($found);

    if ($row) {
        $del = mysqli_prepare($conn, "DELETE FROM models WHERE id = ?");
        mysqli_stmt_bind_param($del, "i", $id);

        if (mysqli_stmt_execute($del)) {
            $image_file = __DIR__ . "/../" . $row['image'];
            if ($row['image'] && file_exists($image_file)) {
                unlink($image_file);
            }
            header("Location: models.php?deleted=1");
            exit;
        } else {
            $error_msg = "Delete failed: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "Model not found.";
    }
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

$brand_filter = $_GET['brand'] ?? null;

if ($brand_filter) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM models WHERE brand = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "s", $brand_filter);
    mysqli_stmt_execute($stmt);
    $models_result = mysqli_stmt_get_result($stmt);
} else {
    $models_result = mysqli_query($conn, "SELECT * FROM models ORDER BY id DESC");
}

$models_list = [];
if ($models_result) {
    while ($row = mysqli_fetch_assoc($models_result)) {
        $models_list[] = $row;
    }
}
?>

<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Models</h1>
            <p class="current-date">Manage phone models shown on the website</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Model was deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $error_msg; ?></div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('addModelModal')).show();
            });
        </script>
    <?php endif; ?>

    <div class="d-flex justify-content-end mb-4">
        <button type="button" class="btn" style="background-color:#0B1E3F; color:#fff;"
            data-bs-toggle="modal" data-bs-target="#addModelModal">
            <i class="fa-solid fa-plus me-1"></i> Add New Model
        </button>
    </div>

    <!-- Add Model Modal -->
    <div class="modal fade" id="addModelModal" tabindex="-1" aria-labelledby="addModelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="models.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addModelModalLabel">
                            <i class="fa-solid fa-plus me-2"></i>Add new model
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Brand</label>
                                <select name="brand" class="form-select-brand form-select-app" required>
                                    <option value="Apple">Apple</option>
                                    <option value="Samsung">Samsung</option>
                                    <option value="oneplus">One Plus</option>
                                    <option value="pixel">Google Pixel</option>
                                
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Model name</label>
                                <input type="text" name="model_name" class="form-control"
                                    placeholder="e.g. iPhone 15 Pro" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_model" class="btn"
                            style="background:#0B1E3F; color:#fff;">Add Model</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        #modelsGrid .col {
            display: flex;
        }
        #modelsGrid .card {
            width: 100%;
        }
        #modelsGrid .card img.card-img-top {
            height: 160px;
            width: 100%;
            object-fit: contain;
            background: #f9fafb;
            padding: 10px;
        }
    </style>

    <div class="input-group flex-nowrap mb-3" style="max-width:320px;">
        <span class="input-group-text" style="background:#f9fafb;"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="modelSearchInput" class="form-control" placeholder="Search by model name or brand...">
    </div>

    <div class="filter-tabs mb-3">
        <a href="models.php" class="filter-tab <?php echo !$brand_filter ? 'active' : ''; ?>">All</a>
        <a href="models.php?brand=Apple"
            class="filter-tab <?php echo ($brand_filter === 'Apple') ? 'active' : ''; ?>">Apple</a>
        <a href="models.php?brand=Samsung"
            class="filter-tab <?php echo ($brand_filter === 'Samsung') ? 'active' : ''; ?>">Samsung</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4" id="modelsGrid">
        <?php if (empty($models_list)): ?>
            <p class="text-muted">No models found. Add one above.</p>
        <?php else: ?>
            <?php foreach ($models_list as $m): ?>
                <div class="col model-card-col"
                     data-search="<?php echo htmlspecialchars(strtolower($m['model_name'] . ' ' . $m['brand'])); ?>">
                    <div class="card h-60">
                        <div></div>
                        <img src="../<?php echo htmlspecialchars($m['image']); ?>" 
                        class="card-img-top" alt="<?php echo htmlspecialchars($m['model_name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($m['model_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($m['brand']); ?></p>
                            <a href="models.php?delete=<?php echo (int) $m['id']; ?>"
                               class="btn btn-sm delete-btn"
                               style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this model? Its pricing will also be removed.');">
                               <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <p id="modelsNoResults" class="text-muted" style="display:none;">No models match your search.</p>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var searchInput = document.getElementById('modelSearchInput');
            var cards = document.querySelectorAll('.model-card-col');
            var noResults = document.getElementById('modelsNoResults');
            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                var query = searchInput.value.trim().toLowerCase();
                var visibleCount = 0;

                cards.forEach(function (card) {
                    var match = card.getAttribute('data-search').indexOf(query) !== -1;
                    card.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                noResults.style.display = (visibleCount === 0) ? '' : 'none';
            });
        });
    </script>

</div>
</body>

</html>