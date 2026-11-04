<?php 
/**
 * Clase Modelo
 */
class Model
{
	protected $table; //Tabla de la base de datos
	protected $primaryKey = "id"; //Primary Key

	public static function findId($id){
		$model = new static();
		$sql = "SELECT * from ".$model->table." where ".$model->primaryKey." = :id";
		$params = ["id" => $id];
		$result = DataBase::getRecord($sql, $params);

		$model = $result; #borrar esta linea y descomentar arriba
		return $model;
	}

	public static function getAll($config = null){
		$model = new static();
		$sql = "SELECT * from ".$model->table;
		$result = DataBase::getRecords($sql);
		return $result;
	}

	public static function getColumnsNames($table){
		 $result = DataBase::getColumnsNames($table);
		 return $result;
	}

	public static function fechaAhora(){
		date_default_timezone_set('America/Argentina/Buenos_Aires');
		$fecha_hora = time();
		$fecha_hora =  date("Y-m-d H:i:s", $fecha_hora);

		return $fecha_hora;
	}
}
