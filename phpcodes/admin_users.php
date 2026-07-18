<?php
include "header.php";
require_once "functions.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("Admin only");
}

$msg = "";
$err = "";

/* CREATE admin/staff */
if(isset($_POST['create_user'])){
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? 'staff';

    if($full_name === '' || $email === '' || $password === ''){
        $err = "Please fill in all required fields.";
    } elseif(!in_array($role, ['admin','staff'], true)){
        $err = "Invalid role selected.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        if($check->get_result()->num_rows > 0){
            $err = "Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (full_name,email,password_hash,role) VALUES (?,?,?,?)");
            $ins->bind_param("ssss", $full_name, $email, $hash, $role);
            if($ins->execute()){
                $msg = ucfirst($role) . " account created successfully.";
            } else {
                $err = "Failed to create user.";
            }
        }
    }
}

/* DELETE user */
if(isset($_POST['delete_user'])){
    $delete_id = (int)($_POST['delete_id'] ?? 0);
    $my_id     = (int)$_SESSION['user_id'];

    if($delete_id <= 0){
        $err = "Invalid user selected.";
    } elseif($delete_id === $my_id){
        $err = "You cannot delete your own account while logged in.";
    } else {
        // Get target user
        $uStmt = $conn->prepare("SELECT id, role, full_name FROM users WHERE id=? LIMIT 1");
        $uStmt->bind_param("i", $delete_id);
        $uStmt->execute();
        $target = $uStmt->get_result()->fetch_assoc();

        if(!$target){
            $err = "User not found.";
        } else {
            // Prevent deleting last admin
            if($target['role'] === 'admin'){
                $aCount = (int)$conn->query("SELECT COUNT(*) c FROM users WHERE role='admin'")->fetch_assoc()['c'];
                if($aCount <= 1){
                    $err = "Cannot delete the last admin account.";
                }
            }

            if($err === ""){
                try {
                    $del = $conn->prepare("DELETE FROM users WHERE id=? LIMIT 1");
                    $del->bind_param("i", $delete_id);
                    $del->execute();

                    if($del->affected_rows > 0){
                        $msg = "User deleted successfully.";
                    } else {
                        $err = "Delete failed.";
                    }
                } catch (mysqli_sql_exception $ex) {
                    // FK-safe message instead of fatal error
                    $err = "Cannot delete this user because there are related records (e.g., orders). "
                         . "You can keep the account, or reassign/remove related records first.";
                }
            }
        }
    }
}

/* LIST admin/staff users */
$list = $conn->query("SELECT id, full_name, email, role, created_at FROM users WHERE role IN ('admin','staff') ORDER BY id DESC");
?>

<div class="page-pad">
  <div class="card">
    <h2 style="margin-bottom:12px;">Manage Admin/Staff Users</h2>

    <?php if($msg): ?>
      <div style="margin:10px 0;padding:10px;border:1px solid #9fd3a8;background:#eefaf0;color:#1f6b2c;border-radius:6px;">
        <?= function_exists('esc') ? esc($msg) : htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <?php if($err): ?>
      <div style="margin:10px 0;padding:10px;border:1px solid #e3b3b3;background:#fff0f0;color:#8a1f1f;border-radius:6px;">
        <?= function_exists('esc') ? esc($err) : htmlspecialchars($err) ?>
      </div>
    <?php endif; ?>

    <h3 style="margin:10px 0;">Create New Admin/Staff</h3>
    <form method="post" class="grid" style="align-items:end;">
      <div>
        <label>Full Name</label>
        <input type="text" name="full_name" required>
      </div>
      <div>
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div>
        <label>Password</label>
        <input type="text" name="password" required>
      </div>
      <div>
        <label>Role</label>
        <select name="role" required>
          <option value="staff">Staff</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div>
        <button type="submit" name="create_user">Create User</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h3 style="margin-bottom:10px;">Admin / Staff Accounts</h3>
    <div style="overflow:auto;">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
            <th style="width:120px;">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while($u = $list->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= function_exists('esc') ? esc($u['full_name']) : htmlspecialchars($u['full_name']) ?></td>
            <td><?= function_exists('esc') ? esc($u['email']) : htmlspecialchars($u['email']) ?></td>
            <td><?= function_exists('esc') ? esc($u['role']) : htmlspecialchars($u['role']) ?></td>
            <td><?= function_exists('esc') ? esc($u['created_at']) : htmlspecialchars($u['created_at']) ?></td>
            <td>
              <?php if((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                <form method="post" onsubmit="return confirm('Delete this user?');">
                  <input type="hidden" name="delete_id" value="<?= (int)$u['id'] ?>">
                  <button type="submit" name="delete_user" style="background:#b63d3d;">Delete</button>
                </form>
              <?php else: ?>
                <span style="opacity:.65;">Current user</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>