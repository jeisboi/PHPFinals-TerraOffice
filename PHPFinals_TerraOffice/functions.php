<?php
function esc($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function logActivity($conn,$userId,$activity){
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
  $st = $conn->prepare("INSERT INTO audit_logs(user_id,activity,ip_address) VALUES(?,?,?)");
  $st->bind_param("iss",$userId,$activity,$ip);
  $st->execute();
}
?>