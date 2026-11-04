<?php 
/**
 * Controllador 
 */

class Controller
{

	protected $title = 'ISEI 4030 | Proyecto Final';
	protected static $path;
	protected static $sessionStatus;
	protected static $ruta;

    public static function ruta(){

		$camino = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		$camino = str_replace('?', '&', $camino);
		$removerUrl = $_SERVER['QUERY_STRING'];

		$removerUrl = str_replace('url=','', $removerUrl);
		$camino = str_replace($removerUrl,'', $camino);

		$camino = $_SERVER["REQUEST_SCHEME"].'://'.$camino;

		self::$ruta = $camino;
    }



	public function actionIndex($var = null){
		$this->action404();
	}

	public function action404(){
		echo "Error 404 - Página no encontrada - CONTROLLER";
		static::path();
		$ruta = self::$path;
		header('Location:'.$ruta.'404');
	}
	
	public static function path(){
		if (isset($_GET['url'])) {
			$url = explode('/', $_GET['url']);
			if (count($url)> 1) {
				$dirFiles ='';
				for ($i=1; $i < count($url); $i++) { 
					$dirFiles .= '../';
				}
			}else{
				$dirFiles = '';
			} 
		}else{
			$dirFiles = '';
		}
		self::$path = $dirFiles;
	}

	protected function viewDir($nameSpace){
		$replace = array($nameSpace,'Controller');
		$viewDir = str_replace($replace , '', get_class($this)).'/';
		$viewDir = str_replace('\\', '', $viewDir);
		$viewDir = strtolower($viewDir);
		return $viewDir;
	}

	protected static function loadJS($vars = [], $specific_controller = null){

		// if (isset($_GET['url'])) {
			if (!isset($specific_controller)) {
				$url = explode('/', $_GET['url']);
				$controller = $url[0];

			}else{
				$controller = strtolower($specific_controller);
			}

				$arr =[];

				foreach ($vars as $key => $value) {
					$$key = $value;
					$arr["#$key#"] = $value;
				}
				// echo "<pre>";
				// var_dump($arr);
				// echo "</pre>";

			$resultado = file_get_contents(APP_PATH."helpers/js/".$controller."_js".".php");
			$resultado = strtr($resultado,$arr);


			return $resultado;
	}

	protected static function loadLibreria($vars, $type, $name){
			$arr =[];

			foreach ($vars as $key => $value) {
				$$key = $value;
				$arr =["#$key#" =>  $value];
			}

			// var_dump($arr);

			$resultado = file_get_contents(APP_PATH."librerias/".$type."/".$name.".".$type);
			$resultado = strtr($resultado,$arr);
			if ($type == 'js') {
				$result = "<script>$resultado</script>";
			}elseif ($type == 'css') {
				$result = "<style>$resultado</style>";
			}
			return $result;
	}

	protected static function tokenSeguro(){
		$longitud = 25;
		return bin2hex(random_bytes(($longitud - ($longitud % 2)) / 2));
	}



}