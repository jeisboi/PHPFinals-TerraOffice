<?php
include "header.php"; require_once "functions.php";
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid=$_SESSION['user_id'];

$u=$conn->prepare("SELECT address FROM users WHERE id=?");
$u->bind_param("i",$uid); $u->execute(); $user=$u->get_result()->fetch_assoc();

$items=$conn->prepare("SELECT ci.product_id,ci.quantity,p.price,p.stock,p.name FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.user_id=?");
$items->bind_param("i",$uid); $items->execute(); $res=$items->get_result();

$data=[]; $total=0;
while($r=$res->fetch_assoc()){ $data[]=$r; $total += $r['price']*$r['quantity']; }

if(!$data){ echo "<div class='card'>Cart is empty.</div>"; include "footer.php"; exit; }

if($_SERVER['REQUEST_METHOD']=="POST"){
  $ship=$_POST['shipping_address']; $pm=$_POST['payment_method'];
  $conn->begin_transaction();
  try{
    $o=$conn->prepare("INSERT INTO orders(user_id,total_amount,payment_method,shipping_address) VALUES(?,?,?,?)");
    $o->bind_param("idss",$uid,$total,$pm,$ship); $o->execute();
    $oid=$conn->insert_id;

    $oi=$conn->prepare("INSERT INTO order_items(order_id,product_id,price,quantity) VALUES(?,?,?,?)");
    $up=$conn->prepare("UPDATE products SET stock=stock-? WHERE id=?");
    foreach($data as $d){
      $oi->bind_param("iidi",$oid,$d['product_id'],$d['price'],$d['quantity']); $oi->execute();
      $up->bind_param("ii",$d['quantity'],$d['product_id']); $up->execute();
    }

    $cl=$conn->prepare("DELETE FROM cart_items WHERE user_id=?");
    $cl->bind_param("i",$uid); $cl->execute();

    $conn->commit();
    logActivity($conn,$uid,"Checkout order #$oid");
    header("Location: payment.php?order_id=".$oid); exit;
  }catch(Exception $e){ $conn->rollback(); echo "<div class='card'>Checkout failed.</div>"; }
}
?>
<div class="card">
<h2>Checkout</h2>
<form method="post">
  <textarea name="shipping_address" required><?=esc($user['address'])?></textarea>
  <select name="payment_method" required>
    <option>Manual Payment</option>
    <option>GCash</option>
    <option>Cash on Delivery</option>
  </select>
  <button type="submit">Place Order</button>
</form>
<h3>Total: ₱<?=number_format($total,2)?></h3>
</div>
<?php include "footer.php"; ?>