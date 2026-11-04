<?php

/**
 * Clase Iniciadora, de arranque (Bootstrapping) App
 * Esta clase se encarga de inicializar y dirigir las solicitudes de la aplicación.
 */
class App
{
    // Propiedades para almacenar valores predeterminados del controlador, método y parámetros.
    protected $controller  = "app\\controllers\\" . "HomeController";
    protected $method      = "actionIndex";
    protected $params      = [];

    /* El controlador por defecto es Home */
    /* El método por defecto es Index */

    /**
     * Constructor de la clase.
     * Se encarga de procesar la URL, determinar el controlador, método y parámetros,
     * y luego llama al método correspondiente del controlador.
     */
    public function __construct()
    {
        // Obtiene la URL parseada.
        $url = $this->parseUrl();

        // Establece el controlador por defecto como HomeController.
        $controladorPorDefecto = false;

        // Comprueba si hay una parte en la URL que corresponda a un controlador.
        if (!isset($url[0])) {
            // Si no hay controlador definido en la URL, se utiliza HomeController por defecto.
            $controllerActive = $this->controller;
            $checkArchivoControlador = $controllerActive . '.php';
            $controladorPorDefecto = true;
        } else {
            // Si hay un dato en la URL en la posición del Controlador, se comprueba si no es un método del HomeController.
            $controllerActive = ucfirst(strtolower($url[0])) . "Controller";
            $checkArchivoControlador = APP_PATH . "controllers/" . $controllerActive . '.php';
        }
        
        // Comprueba si no hay controlador en la URL, si está vacío, o si es "index"
        /*if (!isset($url[0]) || $url[0] === '' || strtolower($url[0]) === 'index') {
            $controllerActive = "HomeController";
            // Ahora sí usamos APP_PATH para que file_exists() lo encuentre correctamente
            $checkArchivoControlador = APP_PATH . "controllers/" . $controllerActive . '.php';
            $controladorPorDefecto = true;

            // Eliminamos 'index' si el usuario lo escribió explícitamente para no romper la asignación de métodos
            if (isset($url[0])) {
                unset($url[0]);
               }
        } else {
            // Si hay un dato en la URL en la posición del Controlador
            $controllerActive = ucfirst(strtolower($url[0])) . "Controller";
            $checkArchivoControlador = APP_PATH . "controllers/" . $controllerActive . '.php';
        }*/

        // Comprueba si existe el archivo del controlador en el directorio principal.
        if (file_exists($checkArchivoControlador)) {
            // Si existe, establece ese controlador para ser instanciado.
            if ($controladorPorDefecto == false) {
                $this->controller = APP_PATH . "controllers/" . $controllerActive;
            }

            // Instancia el controlador.
            $tempController =  str_replace('/', '\\', $this->controller);
            $this->controller = new  $tempController;

            // Comprueba si hay un método especificado en la URL.
            if (isset($url[1])) {
                $methodName = "action" . ucfirst(strtolower($url[1]));

                // Si el método existe en el controlador, lo establece como el método activo.
                if (method_exists($this->controller, $methodName)) {
                    $this->method = $methodName;
                    unset($url[1]); // Elimina el valor del método de los parámetros.
                } else {
                    $this->method = 'action404'; // Establece un método por defecto si no se encuentra.
                    unset($url[1]);
                }
            }

            unset($url[0]); // Elimina el valor del controlador de los parámetros.
            $noexisteControlador = false;
        } else {
            // Si no se encuentra el controlador en el directorio principal, busca en subdirectorio.
            $subDirectorioClase = str_replace('Controller', '', $controllerActive);
            $checkArchivoControlador = APP_PATH . "controllers/" . strtolower($subDirectorioClase) . '/' . $controllerActive . '.php';

            if (file_exists($checkArchivoControlador)) {
                // Si existe, establece ese controlador para ser instanciado.
                $this->controller = APP_PATH . "controllers/" . strtolower($subDirectorioClase) . '/' . $controllerActive;

                $tempController =  str_replace('/', '\\', $this->controller);
                $this->controller = new  $tempController;

                // Comprueba si hay un método especificado en la URL.
                if (isset($url[1])) {
                    $methodName = "action" . ucfirst(strtolower($url[1]));

                    // Si el método existe en el controlador, lo establece como el método activo.
                    if (method_exists($this->controller, $methodName)) {
                        $this->method = $methodName;
                        unset($url[1]); // Elimina el valor del método de los parámetros.
                    } else {
                        $this->method = 'action404'; // Establece un método por defecto si no se encuentra.
                        unset($url[1]);
                    }
                }

                unset($url[0]); // Elimina el valor del controlador de los parámetros.
                $noexisteControlador = false;
            } else {
                // Si no se encuentra el controlador ni en el directorio principal ni en subdirectorio.
                // Se instancia el controlador por defecto HomeController.
                $tempController =  str_replace('/', '\\', $this->controller);
                $this->controller = new  $tempController;

                // Comprueba si el método especificado en la URL es un método del controlador por defecto HomeController.
                $methodName = "action" . ucfirst(strtolower($url[0]));

                if (method_exists($this->controller, $methodName)) {
                    $this->method = $methodName;
                    unset($url[0]); // Elimina el valor del método de los parámetros.
                } else {
                    $this->method = 'action404'; // Establece un método por defecto si no se encuentra.
                    unset($url[1]);
                }

                $noexisteControlador = true;
            }
        }

        // Si la URL tiene valores restantes, los agrega a los parámetros; de lo contrario, los parámetros son nulos.
        $this->params = $url ? array_values($url) : $this->params;

        // Llama al método del controlador y pasa los parámetros al método.
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Método parseUrl
     * Parsea la URL y devuelve un array con los segmentos de la URL.
     */
    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode("/", filter_var(rtrim($_GET["url"], "/"), FILTER_SANITIZE_URL));
        }
    
        /*

            La función rtrim en PHP se utiliza para eliminar caracteres específicos del final (derecha) de una cadena. El nombre rtrim significa "right trim" o "recorte derecho"

            Cuando se utiliza FILTER_SANITIZE_URL, la función filter_var elimina todos los caracteres ilegales de la URL, dejando solo aquellos que son permitidos en una URL válida. Esto incluye la eliminación de caracteres que no son válidos en la sintaxis de una URL, como espacios en blanco y caracteres especiales.
        */
    }
}