<?php
require_once('db.php');
class DBCustomer extends DB{
	//customerテーブルのCRUD担当
	public function UpdateCustomer(){
		$sql="UPDATE customer SET CustomerName=?, TEL=?, Email=?, WHERE CustomerID=?";
		//array関数の引数の順番に注意すること
		$array=array($_POST['CustomerName'], $_POST['TEL'], $_POST['Email'], $_POST['CustomerID']);
		parent::executeSQL($sql, $array);
	}
	
	public function CustomerNameForUpdate($CustomerID){
		return $this->FieldValueUpdate($CustomerID, "CustomerName");
	}
	
	public function TELForUpdate($CustomerID){
		return $this->FieldValueUpdate($CustomerID, "TEL");
	}
	
	public function EmailForUpdate($CustomerID){
		return $this->FieldValueUpdate($CustomerID, "Email");
	}
	
	public function FieldValueUpdate($CustomerID, $field){
		//引数の値を取得
		$sql="SELECT {$field} FROM customer WHERE CustomerID=?";
		$array=array($CustomerID);
		$res=parent::executeSQL($sql,$array);
		$rows=$res->fetch(PDO::FETCH_NUM);
		return $row[0];
	}
	
	public function DeleteCustomer($CustomerID){
		$sql="DELETE FROM customer WHERE CustomerID=?";
		$array=array($CustomerID);
		parent::executeSQL($sql, $array);
	}

	public function InsertCustomer(){
		$sql="INSERT INTO customer VALUE(?,?,?,?)";
		$array=array($_POST['CustomerID'], $_POST['CustomerName'], $_POST['TEL'], $_POST['Email']);
		parent::executeSQL($sql, $array);
	}
	
	public function SelectCustomerAll(){
		$sql="SELECT*FROM customer";
		$res=parent::executeSQL($sql,null);
		$data="<table class='recordlist'>";
		$data.="<tr><th>ID</th><th>顧客名</th><th>TEL</th><th>Email</th><th></th><th></th></tr>\n";
		foreach($rows=$res->fetchAll(PDO::FETCH_NUM) as $row){
			$data.="<tr>";
			for($i=0;$i<count($row);$i++){
				$data.="<td>{$row[$i]}</td>";
			}
			//更新ボタンのコード
			$data.=<<<eof
			<td><form method='post' action=''>
			<input type='hidden' name-'id' value='{$row[0]}'>
			<input type='submit' name='update' value='更新'>
			</form></td>	
eof;
			//削除ボタンのコード
			$data.=<<<eof
			<td><form method='post' action=''>
			<input type='hidden' name='id' id='Deleteid' value='{$row[0]}'>
			<input type='submit' name='delete' value='削除' onClick='return CheckDelete()'>
			</form></td>
eof;
			$data.="</tr>\n";	
		}
		$data.="</table>\n";
		return $data;
	}
}
?>