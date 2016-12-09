<?php
class DB{
	private $USER="root";
	private $PW="root";
	private $dns="mysql:dbname=salesmanagement;host=localhost;charset=utf8";


private function Connectdb(){
	try{
		$pdo=new PDO($this->dns,$this->USER,$this->PW);
		return $pdo;
	}catch(Exception $e){
		return false;
	}
}

protected function executeSQL($sql,$array){
	//SQLを実行する関数
	try{
		if(!$pdo=$this->Connectdb())return false;
		$stmt=$pdo->prepare($sql);
		$stmt->execute($array);
		return $stmt;
	}catch(Exception $e){
		return false;
	}
  }
}
?>