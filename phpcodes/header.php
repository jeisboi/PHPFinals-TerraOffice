<?php require_once "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>TerraOffice</title>
<style>
:root{
  --c1:#223030; --c2:#523D35; --c4:#BBA58F; --c5:#E8D9CD; --c6:#EFEFE9;
}
*{ box-sizing:border-box; }
body{
  margin:0;
  font-family:Arial, Helvetica, sans-serif;
  background:var(--c6);
  color:var(--c1);
}
h1,h2,h3,h4,h5,h6,p{ margin:0; }

/* full width layout */
.container{
  width:100%;
  max-width:none;
  margin:0;
  padding:0;
}

/* shared components */
.page-pad{ padding:20px; }
.card{
  background:#fff;
  border:1px solid #ddd;
  border-radius:10px;
  padding:15px;
  margin-bottom:15px;
}
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:12px;
}
input,select,textarea{
  width:100%;
  padding:8px;
  margin:6px 0 12px;
  border:1px solid #ccc;
  border-radius:6px;
}
button,.btn{
  background:var(--c2);
  color:#fff;
  border:none;
  padding:8px 12px;
  border-radius:6px;
  text-decoration:none;
  cursor:pointer;
  display:inline-block;
}
table{
  width:100%;
  border-collapse:collapse;
  background:#fff;
}
th,td{
  border:1px solid #ddd;
  padding:8px;
}
th{ background:var(--c4); }

/* HEADER */
.main-header{
  background:#fff;
  border-bottom:1px solid #e7e2dd;
  padding:8px 14px;
}
.header-row{
  display:grid;
  grid-template-columns: 200px 1fr auto;
  align-items:center;
  gap:10px;
}

/* left search */
.search-area{
  display:flex;
  align-items:center;
}
.search-area input{
  margin:0;
  width:180px;
  height:34px;
  padding:0 12px;
  border:1px solid #bfc5c1;
  border-radius:4px;
  background:#fff;
  color:#223030;
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.5px;
  outline:none;
}
.search-area input:focus{
  border-color:#523D35;
  box-shadow:0 0 0 2px rgba(82,61,53,.12);
}

.brand-center{
  display:flex;
  justify-content:center;
  align-items:center;
  min-width:0;
}
.brand-wordmark-link{
  display:inline-block;
  text-decoration:none;
  line-height:0;
}
.brand-wordmark{
  display:block;
  width:auto;
  height:52px;
  max-width:100%;
  object-fit:contain;
}

.nav-right{
  display:flex;
  justify-content:flex-end;
  align-items:center;
  flex-wrap:wrap;
  gap:6px;
}
.nav-right a{
  text-decoration:none;
  color:var(--c1);
  font-weight:700;
  font-size:15px;
  padding:7px 9px;
  border-radius:6px;
}
.nav-right a:hover{ background:#f4efea; }
.admin-btn{
  background:#523D35;
  color:#fff !important;
}
.admin-btn:hover{ background:#6b5248 !important; }

footer{
  background:var(--c5);
  padding:14px 10px;
  text-align:center;
  margin-top:0;
}

@media(max-width:1000px){
  .header-row{
    grid-template-columns:1fr;
    gap:8px;
  }
  .search-area{ justify-content:center; }
  .nav-right{ justify-content:center; }
  .brand-wordmark{ height:44px; }
}
@media(max-width:600px){
  .brand-wordmark{ height:34px; }
}
</style>
</head>
<body>

<header class="main-header">
  <div class="header-row">

    <div class="search-area">
      <input type="text" placeholder="SEARCH">
    </div>

    <div class="brand-center">
      <a href="index.php" class="brand-wordmark-link">
        <img src="logo-new.png?v=2" alt="TerraOffice" class="brand-wordmark">
      </a>
    </div>

    <nav class="nav-right">
      <a href="index.php">Home</a>
      <a href="store.php">Store</a>
      <a href="cart.php">Cart</a>
      <a href="about.php">About</a>

      <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin_dashboard.php" class="admin-btn">Admin Dashboard</a>
      <?php elseif(isset($_SESSION['user_id']) && $_SESSION['role'] === 'staff'): ?>
        <a href="staff_dashboard.php" class="admin-btn">Staff Dashboard</a>
      <?php endif; ?>

      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>

  </div>
</header>

<div class="container">