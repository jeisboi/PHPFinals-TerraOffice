<?php
include "header.php";
require_once "functions.php";

$msg = "";

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if($email === '' || $pass === ''){
        $msg = "Please enter email and password.";
    } else {
        $st = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $st->bind_param("s", $email);
        $st->execute();
        $user = $st->get_result()->fetch_assoc();

        if($user && password_verify($pass, $user['password_hash'])){
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['name']    = $user['full_name'];

            if(function_exists('logActivity')){
                logActivity($conn, (int)$user['id'], "User logged in");
            }

            if($user['role'] === 'admin'){
                header("Location: admin_dashboard.php");
                exit;
            } elseif($user['role'] === 'staff'){
                header("Location: staff_dashboard.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $msg = "Invalid email or password.";
        }
    }
}
?>

<div class="page-pad">
  <div class="card" style="max-width:420px;margin:30px auto;">
    <h2 style="margin-bottom:14px;">Login</h2>

    <?php if($msg !== ""): ?>
      <div style="margin-bottom:12px;padding:10px;border:1px solid #d9a9a9;background:#ffefef;color:#7f0000;border-radius:6px;">
        <?= function_exists('esc') ? esc($msg) : htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
    </form>
  </div>
</div>

<?php include "footer.php"; ?>