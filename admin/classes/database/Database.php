<?php
class Database{
	protected $dbh;
	protected $error;
	protected $stmt;

	public function __construct($conn){
        $dsn = 'mysql:host='.$conn[0].';dbname='.$conn[3].';charset=UTF8';
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        try{
            $this->dbh = new PDO($dsn, $conn[1], $conn[2], $options);
        }
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
	}

	public function query($query){
    	$this->stmt = $this->dbh->prepare($query);
	}

	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}

	public function execute(){
		return $this->stmt->execute();
	}

	public function resultset($type = PDO::FETCH_ASSOC){
		$this->execute();
		return $this->stmt->fetchAll($type);
	}

	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount(){
		return $this->stmt->rowCount();
	}

	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}

	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}

	public function endTransaction(){
		return $this->dbh->commit();
	}

	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}

	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}

	public function format($valuesToBind){
		$nonPdoChars = array('\'','`');
		return str_replace($nonPdoChars, "", $valuesToBind);
	}

}
?>
