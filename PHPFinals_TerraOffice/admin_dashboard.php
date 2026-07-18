<?php
include "header.php";
require_once "functions.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("Admin only");
}

$uid = (int)$_SESSION['user_id'];

/* Full name */
$meStmt = $conn->prepare("SELECT full_name FROM users WHERE id=? LIMIT 1");
$meStmt->bind_param("i", $uid);
$meStmt->execute();
$meRow = $meStmt->get_result()->fetch_assoc();
$fullName = $meRow ? $meRow['full_name'] : 'Admin';

/* Counts */
$pCount = (int)$conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];
$uCount = (int)$conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$oCount = (int)$conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
?>

<style>
.dash-wrap{
  padding:28px 26px 10px;
}
.dash-top{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:10px;
  margin-bottom:20px;
}
.dash-title{
  font-size:34px;
  margin:0;
  color:#223030;
}
.dash-sub{
  margin-top:6px;
  color:#6f7470;
  font-size:14px;
}
.manage-users-btn{
  background:#523D35;
  color:#fff;
  text-decoration:none;
  padding:10px 14px;
  border-radius:8px;
  font-weight:700;
}
.manage-users-btn:hover{ opacity:.92; }

.dash-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
  gap:18px;
}
.dash-card{
  display:block;
  text-decoration:none;
  color:#fff;
  background:linear-gradient(135deg,#959D90,#5E6C5B);
  border-radius:16px;
  padding:20px 22px;
  min-height:120px;
  box-shadow:0 10px 25px rgba(28,120,93,.20);
}
.dash-card:hover{
  transform:translateY(-2px);
  transition:.2s ease;
}
.dash-card .label{
  font-size:24px;
  margin:0 0 22px;
  font-weight:700;
}
.dash-card .value{
  font-size:38px;
  font-weight:800;
  letter-spacing:.5px;
}
.dash-card.small .label{ font-size:20px; }
.dash-card.small .value{ font-size:20px; font-weight:700; }

@media(max-width:800px){
  .dash-title{font-size:28px;}
  .dash-top{flex-direction:column;align-items:flex-start;}
}
</style>

<div class="dash-wrap">
  <div class="dash-top">
    <div>
      <h2 class="dash-title">Welcome, <?= esc($fullName) ?>.</h2>
      <div class="dash-sub"><?= date("l, F j, Y • g:i A") ?></div>
    </div>

    <a class="manage-users-btn" href="admin_users.php">Manage Admin/Staff Users</a>
  </div>

  <div class="dash-grid">
    <a class="dash-card" href="admin_products.php">
      <p class="label">Manage Products / Stock / Prices</p>
      <div class="value"><?= $pCount ?></div>
    </a>

    <a class="dash-card" href="inventory_report.php">
      <p class="label">Inventory Report</p>
      <div class="value"><?= $pCount ?></div>
    </a>

    <a class="dash-card" href="audit_log.php">
      <p class="label">Audit Log Report</p>
      <div class="value"><?= $oCount ?></div>
    </a>
  </div>
</div>

<?php include "footer.php"; ?>