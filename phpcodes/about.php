<?php
include "header.php";
require_once "functions.php";
function esc_f($v){ return function_exists('esc') ? esc($v) : htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>

<style>
.about-hero{
  padding:28px 60px;
}
.about-intro{
  background:#fff;
  border-radius:8px;
  padding:18px;
  border:1px solid #e6e2dd;
  margin-bottom:18px;
}
.about-intro h2{ font-size:24px; color:#223030; margin-bottom:6px; }
.about-intro p{ color:#555; line-height:1.5; }

.meet-title{ text-align:center; font-size:22px; margin:26px 0 18px; color:#333; }
.team-grid{
  display:flex;
  gap:40px;
  justify-content:center;
  flex-wrap:wrap;
  padding:0 60px 40px;
}
.team-card{
  background:#fff;
  border-radius:6px;
  border:1px solid #eae8e4;
  overflow:hidden;
  width:320px;
  padding:18px;
  text-align:center;
}
.team-photo{
  width:160px;
  height:160px;
  margin:0 auto 12px;
  border-radius:50%;
  overflow:hidden;
  border:6px solid #fff;
  box-shadow:0 6px 18px rgba(0,0,0,.08);
}
.team-photo img{
  width:100%;
  height:100%;
  object-fit:cover;
}
.team-name{ font-weight:800; margin-bottom:6px; color:#222; }
.team-role{ color:#e05a5a; font-weight:700; margin-bottom:10px; }
.team-bio{ color:#666; font-size:14px; line-height:1.45; }

/* responsive */
@media(max-width:900px){
  .about-hero{ padding:18px; }
  .team-grid{ padding:0 18px; gap:18px; }
  .team-card{ width:100%; max-width:640px; display:flex; gap:16px; align-items:center; text-align:left; }
  .team-photo{ width:110px; height:110px; flex:0 0 110px; }
  .team-info{ flex:1; }
}
</style>

<div class="about-hero">
  <div class="about-intro">
    <h2>About TerraOffice</h2>
    <p>TerraOffice is a student-built office equipment website with a cozy, earthy aesthetic. We provide chairs, tables, and office essentials designed for comfort and productivity.</p>
  </div>

  <div class="meet-title">Meet the Team</div>

  <div class="team-grid">
    <div class="team-card">
      <div class="team-photo"><img src="jeis.jpg" alt="Jeirmaine"></div>
      <div class="team-name">Jeirmaine Christian Vanta</div>
      <div class="team-role">Project Lead & Backend</div>
      <div class="team-bio">BSIT student at FEU Institute of Technology. Loves backend development and systems design.</div>
    </div>

    <div class="team-card">
      <div class="team-photo"><img src="kent.jpg" alt="John"></div>
      <div class="team-name">John Kent Uriarte</div>
      <div class="team-role">Front-end & UI</div>
      <div class="team-bio">BSIT student at FEU Institute of Technology. Focuses on web UI and full-stack development.</div>
    </div>

  </div>
</div>

<?php include "footer.php"; ?>