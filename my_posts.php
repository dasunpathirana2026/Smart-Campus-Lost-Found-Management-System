<?php
require_once 'includes/functions.php'; require_login();
if(!in_array(current_user()['role']??'', ['student','admin'], true)) redirect(BASE_URL.'index.php');
if($_SERVER['REQUEST_METHOD']==='POST' && post_text('action')==='delete'){
  if(!valid_csrf_token()){flash('error','Your session expired. Please refresh and try again.');redirect(BASE_URL.'my_posts.php');}
  $id=filter_var($_POST['post_id']??null,FILTER_VALIDATE_INT);
  if($id===false || $id===null || $id<1){flash('error','The selected post is invalid.');redirect(BASE_URL.'my_posts.php');}
  $stmt=db()->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
  $stmt->execute([$id,$_SESSION['user_id']]);
  $deleted=$stmt->rowCount()>0;
  flash($deleted?'success':'error',$deleted?'Post deleted.':'That post could not be found.');
  redirect(BASE_URL.'my_posts.php');
}
$items=db()->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY created_at DESC");$items->execute([$_SESSION['user_id']]);$items=$items->fetchAll();
$page_title='My Posts'; include 'includes/header.php';
?>
<div class="section-head"><div><h2>My Posts</h2><p class="muted">Manage your lost and found reports.</p></div><div class="report-actions"><a class="btn btn-primary" href="<?=BASE_URL?>add_lost_item.php">Add Lost Item</a><a class="btn btn-found-action" href="<?=BASE_URL?>add_found_item.php">Add Found Item</a></div></div>
<div class="table-wrap"><table class="table"><tr><th>Item</th><th>Type</th><th>Location</th><th>Date Lost / Found</th><th>Status</th><th>Action</th></tr><?php foreach($items as $p):?><tr><td><strong><?=e($p['title'])?></strong></td><td><span class="badge badge-<?=$p['type']?>"><?=e($p['type'])?></span></td><td><?=e($p['location'])?></td><td><?=e(date('d M Y',strtotime($p['item_date'])))?></td><td><?=e($p['status'])?></td><td><a class="btn btn-outline" href="<?=BASE_URL?>item_details.php?id=<?=(int)$p['id']?>">View</a> <form class="inline-form" method="post" onsubmit="return confirm('Delete this post?')"><?=csrf_field()?><input type="hidden" name="action" value="delete"><input type="hidden" name="post_id" value="<?=(int)$p['id']?>"><button class="btn btn-danger" type="submit">Delete</button></form></td></tr><?php endforeach;?></table></div>
<?php if(!$items):?><div class="empty">You have not created any posts yet.</div><?php endif;?>
<?php include 'includes/footer.php';?>