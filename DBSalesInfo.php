<?php
require_once('db.php');
class DBSalesInfo extends DB{
	//salesinfoテーブルのCRUD担当
	public function ListGoods(){
		//商品名リストの作成
		$sql="SELECT GoodsID, GoodsName, Price FROM goods ORDER BY GoodsID";
		$res=parent::executeSQL($sql,null);
		$list="<select name='GoodsID'>\n";
		$list.="<option value='-99'>--選択してください--</option>\n";
		foreach($rows=$res->fetchAll(PDO::FETCH_ASSOC) as $row){
			$list.="<option value='{$row['GoodsID']}'>{$row['GoodsName']}</option>\n";
		}
		$list.="</select>\n";
		return $list;
	}
	
	public function InsertSalesinfo(){
		//顧客IDと商品IDが選択されていたら登録
		if($_POST['CustomerID']>0&&$_POST['GoodsID']>0){
			$sql="INSERT INTO salesinfo VALUES(?,?,?,?,?)";
			$array=array(null, $_POST['SalesDate'],$_POST['CustomerID'],$_POST['GoodsID'],
			$_POST['Quantity']);
			parent::executeSQL($sql,$array);
		}
	}
	
	private function getSalesinfo($salesDate, $customerID){
		//結果セットを取得
		$sql=<<<eof
		SELECT salesinfo.id,salesinfo.SalesDate,customer.CustomerName,goods.GoodsName,goods.Price,salesinfo.Quantity,goods.Price*salesinfo.Quantity FROM salesinfo INNER JOIN customer 
		ON salesinfo.CustomerID=customer.CustomerID INNER JOIN goods ON salesinfo.GoodsID=goods.GoodsID WHERE salesinfo.SalesDate=? and salesinfo.CustomerID=? ORDER BY salesinfo.id
eof;
		$array=array($salesDate,$customerID);
		$res=parent::executeSQL($sql,$array);
		return $res;
	}
	
	public function SelectSalesinfo($salesDate,$customerID){
		//日付と顧客IDで売上情報を抽出（更新・削除ボタン付き）
		$res=$this->getSalesinfo($salesDate,$customerID);
		$data="<table id='entryslip'>\n";
		$data.="<tr><th>ID</th><th>日付</th><th>顧客名</th><th>商品名</th><th>単価</th><th>数量</th><th>金額</th><tr>\n";
		foreach($rows=$res->fetchAll(PDO::FETCH_NUM) as $row){
			$data.="<tr>";
			for($i=0;$i<count($row[$i]);$i++){
				$data.="<td>{$row[$i]}</td>";
			}
			$data.="</tr>\n";
		}
		$data.="</table>\n";
		return $data;
	}

	public function ListCustomerWithSelected($CustomerID){
	//顧客名リストの作成（引数の値を表示）
	$sql="SELECT CustomerID,CustomerName FROM customer ORDER BY CustomerID";
	$res=parent::executeSQL($sql,null);
	$list="<select name='CustomerID'>\n";
	$list.="<option value='-99'>--選択してください--</option>\n";
	foreach($rows=$res->fetchAll(PDO::FETCH_NUM) as $row){
		$selected=($row[0]==$CustomerID)?'selected':'';
		$list.="<option value='{$row[0]}' {$selected}>{$row[1]}</option>\n";
	}
	$list.="</select>\n";
	return $list;
	}
	
