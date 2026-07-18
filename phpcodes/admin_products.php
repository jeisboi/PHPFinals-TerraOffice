<?php
include "header.php";
require_once "functions.php";

if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin','staff'], true)){
    die("Access denied");
}

function esc_f($v){ return function_exists('esc') ? esc($v) : htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$CATEGORY_OPTIONS = [
  "Chairs",
  "Desks & Tables",
  "Storage",
  "Accessories",
  "Electronics",
  "Office Supplies",
  "Lighting",
  "Ergonomic",
  "Décor & Comfort",
  "Clearance",
  "Uncategorized"
];

$dbok = $conn->query("SELECT DATABASE()")->fetch_row()[0] ?? null;
if(!$dbok){
    die("DB connection problem.");
}

$col = $conn->query("SELECT COUNT(*) c FROM information_schema.COLUMNS WHERE table_schema=DATABASE() AND table_name='products' AND column_name='image_filename'")->fetch_assoc()['c'] ?? 0;
if((int)$col === 0){
    @$conn->query("ALTER TABLE `products` ADD COLUMN `image_filename` VARCHAR(255) NULL AFTER `price`");
}

$col2 = $conn->query("SELECT COUNT(*) c FROM information_schema.COLUMNS WHERE table_schema=DATABASE() AND table_name='products' AND column_name='category'")->fetch_assoc()['c'] ?? 0;
if((int)$col2 === 0){
    @$conn->query("ALTER TABLE `products` ADD COLUMN `category` VARCHAR(120) NULL AFTER `name`");
}

$uploadDir = __DIR__ . '/uploads/products/';
if(!is_dir($uploadDir)){
    @mkdir($uploadDir, 0755, true);
}

function unique_filename($ext){
    if(function_exists('random_bytes')){
        return time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    }
    return time() . '_' . uniqid() . '.' . $ext;
}

function handle_image_upload($inputName, $existing = null){
    global $uploadDir;
    if(!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE){
        return $existing;
    }
    $file = $_FILES[$inputName];
    if($file['error'] !== UPLOAD_ERR_OK){
        throw new Exception("Upload error code: " . $file['error']);
    }
    $info = @getimagesize($file['tmp_name']);
    if($info === false){
        throw new Exception("File is not a valid image.");
    }
    $map = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
    if(!isset($map[$info['mime']])){
        throw new Exception("Unsupported image type. Use JPG/PNG/GIF/WEBP.");
    }
    $ext = $map[$info['mime']];
    $fname = unique_filename($ext);
    $dst = $uploadDir . $fname;
    if(!move_uploaded_file($file['tmp_name'], $dst)){
        throw new Exception("Failed to move uploaded file.");
    }
    @chmod($dst, 0644);
    if($existing && $existing !== $fname){
        $old = $uploadDir . $existing;
        if(is_file($old)) @unlink($old);
    }
    return $fname;
}

$messages = []; $errors = [];

if(isset($_POST['create_product'])){
    $name = trim($_POST['name'] ?? '');
    $rawCategory = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if(!in_array($rawCategory, $CATEGORY_OPTIONS, true)){
        $category = 'Uncategorized';
    } else {
        $category = $rawCategory;
    }

    if($name === '' || $price <= 0){
        $errors[] = "Name and a valid price are required.";
    } else {
        try {
            $img = null;
            if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE){
                $img = handle_image_upload('product_image', null);
            }
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, image_filename) VALUES (?,?,?,?,?)");
            if(!$stmt) throw new Exception("DB prepare failed.");
            $stmt->bind_param("ssdis", $name, $category, $price, $stock, $img);
            if($stmt->execute()){
                $messages[] = "Product created.";
            } else {
                if($img && is_file($uploadDir.$img)) @unlink($uploadDir.$img);
                $errors[] = "Insert failed.";
            }
        } catch(Exception $ex){
            $errors[] = $ex->getMessage();
        }
    }
}

if(isset($_POST['update_product'])){
    $pid = (int)($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $rawCategory = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if(!in_array($rawCategory, $CATEGORY_OPTIONS, true)){
        $category = 'Uncategorized';
    } else {
        $category = $rawCategory;
    }

    if($pid <= 0 || $name === '' || $price <= 0){
        $errors[] = "Invalid input.";
    } else {
        $cur = $conn->prepare("SELECT image_filename FROM products WHERE id=? LIMIT 1");
        $cur->bind_param("i",$pid); $cur->execute();
        $row = $cur->get_result()->fetch_assoc();
        $existing = $row['image_filename'] ?? null;

        try {
            $img = $existing;
            if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE){
                $img = handle_image_upload('product_image', $existing);
            }
            $upd = $conn->prepare("UPDATE products SET name=?, category=?, price=?, stock=?, image_filename=? WHERE id=?");
            if(!$upd) throw new Exception("DB prepare failed.");
            $upd->bind_param("ssdisi", $name, $category, $price, $stock, $img, $pid);
            if($upd->execute()){
                $messages[] = "Product updated.";
            } else {
                $errors[] = "Update failed.";
            }
        } catch(Exception $ex){
            $errors[] = $ex->getMessage();
        }
    }
}

