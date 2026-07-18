<?php
include "header.php";
require_once "functions.php";

if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin','staff'])){
    die("No access");
}

$r = $conn->query("SELECT name, stock, price FROM products ORDER BY stock ASC");
?>

<div class="card">
  <h2>Inventory Report (Remaining Items)</h2>
  <table>
    <tr>
      <th>Product</th>
      <th>Stock</th>
      <th>Price</th>
    </tr>
    <?php while($x = $r->fetch_assoc()): ?>
      <tr>
        <td><?= esc($x['name']) ?></td>
        <td><?= (int)$x['stock'] ?></td>
        <td>₱<?= number_format($x['price'], 2) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

<?php include "footer.php"; ?>