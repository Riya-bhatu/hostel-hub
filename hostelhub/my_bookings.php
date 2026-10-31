<?php
require 'config.php'; require 'functions.php'; require_login();
if (($_SESSION['role'] ?? '')==='admin') { header('Location: admin_bookings.php'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $s=$pdo->prepare("DELETE FROM bookings WHERE id=? AND user_id=? AND status='Pending'");
    $s->execute([(int)$_POST['id'],current_user_id()]); flash($s->rowCount()?'Pending request cancelled.':'Only your pending requests can be cancelled.','info');
    header('Location: my_bookings.php'); exit;
}
$s=$pdo->prepare('SELECT b.id,b.status,b.created_at,r.room_number,r.room_type,r.monthly_fee FROM bookings b JOIN rooms r ON r.id=b.room_id WHERE b.user_id=? ORDER BY b.created_at DESC');
$s->execute([current_user_id()]); $rows=$s->fetchAll();
$page_title='My Bookings'; require 'header.php';
?>
<h2 class="mb-3">My Room Requests</h2><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Room</th><th>Type</th><th>Monthly fee</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['room_number']) ?></td><td><?= e($r['room_type']) ?></td><td>₹<?= number_format((float)$r['monthly_fee'],2) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['created_at']) ?></td><td><?php if($r['status']==='Pending'): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($r['id']) ?>"><button class="btn btn-sm btn-outline-danger">Cancel</button></form><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php if(!$rows): ?><p>You have not requested a room yet. <a href="rooms.php">Browse rooms</a>.</p><?php endif; ?>
<?php require 'footer.php'; ?>