if(isset($_POST['delete_product'])){
    $pid = (int)($_POST['delete_product'] ?? 0);
    if($pid > 0){
        $cur = $conn->prepare("SELECT image_filename FROM products WHERE id=? LIMIT 1");
        $cur->bind_param("i",$pid); $cur->execute();
        $row = $cur->get_result()->fetch_assoc();
        $filename = $row['image_filename'] ?? null;

        try {
            $del = $conn->prepare("DELETE FROM products WHERE id=? LIMIT 1");
            $del->bind_param("i",$pid); $del->execute();
            if($del->affected_rows > 0){
                if($filename && is_file($uploadDir.$filename)) @unlink($uploadDir.$filename);
                $messages[] = "Product deleted.";
            } else {
                $errors[] = "Delete failed (maybe referenced elsewhere).";
            }
        } catch(Exception $ex){
            $errors[] = $ex->getMessage();
        }
    } else {
        $errors[] = "Invalid product id.";
    }
}

$editProduct = null;
if(isset($_GET['edit'])){
    $eid = (int)$_GET['edit'];
    $s = $conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
    $s->bind_param("i",$eid); $s->execute();
    $editProduct = $s->get_result()->fetch_assoc() ?: null;
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<div class="page-pad">
  <div class="card">
    <h2>Products Management</h2>

    <?php foreach($messages as $m): ?>
      <div style="margin:10px 0;padding:10px;border:1px solid #9fd3a8;background:#eefaf0;color:#1f6b2c;border-radius:6px;">
        <?= esc_f($m) ?>
      </div>
    <?php endforeach; ?>

    <?php foreach($errors as $e): ?>
      <div style="margin:10px 0;padding:10px;border:1px solid #e3b3b3;background:#fff0f0;color:#8a1f1f;border-radius:6px;">
        <?= esc_f($e) ?>
      </div>
    <?php endforeach; ?>

    <h3 style="margin-top:12px;"><?= $editProduct ? "Edit Product" : "Create Product" ?></h3>

    <form method="post" enctype="multipart/form-data" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:10px;">
      <input type="hidden" name="product_id" value="<?= $editProduct ? (int)$editProduct['id'] : '' ?>">

      <div>
        <label>Name</label>
        <input type="text" name="name" required value="<?= $editProduct ? esc_f($editProduct['name'] ?? '') : '' ?>">
      </div>

      <div>
        <label>Category</label>
        <select name="category" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;">
          <?php $currentCategoryText = $editProduct['category'] ?? ''; ?>
          <?php foreach($CATEGORY_OPTIONS as $opt): ?>
            <option value="<?= esc_f($opt) ?>" <?= (strcasecmp($currentCategoryText, $opt) === 0) ? 'selected' : '' ?>><?= esc_f($opt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label>Price</label>
        <input type="number" step="0.01" name="price" required value="<?= $editProduct ? esc_f($editProduct['price'] ?? '') : '' ?>">
      </div>

      <div>
        <label>Stock</label>
        <input type="number" name="stock" value="<?= $editProduct ? esc_f($editProduct['stock'] ?? 0) : 0 ?>">
      </div>

      <div>
        <label>Product Image (JPG/PNG/GIF/WEBP)</label>
        <input type="file" name="product_image" accept="image/*">
        <?php if($editProduct && !empty($editProduct['image_filename'])): ?>
          <div style="margin-top:8px;">
            <small>Current:</small><br>
            <img src="uploads/products/<?= esc_f($editProduct['image_filename']) ?>" alt="" style="max-width:120px;border:1px solid #ddd;padding:4px;border-radius:6px;margin-top:6px;">
          </div>
        <?php endif; ?>
      </div>

      <div style="align-self:end;">
        <?php if($editProduct): ?>
          <button type="submit" name="update_product">Update Product</button>
          <a href="admin_products.php" style="display:inline-block;margin-left:8px;text-decoration:none;padding:8px 10px;background:#eee;border-radius:6px;color:#222;">Cancel</a>
        <?php else: ?>
          <button type="submit" name="create_product">Create Product</button>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="card" style="margin-top:18px;">
    <h3>Products List</h3>
    <div style="overflow:auto;margin-top:12px;">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th style="width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while($p = $products->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$p['id'] ?></td>
            <td>
              <?php if(!empty($p['image_filename']) && is_file(__DIR__ . '/uploads/products/' . $p['image_filename'])): ?>
                <img src="uploads/products/<?= esc_f($p['image_filename']) ?>" alt="" style="width:80px;height:60px;object-fit:cover;border-radius:6px;">
              <?php else: ?>
                <img src="assets/img/placeholder-1x1.png" alt="" style="width:80px;height:60px;object-fit:cover;border-radius:6px;">
              <?php endif; ?>
            </td>
            <td><?= esc_f($p['name'] ?? '') ?></td>
            <td><?= esc_f($p['category'] ?? '') ?></td>
            <td>₱<?= number_format((float)($p['price'] ?? 0),2) ?></td>
            <td><?= (int)($p['stock'] ?? 0) ?></td>
            <td>
              <a href="admin_products.php?edit=<?= (int)$p['id'] ?>" style="padding:6px 8px;background:#3b6ea5;color:#fff;border-radius:6px;text-decoration:none;">Edit</a>
              <form method="post" style="display:inline-block;margin-left:6px;" onsubmit="return confirm('Delete this product?');">
                <button type="submit" name="delete_product" value="<?= (int)$p['id'] ?>" style="background:#b63d3d;padding:6px 8px;color:#fff;border-radius:6px;border:none;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>