	public function ListCustomer(){
		//顧客名リストの作成
		$sql="SELECT CustomerID,CustomerName FROM customer ORDER BY CustomerID";
		$res=parent::executeSQL($sql,null);
		$list="<select name='CustomerID'>\n";
		$list="<option value='-99'>--選択してください--</option>\n";
		foreach($rows=$res->fetchAll(PDO::FETCH_NUM) as $row){
			$list.="<option value='rows[0]'>{$row[1]}</option>\n";
		}
		$list.="</select>\n";
		return $list;
	}

public function DeleteDetail(){
	$sql="DELETE FROM salesinfo WHERE ID=?";
	$array=array($_POST['id']);
	parent::executeSQL($sql.$array);	
}

public function UpdateDetail(){
	$sql="UPDATE salesinfo SET SalesDate=?, CustomerID=?, GoodsID=?, Quantity=? WHERE id=?";
	$array=array($_POST['SalesDate'],$_POST['CustomerID'],$_POST['GoodsID'],$_POST['Quantity'],$_POST['id']);
	parent::executeSQL($sql,$array);
}

private function FieldValueForUpdate($id,$field){
	//引数の値を取得
	$sql="SELECT {$field} FROM salesinfo WHERE id=?";
	$array=array($id);
	$res=parent::executeSQL($sql,$array);
	$rows=$res->fetch(PDO::FETCH_NUM);
	return $rows[0];
}

public function getSalesDate($id){
	return $this->FieldValueForUpdate($id,"SalesDate");
}

public function getCustomerID($id){
	return $this->FieldValueForUpdate($id,"CustomerID");
}

public function getGoodsID($id){
	return $this->FieldValueForUpdate($id,"GoodsID");
}

public function getQuantity($id){
	return $this->FieldValueForUpdate($id,"Quantity");
}

public function ListGoodsWithSelected($GoodsID){
	//商品リストの作成（引数の値を表示）
	$sql="SELECT GoodsID,GoodsName FROM goods ORDER BY GoodsID";
	$res=parent::executeSQL($sql,null);
	$list="<select name='GoodsID'>\n";
	$list.="<option value='-99'>--選択してください--</option>\n";
	foreach($rows=$res->fetchAll(PDO::FETCH_ASSOC) as $row){
		$selected=($row['GoodsID']==$GoodsID)?'selected':'';
		$list.="<option value='{$row['GoodsID']}' {$selected}>{$row['GoodsName']}</option>\n";
	}
	$list.="</select>\n";
	return $list;
}
public function SelectSalesinfoWithButton($salesDate,$customerID){
	//日付と顧客IDで売上情報を抽出(更新・削除ボタン付き)
	$res=$this->getSalesinfo($salesDate,$customerID);
	$data="<table>\n";
	$data.="<tr><th>ID</th><th>日付</th><th>顧客名</th><th>商品名</th><th>単価</th><th>数量</th><th>金額</th><th></th><th></th></tr>\n";
	foreach($rows=$res->fetchAll(PDO::FETCH_NUM) as $row){
		$data.="<tr>";
		for($i=0;$i<count($row);$i++){
			$data.="<tr>{$row[$i]}</td>";
		}
		$data.=<<<eof
		<td><form method='post' action=''>
		<input type='hidden' name='id' value='{$row[0]}' >
		<input type='submit' name-'updatedetail' value='更新'></from></td>
		<td><form method='post' action=''>
		<input type='hidden' name='id' value='{$row[0]}'>
		<input type='submit' name='deletedetail' value='削除' onClick='return CheckDelete()'></form></td></tr>\n
eof;
		$data.="</tr>\n"; 
	}
	$data.="</table>\n";
	return $data;
	}

	public function TotalAmount($SalesDate,$CustomerID){
		//伝票の合計額
		$sql=<<<eof
		SELECT sum(salesinfo.Quantity*goods.Price)
		FROM salesinfo INNER JOIN godos ON salesinfo.GoodsID=goods.GoodsID WHERE salesinfo.SalesDate=? AND salesinfo.CustomerID=?
eof;
		$array=array($SalesDate, $CustomerID);
		$res=parent::executeSQL($sql,$array);
		$row=$res->fetch(PDO::FETCH_NUM);
		return $row[0];
	}
	
	public function DeleSlip(){
		$sql="DELETE FROM salesinfo WHERE SalesDate=? AND CustomerID=?";
		$array=array($_POST['SalesDate'],$_POST['CustomerID']);
		parent::executeSQL($sql,$array);
	}

	public function SelectSlips($salesDate){
		//日付で抽出
		$sql=<<<eof
		SELECT distinct salesinfo.SalesDate,salesinfo.CustomerID,customer.CustomerName FROM salesinfo INNER JOIN customer ON salesinfo.CustomerID=customerID WHERE salesinfo.SalesDate=? ORDER BY customer.CustomerID
eof;
	}
	$array=array($salesDate);
	$res=parent::executeSQL($sql,$array);
	$data="";
	foreach($rows=$res->fetchAll(PDO::FETCH_NUM) as $row){
		$data.="<tr>";
		for($i=0;$i<count($row);$i++){
			$data.="<td>{$row[$i]}</td>";
		}
		$data.=<<eof
		<td><form method='post' action=''> 
		<input type='hidden' name='SalesDate' value='{$row[0]}'>
		<input type='hidden' name='CustomerID' value='{$row[1]}'>
		<input type='submit' name='detail' value='詳細'></form></td>
		<td><form method='post' action=''>
		<input type='hidden' name='SalesDate' value='{$row[0]}'>
		<input type='hidden' name='CustomerID' value='{$row[1]}'>
		<input type='submit' name='delete' value='削除' onClick='return CheckDelete()'></from>
		</td></tr>\n		
eof;
	}
return $data;
}	
}
?>