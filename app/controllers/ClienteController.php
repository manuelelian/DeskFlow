<?php

namespace app\controllers;

use \Controller;
use \Response;
use app\models\TicketsModel;
use app\models\CategoriasModel;
use app\models\MensajesModel;

class ClienteController extends Controller
{
    public function __construct()
    {
        // 1. Verificamos que exista una sesión activa
        self::$sessionStatus = SessionController::sessionVerificacion();
        SessionController::onlyLogin();

        // 2. Protegemos la ruta: Si el usuario NO es un Cliente (Rol 1), lo expulsamos al home
        if (SessionController::getRol() != 1) {
            static::path();
            $ruta = self::$path . 'home';
            header("Location: $ruta");
            exit;
        }
    }

    public function actionIndex($var = null)
    {
        static::path();
        
        // 1. Instanciamos el modelo de Tickets
        $ticketsModel = new TicketsModel();
        
        // 2. Obtenemos el ID del cliente logueado desde la variable global de sesión
        $id_cliente = $_SESSION['USER']['id_usuario'];
        
        // 3. Traemos SOLO los tickets que le pertenecen a este cliente
        $mis_tickets = $ticketsModel->listarTicketsPorCliente($id_cliente);

        // 4. Preparamos los parámetros para la vista
        $parametros_de_vista = [
            "head"        => SiteController::head(),
            "header"      => SiteController::header(),
            "topbar"      => SiteController::topbar(),
            "menu"        => SiteController::menu(), // Menú lateral (lo podés ajustar luego si tenés uno específico para clientes)
            "mis_tickets" => $mis_tickets
        ];

        // 5. Renderizamos la vista 'index' dentro de la subcarpeta 'cliente' (que crearemos luego)
        Response::render($this->viewDir(__NAMESPACE__), 'index', $parametros_de_vista);
    }

    public function actionCrear()
    {
        static::path();

        // 1. Instanciamos CategoriasModel para cargar el <select> del formulario
        $categoriasModel = new CategoriasModel();
        $categorias = $categoriasModel->listarCategorias();

        $error = '';
        $datos = [];

        // 2. Si el cliente envió el formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo       = trim($_POST['titulo'] ?? '');
            $id_categoria = $_POST['id_categoria'] ?? '';
            $prioridad    = $_POST['prioridad'] ?? '';
            $descripcion  = trim($_POST['descripcion'] ?? '');

            // Guardamos los datos por si hay un error y debemos repoblar el formulario
            $datos = [
                'titulo'       => $titulo,
                'id_categoria' => $id_categoria,
                'prioridad'    => $prioridad,
                'descripcion'  => $descripcion
            ];

            // 3. Validaciones básicas Back-end
            if (empty($titulo) || empty($id_categoria) || empty($prioridad) || empty($descripcion)) {
                $error = "Por favor, completá todos los campos obligatorios.";
            } else {
                // 4. Preparamos los datos para el Modelo (incluyendo auditoría RN05)
                $id_cliente = $_SESSION['USER']['id_usuario'];
                
                $nuevoTicket = [
                    'id_cliente'   => $id_cliente,
                    'id_categoria' => $id_categoria,
                    'titulo'       => $titulo,
                    'descripcion'  => $descripcion,
                    'prioridad'    => $prioridad,
                    'creado_por'   => $id_cliente
                ];

                $ticketsModel = new TicketsModel();

                // 5. Guardamos en la BD (el modelo fuerza el estado 'Abierto' por RN02)
                if ($ticketsModel->crearTicket($nuevoTicket)) {
                    $ruta = self::$path . 'cliente';
                    header("Location: $ruta");
                    exit;
                } else {
                    $error = "Ocurrió un error al intentar crear el ticket. Intentá nuevamente.";
                }
            }
        }

        // 6. Renderizamos la vista 'crear' pasándole las categorías y posibles errores
        $parametros_de_vista = [
            "head"       => SiteController::head(),
            "header"     => SiteController::header(),
            "topbar"     => SiteController::topbar(),
            "menu"       => SiteController::menu(),
            "categorias" => $categorias,
            "error"      => $error,
            "datos"      => $datos
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'crear', $parametros_de_vista);
    }

    public function actionVer($id = null)
    {
        static::path();

        if (!$id) {
            header("Location: " . self::$path . "cliente");
            exit;
        }

        $ticketsModel = new TicketsModel();
        $ticket = $ticketsModel->obtenerPorId($id);

        // 1. Validamos que el ticket exista y pertenezca al cliente logueado (RN04)
        $id_cliente = $_SESSION['USER']['id_usuario'];
        if (!$ticket || $ticket->id_cliente != $id_cliente) {
            header("Location: " . self::$path . "cliente");
            exit;
        }

        $mensajesModel = new MensajesModel();
        $error = '';

        // 2. Si el cliente envía una respuesta por el chat (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validamos Regla de Negocio RN03: Un ticket Cerrado no admite nuevos mensajes
            if ($ticket->estado === 'Cerrado') {
                $error = "Este ticket se encuentra cerrado. No se pueden enviar más mensajes.";
            } else {
                $contenido = trim($_POST['contenido'] ?? '');

                if (!empty($contenido)) {
                    $nuevoMensaje = [
                        'id_ticket'  => $id,
                        'id_usuario' => $id_cliente,
                        'contenido'  => $contenido
                    ];

                    if ($mensajesModel->insertarMensaje($nuevoMensaje)) {
                        // Recargamos la página para limpiar el POST y mostrar el mensaje nuevo
                        header("Location: " . self::$path . "cliente/ver/" . $id);
                        exit;
                    } else {
                        $error = "No se pudo enviar el mensaje. Intentá nuevamente.";
                    }
                } else {
                    $error = "El mensaje no puede estar vacío.";
                }
            }
        }

        // 3. Obtenemos todo el hilo de mensajes del ticket
        $mensajes = $mensajesModel->obtenerMensajesPorTicket($id);

        // 4. Preparamos los parámetros para la vista
        $parametros_de_vista = [
            "head"     => SiteController::head(),
            "header"   => SiteController::header(),
            "topbar"   => SiteController::topbar(),
            "menu"     => SiteController::menu(),
            "ticket"   => $ticket,
            "mensajes" => $mensajes,
            "error"    => $error
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'ver', $parametros_de_vista);
    }
}