<?php
include "header.php"; require_once "functions.php";
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid=$_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['product_id'])){
  $pid=(int)$_POST['product_id']; $qty=max(1,(int)$_POST['qty']);
  $c=$conn->prepare("SELECT id,quantity FROM cart_items WHERE user_id=? AND product_id=?");
  $c->bind_param("ii",$uid,$pid); $c->execute(); $r=$c->get_result()->fetch_assoc();
  if($r){
    $n=$r['quantity']+$qty;
    $u=$conn->prepare("UPDATE cart_items SET quantity=? WHERE id=?");
    $u->bind_param("ii",$n,$r['id']); $u->execute();
  }else{
    $i=$conn->prepare("INSERT INTO cart_items(user_id,product_id,quantity) VALUES(?,?,?)");
    $i->bind_param("iii",$uid,$pid,$qty); $i->execute();
  }
  logActivity($conn,$uid,"Added to cart product #$pid");
  header("Location: cart.php"); exit;
}

if(isset($_GET['remove'])){
  $id=(int)$_GET['remove'];
  $d=$conn->prepare("DELETE FROM cart_items WHERE id=? AND user_id=?");
  $d->bind_param("ii",$id,$uid); $d->execute();
  header("Location: cart.php"); exit;
}
?>
<div class="card">
<h2>Cart</h2>
<table>
<tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr>
<?php
$total=0;
$st=$conn->prepare("SELECT ci.id cid, ci.quantity, p.name, p.price FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.user_id=?");
$st->bind_param("i",$uid); $st->execute(); $res=$st->get_result();
while($r=$res->fetch_assoc()):
$sub=$r['price']*$r['quantity']; $total+=$sub;
?>
<tr>
<td><?=esc($r['name'])?></td>
<td>₱<?=number_format($r['price'],2)?></td>
<td><?=$r['quantity']?></td>
<td>₱<?=number_format($sub,2)?></td>
<td><a class="btn" href="cart.php?remove=<?=$r['cid']?>">Remove</a></td>
</tr>
<?php endwhile; ?>
</table>
<h3>Total: ₱<?=number_format($total,2)?></h3>
<a class="btn" href="checkout.php">Proceed to Checkout</a>
</div>
<?php include "footer.php"; ?>