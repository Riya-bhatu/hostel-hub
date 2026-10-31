<?php
require 'config.php'; require 'functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_admin(); verify_csrf();
    $action=$_POST['action'] ?? '';
    if ($action==='save') {
        $id=(int)($_POST['id'] ?? 0); $room=trim($_POST['room_number'] ?? '');
        $type=trim($_POST['room_type'] ?? ''); $capacity=max(1,(int)($_POST['capacity'] ?? 1));
        $fee=max(0,(float)($_POST['monthly_fee'] ?? 0)); $status=$_POST['status'] ?? 'Available';
        if (!in_array($status,['Available','Full','Maintenance'],true) || $room==='' || $type==='') {
            flash('Enter valid room details.','danger');
        } elseif ($id) {
            $s=$pdo->prepare('UPDATE rooms SET room_number=?,room_type=?,capacity=?,monthly_fee=?,status=? WHERE id=?');
            $s->execute([$room,$type,$capacity,$fee,$status,$id]); flash('Room updated.');
        } else {
            $s=$pdo->prepare('INSERT INTO rooms (room_number,room_type,capacity,monthly_fee,status) VALUES (?,?,?,?,?)');
            $s->execute([$room,$type,$capacity,$fee,$status]); flash('Room added.');
        }
    } elseif ($action==='delete') {
        $s=$pdo->prepare('DELETE FROM rooms WHERE id=?'); $s->execute([(int)$_POST['id']]); flash('Room deleted if it had no booking history.','info');
    }
    header('Location: rooms.php'); exit;
}
$edit=null;
if (isset($_GET['edit'])) { require_admin(); $s=$pdo->prepare('SELECT * FROM rooms WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$rooms=$pdo->query('SELECT * FROM rooms ORDER BY room_number')->fetchAll();
$page_title='Rooms'; require 'header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>Hostel Rooms</h2><?php if (($_SESSION['role'] ?? '')==='admin'): ?><span class="badge bg-primary">Admin management enabled</span><?php endif; ?></div>
<?php if (($_SESSION['role'] ?? '')==='admin'): ?>
<div class="card mb-4"><div class="card-body"><h5><?= $edit?'Update room':'Add a room' ?></h5>
<form method="post" class="row g-2"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
<div class="col-md-2"><input class="form-control" name="room_number" placeholder="Room no." value="<?= e($edit['room_number'] ?? '') ?>" required></div>
<div class="col-md-2"><input class="form-control" name="room_type" placeholder="Type" value="<?= e($edit['room_type'] ?? '') ?>" required></div>
<div class="col-md-2"><input class="form-control" type="number" min="1" name="capacity" placeholder="Capacity" value="<?= e($edit['capacity'] ?? 2) ?>" required></div>
<div class="col-md-2"><input class="form-control" type="number" min="0" step="0.01" name="monthly_fee" placeholder="Monthly fee" value="<?= e($edit['monthly_fee'] ?? '') ?>" required></div>
<div class="col-md-2"><select class="form-select" name="status"><?php foreach(['Available','Full','Maintenance'] as $st): ?><option <?= (($edit['status'] ?? 'Available')===$st)?'selected':'' ?>><?= e($st) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><button class="btn btn-primary w-100">Save</button></div></form></div></div>
<?php endif; ?>
<div class="row g-3">
<?php foreach($rooms as $r): ?>
<div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><div class="d-flex justify-content-between"><h5>Room <?= e($r['room_number']) ?></h5><span class="badge bg-<?= $r['status']==='Available'?'success':($r['status']==='Full'?'secondary':'warning') ?>"><?= e($r['status']) ?></span></div><p class="mb-1">Type: <?= e($r['room_type']) ?></p><p class="mb-1">Capacity: <?= e($r['capacity']) ?></p><p>Monthly fee: ₹<?= number_format((float)$r['monthly_fee'],2) ?></p>
<?php if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '')==='student' && $r['status']==='Available'): ?><form method="post" action="book.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="room_id" value="<?= e($r['id']) ?>"><button class="btn btn-primary btn-sm">Request this room</button></form><?php elseif (empty($_SESSION['user_id'])): ?><a class="btn btn-outline-primary btn-sm" href="login.php">Login to request</a><?php endif; ?>
<?php if (($_SESSION['role'] ?? '')==='admin'): ?><div class="mt-2 d-flex gap-2"><a class="btn btn-outline-secondary btn-sm" href="rooms.php?edit=<?= e($r['id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this room?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($r['id']) ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></form></div><?php endif; ?>
</div></div></div>
<?php endforeach; ?>
<?php if (!$rooms): ?><p>No rooms have been added yet.</p><?php endif; ?>
</div>
<?php require 'footer.php'; ?>
