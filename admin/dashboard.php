<?php
require_once '../includes/functions.php'; require_admin();
$stats=[
'users'=>db()->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
'lost'=>db()->query("SELECT COUNT(*) FROM posts WHERE type='lost'")->fetchColumn(),
'found'=>db()->query("SELECT COUNT(*) FROM posts WHERE type='found'")->fetchColumn(),
'open'=>db()->query("SELECT COUNT(*) FROM posts WHERE status='open'")->fetchColumn()
];
$recent=db()->query("SELECT p.*,u.name owner_name FROM posts p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC LIMIT 8")->fetchAll();
$page_title='Admin Dashboard'; include '../includes/header.php';
?>
<div class="section-head"><div><h2>Admin Dashboard</h2><p class="muted">Manage the campus lost & found system.</p></div></div>
<div class="stats"><div class="stat"><small>Students</small><strong><?=$stats['users']?></strong></div><div class="stat"><small>Lost Posts</small><strong><?=$stats['lost']?></strong></div><div class="stat"><small>Found Posts</small><strong><?=$stats['found']?></strong></div><div class="stat"><small>Open Posts</small><strong><?=$stats['open']?></strong></div></div>
<div class="section-head"><h2>Quick Management</h2></div><a class="btn btn-primary" href="<?=BASE_URL?>admin/users.php">Manage Users</a> <a class="btn btn-outline" href="<?=BASE_URL?>admin/posts.php">Manage Posts</a>
<div class="section-head"><h2>Recent Posts</h2></div><div class="table-wrap"><table class="table"><tr><th>Item</th><th>Type</th><th>Student</th><th>Status</th><th>Date</th></tr><?php foreach($recent as $p):?><tr><td><?=e($p['title'])?></td><td><?=e($p['type'])?></td><td><?=e($p['owner_name'])?></td><td><?=e($p['status'])?></td><td><?=e(date('d M Y',strtotime($p['created_at'])))?></td></tr><?php endforeach;?></table></div>
<?php include '../includes/footer.php';?>