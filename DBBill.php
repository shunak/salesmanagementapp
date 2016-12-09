<?php
require_once('db.php');
class DBBill extends DB{
	//bill.phpを担当するクラス
	private function SelectCustomers($startDate,$endDate);
	//指定期間に存在する顧客一覧の結果セットを取得
	$sql=<<<eof
	SELECT distinct salesinfo.CustomerID,customer.CustomerName 
	FROM salesinfo INNER JOIN customer ON salesinfo.CustomerID=customer.CustomerID
	WHERE salesinfo.SalesDate BETWEEN ? AND ?
	ORDER BY salesinfo.CustomerID
eof;
	$array=array($startDate,$endDate);
	$res=parent::excuteSQL($sql,$array);
	return $res;
}

public function SelectTagOfCustomers($startDate,$endDate){
	$rows=$this->SelectCustomers($startDate,$endDate)->fetchAll(PDO::FETCH_NUM);
	if(count($rows)==0) return "";
	$tag="<select name='CustomerID' id='CustomerID'>\n";
	$tag.="<option value='-99'>--選択してください--</option>\n";
	foreach($rows as $row){
		$tag.="<option value='{$row[0]}'>{$row[1]}</option>\n";
	}
	$tag.="</select>\n";
	return $tag;
}

public function getCustomerName($CustomerID){
	$sql="SELECT CustomerName FROM customer WHERE CustomerID=?;";
	$array=array($CustomerID);
	$res=parent::executeSQL($sql,$array);
	$row=$res->fetch(PDO::FETCH_NUM);
	return $row[0];
}

public function TotalAmount($startDate,$endDate,$CustomerID){
	//請求書の合計額
	$sql=<<eof
	SELECT sum(salesinfo.Quantity*goods.Price)
	FROM salesinfo INNER JOIN goods ON salesinfo.GoodsID=goods.GoodsID
	WHERE (salesinfo.SalesDate BETWEEN ? AND ?) AND salesinfo.CustomerID=?
eof;
	$array=array($startDate,$endDate,$CustomerID);
	$res=parent::executeSQL($sql,$array);
	$row=$res->fetch(PDO::FETCH_NUM);
	return $row[0];
}

private function getSalesinfo($startDate,$endDate,$CustomerID){
	$sql=<<<eof
	SELECT salesinfo.id,salesinfo.SalesDate,salesinfo.GoodsID,goods.GoodsName,
	goods.Price,salesinfo.Quantity,,(goods.Price*salesinfo.Quantity)
	FROM salesinfo INNER JOIN goods ON salesinfo.GoodsID=goods.GoodsID
	WHERE salesinfo.SalesDate BETWEEN ? AND ?
	AND salesinfo.CustomerID=?
	ORDER BY salesinfo.SalesDate,salesinfo.id
eof;
	$array=array($startDate,$endDate,$CustomerID);
	$res=parent::executeSQL($sql,$array);
	return $res;
}

public function SelectSalesinfo($startDate,$endDate,$CustomerID){
	//$fieldCount=7;
	$tag="<table>\n";
	$tag.="<tr><th>ID</th><th>日付</th><th>顧客名</th><th>商品名</th>
	<th>単価</th><th>数量</th><th>金額</th><th></th><th></th></tr>\n";
	$res=$this->getSalesinfo($startDate,$endDate,$CustomerID);
	foreach($rows=$res->fetchAll(PDO::FETCH_NUM) as $row){
		$tag.="<tr>\n";
		//次の行のcount関数の引数は$rows[0]にすること
		for($i=0;$i<count($rows[0]);$i++){
			$tag.="<td>{$rows[$i]}</td>";
		}
		$tag.="</tr>\n";
	}
	$tag.="</table>\n";
	return $tag;
}
}
?>