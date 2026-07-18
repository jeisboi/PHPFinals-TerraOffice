<?php
include "header.php";
require_once "functions.php";

if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin','staff'])){
    die("No access");
}

$r = $conn->query("
    SELECT a.*, u.full_name
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.id DESC
");
?>

<div class="card">
  <h2>Audit Log Report</h2>
  <table>
    <tr>
      <th>Date</th>
      <th>User</th>
      <th>Activity</th>
      <th>IP</th>
    </tr>
    <?php while($x = $r->fetch_assoc()): ?>
      <tr>
        <td><?= esc($x['created_at']) ?></td>
        <td><?= esc($x['full_name'] ?? 'Unknown') ?></td>
        <td><?= esc($x['activity']) ?></td>
        <td><?= esc($x['ip_address']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

<?php include "footer.php"; ?>