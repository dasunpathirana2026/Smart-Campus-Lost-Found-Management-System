<?php
require_once '../includes/functions.php'; require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id=filter_var($_POST['user_id']??null,FILTER_VALIDATE_INT);
  $action=post_text('action');
  if(!valid_csrf_token() || $id===false || $id===null || $id<1 || !in_array($action,['delete','toggle'],true)){
    flash('error','The request could not be verified. Please try again.');
    redirect(BASE_URL.'admin/users.php');
  }
  if($action==='delete'){
    $stmt=db()->prepare("DELETE FROM users WHERE id=? AND role='student'");
    $stmt->execute([$id]);
    $changed=$stmt->rowCount()>0;
    flash($changed?'success':'error',$changed?'User removed.':'That student account could not be found.');
  }else{
    $stmt=db()->prepare("UPDATE users SET status=IF(status='active','blocked','active') WHERE id=? AND role='student'");
    $stmt->execute([$id]);
    $changed=$stmt->rowCount()>0;
    flash($changed?'success':'error',$changed?'User status updated.':'That student account could not be found.');
  }
  redirect(BASE_URL.'admin/users.php');
}
$users=db()->query("SELECT id,name,email,phone,status,created_at FROM users WHERE role='student' ORDER BY created_at DESC")->fetchAll();
$page_title='Manage Users'; include '../includes/header.php';
?>
<div class="section-head"><div><h2>Manage Student Accounts</h2><p class="muted">Activate, block or remove student accounts.</p></div></div>
<div class="table-wrap"><table class="table"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Joined</th><th>Actions</th></tr><?php foreach($users as $u):?><tr><td><?=e($u['name'])?></td><td><?=e($u['email'])?></td><td><?=e($u['phone'])?></td><td><?=e($u['status'])?></td><td><?=e(date('d M Y',strtotime($u['created_at'])))?></td><td><form class="inline-form" method="post"><?=csrf_field()?><input type="hidden" name="user_id" value="<?=(int)$u['id']?>"><input type="hidden" name="action" value="toggle"><button class="btn btn-outline" type="submit"><?= $u['status']==='active'?'Block':'Activate' ?></button></form> <form class="inline-form" method="post" onsubmit="return confirm('Delete this student and all their posts?')"><?=csrf_field()?><input type="hidden" name="user_id" value="<?=(int)$u['id']?>"><input type="hidden" name="action" value="delete"><button class="btn btn-danger" type="submit">Delete</button></form></td></tr><?php endforeach;?></table></div>
<?php include '../includes/footer.php';?>