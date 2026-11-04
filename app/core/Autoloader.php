<?php

/**
 * Clase Autoloader
 * Esta clase se encarga de cargar automáticamente las clases necesarias
 * basándose en una lista predefinida de nombres de clases.
 */
class Autoloader
{
    /**
     * Constructor de la clase.
     * Llama al método loadAppClasses al instanciar la clase.
     */
    public function __construct()
    {
        $this->loadAppClasses();
    }

    /**
     * Método privado loadAppClasses
     * Registra una función de autoloading anónima utilizando spl_autoload_register.
     * Esta función se encarga de cargar las clases automáticamente cuando son requeridas.
     */
    private function loadAppClasses()
    {
        spl_autoload_register(function ($nombreClase) {

            // Lista de partes de nombres de clases que se buscarán para la carga automática.
            $clasesParaCargaAutomatica = array('App', 'Controller', 'Model', 'Response', 'DataBase');

            $cargaAutomatica = false;

            // foreach sobre la lista de partes de nombres de clases.
            foreach ($clasesParaCargaAutomatica as $clases) {
                // Comprueba si el nombre de la clase contiene alguna parte definida.
                $contiene = strstr($nombreClase, $clases);

                // Si encuentra una coincidencia, activa la carga automática.
                if (is_string($contiene)) {
                    $cargaAutomatica = true;
                }
            }

            // Si la carga automática está activada, incluye el archivo de la clase.
            if ($cargaAutomatica == true) {
                require_once str_replace('\\', '/', $nombreClase) . ".php";
            }

        }, TRUE, FALSE);
    }
}

// Crea una instancia de la clase Autoloader para iniciar el proceso de carga automática.
new Autoloader();