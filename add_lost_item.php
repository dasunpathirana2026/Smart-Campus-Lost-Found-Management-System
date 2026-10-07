<?php
require_once 'includes/functions.php'; require_login();
if(!in_array(current_user()['role']??'', ['student','admin'], true)) redirect(BASE_URL.'index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $title=post_text('title');
  $description=post_text('description');
  $location=post_text('location');
  $phone=post_text('phone');
  $item_date=post_text('item_date');
  try{
    if(!valid_csrf_token()) throw new RuntimeException('Your session expired. Please refresh the page and try again.');
    if($title==='' || strlen($title)>150 || $description==='' || $location==='' || strlen($location)>200 || $phone==='' || strlen($phone)>30){
      throw new RuntimeException('Please complete each field and keep the item name, location, and phone within their allowed lengths.');
    }
    if(!is_valid_item_date($item_date)) throw new RuntimeException('Choose a valid date the item was lost. The date cannot be in the future.');
    $image=upload_image($_FILES['image']??[]);
    $stmt=db()->prepare("INSERT INTO posts(user_id,type,item_date,title,description,location,phone,image,status) VALUES(?,?,?,?,?,?,?,?,'open')");
    $stmt->execute([$_SESSION['user_id'],'lost',$item_date,$title,$description,$location,$phone,$image]);
    flash('success','Lost item report posted successfully.'); redirect(BASE_URL.'my_posts.php');
  }catch(RuntimeException $e){$error=$e->getMessage();}
  catch(PDOException $e){error_log($e->getMessage());$error='The report could not be saved. Please try again.';}
}
$page_title='Report Lost Item'; include 'includes/header.php';
?>
<div class="form-card"><h1>Report a Lost Item</h1><p class="muted">Add enough details so someone can identify and return it.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data"><?=csrf_field()?><div class="field"><label for="title">Item Name</label><input id="title" name="title" maxlength="150" required placeholder="e.g. Black Wallet" value="<?=e(post_text('title'))?>"></div><div class="field"><label for="description">Description</label><textarea id="description" name="description" required placeholder="Colour, brand, special marks..."><?=e(post_text('description'))?></textarea></div><div class="field"><label for="location">Last Seen / Location</label><input id="location" name="location" maxlength="200" required value="<?=e(post_text('location'))?>"></div><div class="field"><label for="item_date">Date Lost</label><input id="item_date" type="date" name="item_date" max="<?=e(date('Y-m-d'))?>" value="<?=e($_SERVER['REQUEST_METHOD']==='POST' ? post_text('item_date') : date('Y-m-d'))?>" required></div><div class="field"><label for="phone">Contact Phone</label><input id="phone" name="phone" maxlength="30" value="<?=e(post_text('phone'))?>" required></div><div class="field"><label for="image">Photo (optional, max 5MB)</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp"></div><button class="btn btn-primary">Publish Lost Report</button></form></div>
<?php include 'includes/footer.php';?>