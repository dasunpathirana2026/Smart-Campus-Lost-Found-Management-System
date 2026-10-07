<?php
require_once 'includes/functions.php';
$q=trim($_GET['q']??'');
$sql="SELECT p.*,u.name owner_name FROM posts p JOIN users u ON u.id=p.user_id WHERE p.type='lost' AND p.status='open'";
$params=[];
if($q!==''){ $sql.=" AND (p.title LIKE ? OR p.description LIKE ? OR p.location LIKE ?)"; $like="%$q%"; $params=[$like,$like,$like]; }
$sql.=" ORDER BY p.created_at DESC";
$stmt=db()->prepare($sql); $stmt->execute($params); $items=$stmt->fetchAll();
$page_title='Lost Items'; include 'includes/header.php';
?>
<div class="section-head"><div><h2>Lost Items</h2><p class="muted">Browse active lost-item reports.</p></div><?php if(is_logged_in() && in_array(current_user()['role'], ['student', 'admin'], true)):?><a class="btn btn-primary" href="<?=BASE_URL?>add_lost_item.php">Report Lost Item</a><?php endif;?></div>
<form class="search-form" method="get"><label class="visually-hidden" for="search-items">Search lost items</label><input id="search-items" name="q" value="<?= e($q) ?>" placeholder="Search item or location..." maxlength="150"><button class="btn btn-primary" type="submit">Search</button></form>
<div class="grid"><?php foreach($items as $p): ?><article class="card"><?php if($p['image']): ?><img class="item-img" src="<?= BASE_URL.e($p['image']) ?>" alt="<?=e($p['title'])?>"><?php else:?><div class="item-placeholder" aria-hidden="true">?</div><?php endif;?><div class="card-body"><span class="badge badge-lost">Lost</span><h3><?=e($p['title'])?></h3><div class="meta">📍 <?=e($p['location'])?><br>📅 Lost <?=e(date('d M Y',strtotime($p['item_date'])))?></div><br><a class="btn btn-primary" href="<?=BASE_URL?>item_details.php?id=<?=(int)$p['id']?>">View details</a></div></article><?php endforeach;?></div>
<?php if(!$items):?><div class="empty">No matching lost-item reports found.</div><?php endif;?><?php include 'includes/footer.php';?>