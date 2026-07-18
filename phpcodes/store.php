<?php


include "header.php";
require_once "functions.php";

function find_field($row, $candidates, $default = null){
    foreach($candidates as $c){
        if(array_key_exists($c, $row) && $row[$c] !== null && $row[$c] !== ''){
            return $row[$c];
        }
    }
    return $default;
}

function esc_f($v){ return function_exists('esc') ? esc($v) : htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$res = $conn->query("SELECT * FROM products ORDER BY id DESC");
if($res === false){
    echo '<div class="page-pad"><div class="card"><h3>Error loading products</h3><p>Check your products table and database connection.</p></div></div>';
    include "footer.php";
    exit;
}
?>

<style>
.store-wrap{ padding:28px 60px; }
.store-title{ font-size:28px;margin-bottom:14px;color:#223030;padding-left:6px;}
.store-grid{
  display:grid;
  grid-template-columns: repeat(4, 1fr);
  gap:20px;
}

/* product card */
.product-card{
  background:#fff;
  border-radius:10px;
  border:1px solid #e6e2dd;
  padding:12px;
  display:flex;
  flex-direction:column;
  min-height:360px;
}
.product-media{
  width:100%;
  height:220px;
  border-radius:6px;
  overflow:hidden;
  background:#f6f6f6;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom:12px;
}
.product-media img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}
.product-name{ font-weight:700; margin-bottom:6px; color:#222;}
.product-meta{ color:#666; font-size:13px; margin-bottom:8px; }
.product-price{ font-weight:800; margin-bottom:10px; color:#333; }

.product-actions{ margin-top:auto; display:flex; align-items:center; gap:8px; }
.product-actions input[type="number"]{ width:80px;padding:8px;border:1px solid #ddd;border-radius:6px; }
.product-actions button{ background:#523D35;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer; }

@media (max-width:1100px){
  .store-wrap{ padding:18px 28px; }
  .product-media{ height:180px; }
}
@media (max-width:900px){
  .store-grid{ grid-template-columns: repeat(2,1fr); }
}
@media (max-width:520px){
  .store-grid{ grid-template-columns: 1fr; }
  .product-media{ height:220px; }
}
</style>

<div class="store-wrap">
  <h2 class="store-title">Store</h2>

  <div class="store-grid">
    <?php while($p = $res->fetch_assoc()): ?>

      <?php
        $id            = (int) find_field($p, ['id', 'product_id', 'pid'], 0);
        $name          = find_field($p, ['name', 'product_name', 'title'], 'Untitled Product');
        $category      = find_field($p, ['category', 'cat', 'category_name'], 'Uncategorized');
        $priceRaw      = find_field($p, ['price', 'product_price', 'unit_price', 'amount'], null);
        $stockRaw      = find_field($p, ['stock', 'quantity', 'qty'], null);
        $imageFilename = find_field($p, ['image_filename', 'image', 'img', 'photo'], null);

        if($priceRaw === null || $priceRaw === ''){
            $priceText = '—';
        } elseif(is_numeric($priceRaw)){
            $priceText = '₱' . number_format((float)$priceRaw, 2);
        } else {
            $priceText = esc_f($priceRaw);
        }

        $stockText = ($stockRaw !== null ? intval($stockRaw) : 'N/A');

        $imagePath = null;
        if($imageFilename){
            $candidate = __DIR__ . '/uploads/products/' . $imageFilename;
            if(is_file($candidate)){
                $imagePath = 'uploads/products/' . rawurlencode($imageFilename);
            } else {
                if(stripos($imageFilename, 'http') === 0 || str_starts_with($imageFilename, '/')){
                    $imagePath = $imageFilename;
                } elseif(is_file(__DIR__ . '/' . $imageFilename)){
                    $imagePath = $imageFilename;
                } else {
                    $imagePath = null;
                }
            }
        }
        $placeholder = 'assets/img/placeholder-1x1.png';
      ?>

      <div class="product-card">
        <div class="product-media">
          <?php if($imagePath): ?>
            <img src="<?= esc_f($imagePath) ?>" alt="<?= esc_f($name) ?>">
          <?php else: ?>
            <img src="<?= esc_f($placeholder) ?>" alt="placeholder">
          <?php endif; ?>
        </div>

        <div class="product-name"><?= esc_f($name) ?></div>
        <div class="product-meta">Category: <?= esc_f($category) ?></div>
        <div class="product-price"><?= $priceText ?></div>

        <form method="post" action="cart.php" style="margin-top:auto;">
          <div class="product-actions">
            <input type="number" name="qty" value="1" min="1">
            <input type="hidden" name="product_id" value="<?= $id ?>">
            <button type="submit" name="add_to_cart">Add to Cart</button>
          </div>
        </form>
      </div>

    <?php endwhile; ?>
  </div>
</div>

<?php include "footer.php"; ?>