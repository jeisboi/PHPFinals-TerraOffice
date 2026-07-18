<?php include "header.php"; ?>

<style>
.hero{
  position:relative;
  width:100%;
  height:calc(100vh - 82px);
  min-height:520px;
  background:url('home_office.jpg') center center/cover no-repeat;
  margin:0;
  border:0;
  border-radius:0;
  overflow:hidden;
}
.hero::before{
  content:"";
  position:absolute; inset:0;
  background:linear-gradient(to right, rgba(25,20,16,0.36), rgba(25,20,16,0.05));
}
.hero-content{
  position:absolute;
  left:30px;
  top:40px;
  color:#fff;
  z-index:2;
}
.hero-content small{
  text-transform:uppercase;
  letter-spacing:1.1px;
  font-size:12px;
}
.hero-content h1{
  font-family:Georgia, "Times New Roman", serif;
  font-size:110px;
  line-height:0.95;
  margin:10px 0 16px;
  font-weight:600;
}
.hero-content .shop-btn{
  display:inline-block;
  background:#fff;
  color:#223030;
  text-decoration:none;
  padding:11px 18px;
  font-weight:700;
  border-radius:2px;
}
@media(max-width:900px){
  .hero{height:70vh;min-height:430px;}
  .hero-content{left:18px;top:28px;}
  .hero-content h1{font-size:46px;line-height:1.02;}
}
.search-area{
  display:flex;
  align-items:center;
}

.search-area input{
  margin:0;
  width:180px;
  height:34px;
  padding:0 12px;
  border:1px solid #bfc5c1;   /* outlined box */
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
</style>

<section class="hero">
  <div class="hero-content">
    <small><br><br><br><br><br><br><br>The Most Trusted Office Equipment Store.</small><br><br>
    <h1>Shop with us.<br>Shop with class.</h1><br>
    <a href="store.php" class="shop-btn">VIEW ITEMS</a>
  </div>
</section>

<?php include "footer.php"; ?>