<?php
require 'config.php'; require 'functions.php'; require_admin();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $id=(int)$_POST['id']; $status=$_POST['status'] ?? '';
    if (!in_array($status,['Pending','Approved','Rejected'],true)) { flash('Invalid status.','danger'); }
    else {
        $s=$pdo->prepare('UPDATE bookings SET status=? WHERE id=?'); $s->execute([$status,$id]);
        if ($status==='Approved') {
            $s=$pdo->prepare('SELECT room_id FROM bookings WHERE id=?'); $s->execute([$id]); $room_id=$s->fetchColumn();
            if ($room_id) { $s=$pdo->prepare("UPDATE rooms SET status='Full' WHERE id=?"); $s->execute([$room_id]); }
        }
        flash('Booking status updated.');
    }
    header('Location: admin_bookings.php'); exit;
}
$rows=$pdo->query('SELECT b.id,b.status,b.created_at,u.name,u.email,r.room_number,r.room_type FROM bookings b JOIN users u ON u.id=b.user_id JOIN rooms r ON r.id=b.room_id ORDER BY b.created_at DESC')->fetchAll();
$page_title='Manage Bookings'; require 'header.php';
?>
<h2 class="mb-3">Manage Room Requests</h2><div class="table-responsive"><table class="table table-striped align-middle"><thead><tr><th>Student</th><th>Email</th><th>Room</th><th>Requested</th><th>Status</th><th>Update</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['room_number']) ?> (<?= e($r['room_type']) ?>)</td><td><?= e($r['created_at']) ?></td><td><?= e($r['status']) ?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($r['id']) ?>"><select class="form-select form-select-sm" name="status"><?php foreach(['Pending','Approved','Rejected'] as $st): ?><option <?= $r['status']===$st?'selected':'' ?>><?= e($st) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-primary">Save</button></form></td></tr><?php endforeach; ?>
</tbody></table></div><?php if(!$rows): ?><p>No booking requests yet.</p><?php endif; ?>
<?php require 'footer.php'; ?>
