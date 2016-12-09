<?php
require_once('DBGoods.php');
$dbGoods=new DBGoods();
//更新処理
if(isset($_POST['submitUpdate'])){
	$dbGoods->UpdateGoods();
}
//更新用フォーム要素の表示
if(isset($_POST['update'])){
	//更新対象の値を取得、値idで管理
	$dbGoodsId=$_POST['id'];
	$dbGoodsName=$dbGoods->GoodsNameForUpdate($_POST['id']);
	$Price=$dbGoods->PriceForUpdate($_POST['id']);
	//クラスを記述することで表示/非表示を設定
	$entryCss="class='hideArea'";
	$updateCss="";
}else{
	$entryCss="";
	$updateCss="class='hideArea'";
}
//削除処理
if(isset($_POST['delete'])){
	$dbGoods->DeleteGoods($_POST['id']);
}
//新規登録処理
if(isset($_POST['submitEntry'])){
	$dbGoods->InsertGoods();
}
//テーブルデータの一覧表示
$data=$dbGoods->SelectGoodsAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>売上管理システム</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
<script type="text/javascript">
function CheckDelete(){
	return confirm("削除してもよろしいですか?");
}
</script>
</head>
<body>
<div id="menu">
<ul>
<li><a href="salesinfo.php">売上情報</a></li>
<li><a href="salesinfoEntry.php">伝票の新規作成</a></li>
<li><a href="bill.php">請求書</a></li>
<li><a href="customer.php">顧客マスタ</a></li>
<li><a href="goods.php">商品マスタ</a></li>
</ul>
</div>
<h1>商品マスタ</h1>
<div id="entry"<?php echo $entryCss;?>>
<form action="" method="post">
<h2>新規登録</h2>
<label><span class="entrylabel">ID</span><input type='text' name='GoodsID' size="10" required></label>
<label><span class="entrylabel">商品名</span><input type='text' name='GoodsName' size="30" required></label>
<label><span class="entrylabel">単価</span><input type='text' name='Price' size="10" required></label>
<input type='submit' name='submitEntry' value='新規登録'>
</form>
</div>
<div id="update" <?php echo $updateCss;?>>
<form action="" method="post">
<h2>更新</h2>
<p>GoodsID:<?php echo $dbGoodsId;?></p>
<input type="hidden" name="GoodsID" value="<?php echo $dbGoodsId;?>"/>
<label><span class="entrylabel">商品名</span><input type='text' name='GoodsName' size="30" value="<?php echo $dbGoodsName;?>" required></label>
<label><span class="entrylabel">単価</span><input type='text' name='Price' 
size="10" value="<?php echo $Price;?>" required></label>
<input type='submit' name='submitUpdate' value='更新'>
</form>
</div>
<div>
<?php echo $data;?>
</div>
</body>
</html>