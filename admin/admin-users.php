<?php
include("include/db-connect.php");
include("include/auth-check.php");

$success_msg = "";
$error_msg = "";
$pwd_success_msg = "";
$pwd_error_msg = "";
$edit_success_msg = "";
$edit_error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = trim($_POST['username']);
    $admin_email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($username === '' || $admin_email === '' || $password === '') {
        $error_msg = "Username, email and password are required.";
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Password and Confirm Password do not match.";
    } elseif (strlen($password) < 8) {
        $error_msg = "Password must be at least 8 characters long.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM admin_user WHERE username = ? OR admin_email = ?");
        mysqli_stmt_bind_param($check, "ss", $username, $admin_email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error_msg = "This username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO admin_user (username, admin_email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $admin_email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "New admin '$username' was added successfully.";
            } else {
                $error_msg = "Could not add admin: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($check);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_admin'])) {
    $edit_id = (int) $_POST['edit_id'];
    $new_username = trim($_POST['edit_username']);
    $new_email = trim($_POST['edit_email']);

    if ($new_username === '' || $new_email === '') {
        $edit_error_msg = "Username and email are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $edit_error_msg = "Please enter a valid email address.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM admin_user WHERE (username = ? OR admin_email = ?) AND id != ?");
        mysqli_stmt_bind_param($check, "ssi", $new_username, $new_email, $edit_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $edit_error_msg = "That username or email is already used by another admin.";
        } else {
            $old_username_stmt = mysqli_prepare($conn, "SELECT username FROM admin_user WHERE id = ?");
            mysqli_stmt_bind_param($old_username_stmt, "i", $edit_id);
            mysqli_stmt_execute($old_username_stmt);
            $old_row = mysqli_fetch_assoc(mysqli_stmt_get_result($old_username_stmt));
            $old_username = $old_row['username'] ?? null;

            $update = mysqli_prepare($conn, "UPDATE admin_user SET username = ?, admin_email = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "ssi", $new_username, $new_email, $edit_id);

            if (mysqli_stmt_execute($update)) {
                $edit_success_msg = "Admin details updated successfully.";
                if ($old_username !== null && ($_SESSION['admin_username'] ?? null) === $old_username) {
                    $_SESSION['admin_username'] = $new_username;
                }
            } else {
                $edit_error_msg = "Could not update admin: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($check);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';
    $current_username = $_SESSION['admin_username'] ?? null;

    if (!$current_username) {
        $pwd_error_msg = "Session expired. Please log in again.";
    } elseif ($current_password === '' || $new_password === '') {
        $pwd_error_msg = "All password fields are required.";
    } elseif ($new_password !== $confirm_new_password) {
        $pwd_error_msg = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 8) {
        $pwd_error_msg = "New password must be at least 8 characters long.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, password FROM admin_user WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $current_username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $me = mysqli_fetch_assoc($result);

        if (!$me || !password_verify($current_password, $me['password'])) {
            $pwd_error_msg = "Current password is incorrect.";
        } else {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = mysqli_prepare($conn, "UPDATE admin_user SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "si", $new_hashed, $me['id']);

            if (mysqli_stmt_execute($update)) {
                $pwd_success_msg = "Your password was updated successfully.";
            } else {
                $pwd_error_msg = "Could not update password: " . mysqli_error($conn);
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $current_username = $_SESSION['admin_username'] ?? null;

    $self_check = mysqli_prepare($conn, "SELECT username FROM admin_user WHERE id = ?");
    mysqli_stmt_bind_param($self_check, "i", $id);
    mysqli_stmt_execute($self_check);
    $self_result = mysqli_stmt_get_result($self_check);
    $target = mysqli_fetch_assoc($self_result);

    if (!$target) {
        $error_msg = "Admin not found (id: $id).";
    } elseif ($current_username !== null && $target['username'] === $current_username) {
        $error_msg = "You cannot delete your own currently logged-in account.";
    } else {
        $del = mysqli_prepare($conn, "DELETE FROM admin_user WHERE id = ?");
        mysqli_stmt_bind_param($del, "i", $id);
        if (mysqli_stmt_execute($del)) {
            header("Location: admin-users.php?deleted=1");
            exit;
        } else {
            $error_msg = "Delete failed: " . mysqli_error($conn);
        }
    }
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

$admins = [];
$result = mysqli_query($conn, "SELECT * FROM admin_user ORDER BY id ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
}
?>

<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Admin Users</h1>
            <p class="current-date">Manage dashboard admin accounts</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Admin was deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $error_msg; ?></div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('addAdminModal')).show();
            });
        </script>
    <?php endif; ?>
    <?php if ($pwd_success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $pwd_success_msg; ?></div>
    <?php endif; ?>
    <?php if ($pwd_error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $pwd_error_msg; ?></div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
            });
        </script>
    <?php endif; ?>
    <?php if ($edit_success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $edit_success_msg; ?></div>
    <?php endif; ?>
    <?php if ($edit_error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $edit_error_msg; ?></div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var reopenId = <?php echo isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : 0; ?>;
                var el = document.getElementById('editAdminModal' + reopenId);
                if (el) new bootstrap.Modal(el).show();
            });
        </script>
    <?php endif; ?>

    <div class="d-flex justify-content-end mb-4" style="gap:10px;">
        <button type="button" class="btn" style="background:#eef1f5; color:#0B1E3F; border:1px solid #dfe4ea;"
            data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="fa-solid fa-key me-1"></i> Change My Password
        </button>
        <button type="button" class="btn" style="background-color:#0B1E3F; color:#fff;"
            data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="fa-solid fa-user-plus me-1"></i> Add New Admin
        </button>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="admin-users.php">
                    <div class="modal-header" style="border-bottom:1px solid #eef1f5;">
                        <h1 class="modal-title fs-5" id="addAdminModalLabel" style="color:#0B1E3F;">
                            <i class="fa-solid fa-user-plus me-2"></i>Add new admin
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required minlength="8">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #eef1f5;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_admin" class="btn"
                            style="background:#0B1E3F; color:#fff;">Add Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="admin-users.php">
                    <div class="modal-header" style="border-bottom:1px solid #eef1f5;">
                        <h1 class="modal-title fs-5" id="changePasswordModalLabel" style="color:#0B1E3F;">
                            <i class="fa-solid fa-key me-2"></i>Change my password
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:12.5px; color:#797979c5;">Current password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Current password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:12.5px; color:#797979c5;">New password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="New password" required minlength="8">
                        </div>
                        <div class="mb-1">
                            <label class="form-label" style="font-size:12.5px; color:#797979c5;">Confirm new password</label>
                            <input type="password" name="confirm_new_password" class="form-control" placeholder="Confirm new password" required minlength="8">
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #eef1f5;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="change_password" class="btn"
                            style="background:#0B1E3F; color:#fff;">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Admin Modals (one per row) -->
    <?php foreach ($admins as $a): ?>
        <div class="modal fade" id="editAdminModal<?php echo (int) $a['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="admin-users.php">
                        <input type="hidden" name="edit_id" value="<?php echo (int) $a['id']; ?>">
                        <div class="modal-header" style="border-bottom:1px solid #eef1f5;">
                            <h1 class="modal-title fs-5" style="color:#0B1E3F;">
                                <i class="fa-solid fa-pen me-2"></i>Edit admin
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Username</label>
                                <input type="text" name="edit_username" class="form-control"
                                    value="<?php echo htmlspecialchars($a['username']); ?>" required>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Email</label>
                                <input type="email" name="edit_email" class="form-control"
                                    value="<?php echo htmlspecialchars($a['admin_email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top:1px solid #eef1f5;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit_admin" class="btn"
                                style="background:#0B1E3F; color:#fff;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="table-responsive" style="border:1px solid #eef1f5; border-radius:14px;">
        <table class="table align-middle mb-0" style="font-size:13px;">
            <thead style="background:#f9fafb;">
                <tr>
                    <th style="color:#0B1E3F;">ID</th>
                    <th style="color:#0B1E3F;">Username</th>
                    <th style="color:#0B1E3F;">Email</th>
                    <th class="text-center" style="color:#0B1E3F;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admins)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">No admins found.</td></tr>
                <?php else: ?>
                    <?php foreach ($admins as $a): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['id']); ?></td>
                            <td class="fw-semibold" style="color:#333;"><?php echo htmlspecialchars($a['username']); ?></td>
                            <td style="color:#555;"><?php echo htmlspecialchars($a['admin_email'] ?? '—'); ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm"
                                    style="background:#eef1f5; color:#0B1E3F; border:none;"
                                    data-bs-toggle="modal" data-bs-target="#editAdminModal<?php echo (int) $a['id']; ?>">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <a href="admin-users.php?delete=<?php echo (int) $a['id']; ?>"
                                   class="btn btn-sm delete-btn"
                                   style="background:#fdeaea; color:#c0392b; border:none;"
                                   onclick="return confirm('Are you sure you want to delete this admin?');">
                                   <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>