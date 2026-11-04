<?php 
/**
 * Coenxión con la Base de Datos
 */
class DataBase
{

	private static $host;
    private static $dbname;
    private static $dbuser;
    private static $dbpass;

	
	private $dbh; //Data Base handler
	private $stmt; //Statement
	private static $error;

	public static $numRows;

	private function __construct() {}
    
	public static function connection(){
		self::$host   = $_ENV['DB_HOST'];
        self::$dbname = $_ENV['DB_NAME'];
        self::$dbuser = $_ENV['DB_USER'];
        self::$dbpass = $_ENV['DB_PASS'];
		// //Configurar la conexion
		$dsn = "mysql:host=".self::$host.";dbname=".self::$dbname;
		$opciones = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		//Crear una instancia de PDO
		try {
			$conexion = new PDO($dsn, self::$dbuser, self::$dbpass, $opciones);
			$conexion->exec('set names utf8');
			$conexion->exec('SET time_zone = "-03:00";');
			$conexion->exec('SET @@session.time_zone = "-03:00";');
			

		} catch (PDOException $e) {
			self::$error = $e->getMessage();
			return self::$error;

		}
		return $conexion;
	}

	public static function query($sql, $params = []){
		$statement = static::connection()->prepare($sql);
		$statement->execute($params);
		$result = $statement->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

	public static function execute($sql, $params = []){
		$statement = static::connection()->prepare($sql);
		$result = $statement->execute($params);
		return $result;
	}

	public static function getRecords($sql, $params = []){
		$result['status'] = true;
		$statement = static::connection()->prepare($sql);
		try {
			$statement->execute($params);
			$resultado = $statement->fetchAll(PDO::FETCH_OBJ);
		}
		catch (exception $e) {
		    $resultado = false;
		}


		if ($resultado === false) {
			$result['status'] = false;
			$result['resultado'] = $e->errorInfo;
		}else{
			$result['resultado'] = $resultado;
		}
 
		return $result;
	}

	public static function getRecord($sql, $params = []){
		$statement = static::connection()->prepare($sql);
		// echo "$sql";
		$statement->execute($params);
		$result = $statement->fetch(PDO::FETCH_OBJ);
		return $result;
	}

	public static function transaction($sql){
			$statement = static::connection();
		    try {
		    	/* Iniciar una transacción, desactivando 'autocommit' */
		        $statement->beginTransaction();

		        for ($i=0; $i < count($sql); $i++) { 
		        	$statement->exec($sql[$i]);
		        }

		        $statement->commit();

		        $estadoOperacion['state'] = true;

		    } catch(PDOException $e) {
		    	// var_dump($e);
		        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
		             $statement->commit();
		        } else {
		        	 echo "ERROR, ROLLBACK"."\n"; 
		        	 echo $e->errorInfo[2]."\n"; 
		        	
		             $statement->rollBack();
		            
		            $estadoOperacion['notification'] = $e->errorInfo[2];
		        }
		        $estadoOperacion['state'] = false;
		    }
		    
		if (empty($estadoOperacion['notification'])) {
			 $estadoOperacion['notification'] = "Listo!";
		}
		return $estadoOperacion;
	}

	public static function transactionReturn($sql){
			$statement = static::connection();
		    try {
		    	/* Iniciar una transacción, desactivando 'autocommit' */
		        $statement->beginTransaction();

		        for ($i=0; $i < count($sql); $i++) { 
		        	$eje = $statement->prepare($sql[$i]);
		        	$eje->execute();
		        }

	        	$resultado = $eje->fetchAll(PDO::FETCH_OBJ);

		        $statement->commit();

		        $estadoOperacion['state'] = true;
		        $estadoOperacion['resultado'] = $resultado;

		    } catch(PDOException $e) {
		    	// var_dump($e);
		        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
		             $statement->commit();
		        } else {
		        	 echo "ERROR, ROLLBACK"."\n"; 
		        	 echo $e->errorInfo[2]."\n"; 
		        	
		             $statement->rollBack();
		            
		            $estadoOperacion['notification'] = $e->errorInfo[2];
		        }
		        $estadoOperacion['state'] = false;
		         $estadoOperacion['resultado'] = 'Error!';
		    }
		    
		if (empty($estadoOperacion['notification'])) {
			 $estadoOperacion['notification'] = "Operación exitosa";
		}
		return $estadoOperacion;
	}

	public static function rowCount($sql, $params = []){
		$statement = static::connection()->prepare($sql);

		try {
			$statement->execute($params);
			$result = $statement->rowCount();
		}
		catch (exception $e) {

		    $result = false;
		}
		
		return $result;
	}

	public static function getColumnsNames($table){
		$sql = "SELECT column_name
				FROM information_schema.columns
				WHERE table_name='$table'";
		$statement = static::connection()->prepare($sql);
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}
}