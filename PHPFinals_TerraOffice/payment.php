<?php
include "header.php"; require_once "functions.php";
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid=$_SESSION['user_id']; $oid=(int)($_GET['order_id'] ?? 0);

$st=$conn->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$st->bind_param("ii",$oid,$uid); $st->execute(); $o=$st->get_result()->fetch_assoc();
if(!$o){ echo "<div class='card'>Order not found.</div>"; include "footer.php"; exit; }

if($_SERVER['REQUEST_METHOD']=="POST"){
  $up=$conn->prepare("UPDATE orders SET status='Paid' WHERE id=? AND user_id=?");
  $up->bind_param("ii",$oid,$uid); $up->execute();
  logActivity($conn,$uid,"Marked order #$oid as Paid");
  header("Location: payment.php?order_id=$oid"); exit;
}
?>
<div class="card">
<h2>Payment</h2>
<p>Order #: <?=$o['id']?></p>
<p>Status: <?=$o['status']?></p>
<p>Total: ₱<?=number_format($o['total_amount'],2)?></p>
<p>Payment Method: <?=esc($o['payment_method'])?></p>
<p><strong>Note:</strong> No payment API yet (as required).</p>
<?php if($o['status']!='Paid'): ?>
<form method="post"><button type="submit">I Have Paid</button></form>
<?php else: ?><p>Payment recorded.</p><?php endif; ?>
</div>
<?php include "footer.php"; ?>