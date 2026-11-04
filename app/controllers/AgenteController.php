<?php

namespace app\controllers;

use \Controller;
use \Response;
use app\models\TicketsModel;
use app\models\MensajesModel;

class AgenteController extends Controller
{
    public function __construct()
    {
        // 1. Verificamos que exista una sesión activa
        self::$sessionStatus = SessionController::sessionVerificacion();
        SessionController::onlyLogin();

        // 2. Protegemos la ruta: Si el usuario NO es un Agente (Rol 2), lo expulsamos al home o login
        if (SessionController::getRol() != 2) {
            static::path();
            $ruta = self::$path . 'home';
            header("Location: $ruta");
            exit;
        }
    }

    public function actionIndex($var = null)
    {
        // Redirigimos por defecto a la bandeja global de tickets sin asignar
        static::path();
        header("Location: " . self::$path . "agente/global");
        exit;
    }

    public function actionGlobal()
    {
        static::path();

        // 1. Instanciamos el modelo de Tickets
        $ticketsModel = new TicketsModel();

        // 2. Obtenemos todos los tickets abiertos y sin agente asignado
        $tickets_globales = $ticketsModel->listarTicketsSinAsignar();

        // 3. Preparamos los parámetros que recibirá la vista
        $parametros_de_vista = [
            "head"             => SiteController::head(),
            "header"           => SiteController::header(),
            "topbar"           => SiteController::topbar(),
            "menu"             => SiteController::menu(),
            "tickets_globales" => $tickets_globales
        ];

        // 4. Renderizamos la vista 'global' dentro de la subcarpeta 'agente'
        Response::render($this->viewDir(__NAMESPACE__), 'global', $parametros_de_vista);
    }

    public function actionTomar($id = null)
    {
        static::path();

        if (!$id) {
            header("Location: " . self::$path . "agente/global");
            exit;
        }

        $ticketsModel = new TicketsModel();
        
        $ticket = $ticketsModel->obtenerPorId($id);
        if (!$ticket) {
            header("Location: " . self::$path . "agente/global");
            exit;
        }

        $id_agente = $_SESSION['USER']['id_usuario'];

        // Intentamos actualizar el estado y asignar el agente
        if ($ticketsModel->actualizarEstado($id, 'En Proceso', $id_agente)) {
            // En lugar de redirigir de inmediato, recargamos la bandeja global 
            // pero pasándole un parámetro GET de éxito para disparar el modal
            header("Location: " . self::$path . "agente/global?tomado=exito");
            exit;
        } else {
            header("Location: " . self::$path . "agente/global");
            exit;
        }
    }

    public function actionPersonal()
    {
        static::path();

        // 1. Obtenemos el ID del agente logueado en la sesión actual
        $id_agente = $_SESSION['USER']['id_usuario'];

        // 2. Instanciamos el modelo de Tickets
        $ticketsModel = new TicketsModel();

        // 3. Obtenemos exclusivamente los tickets asignados a este agente
        $mis_tickets = $ticketsModel->listarTicketsPorAgente($id_agente);

        // 4. Preparamos los parámetros para la vista
        $parametros_de_vista = [
            "head"        => SiteController::head(),
            "header"      => SiteController::header(),
            "topbar"      => SiteController::topbar(),
            "menu"        => SiteController::menu(),
            "mis_tickets" => $mis_tickets
        ];

        // 5. Renderizamos la vista 'personal' dentro de la subcarpeta 'agente'
        Response::render($this->viewDir(__NAMESPACE__), 'personal', $parametros_de_vista);
    }

    public function actionVer($id = null)
    {
        static::path();

        if (!$id) {
            header("Location: " . self::$path . "agente/personal");
            exit;
        }

        $ticketsModel = new TicketsModel();
        $ticket = $ticketsModel->obtenerPorId($id);

        // 1. Validamos que el ticket exista y esté asignado a este agente
        $id_agente = $_SESSION['USER']['id_usuario'];
        if (!$ticket || $ticket->id_agente != $id_agente) {
            header("Location: " . self::$path . "agente/personal");
            exit;
        }

        $mensajesModel = new MensajesModel();
        $error = '';

        // 2. Si el agente envía una respuesta por el chat (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validamos Regla de Negocio RN03: Un ticket Cerrado es de solo lectura
            if ($ticket->estado === 'Cerrado') {
                $error = "Este ticket ya se encuentra cerrado. No admite nuevos mensajes.";
            } else {
                $contenido = trim($_POST['contenido'] ?? '');

                if (!empty($contenido)) {
                    $nuevoMensaje = [
                        'id_ticket'  => $id,
                        'id_usuario' => $id_agente,
                        'contenido'  => $contenido
                    ];

                    if ($mensajesModel->insertarMensaje($nuevoMensaje)) {
                        // Patrón PRG: Recargamos para limpiar el POST y evitar envíos duplicados
                        header("Location: " . self::$path . "agente/ver/" . $id);
                        exit;
                    } else {
                        $error = "No se pudo enviar la respuesta. Intentá nuevamente.";
                    }
                } else {
                    $error = "El mensaje no puede estar vacío.";
                }
            }
        }

        // 3. Obtenemos el hilo de mensajes del ticket
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

    public function actionCerrar($id = null)
    {
        static::path();

        if (!$id) {
            header("Location: " . self::$path . "agente/personal");
            exit;
        }

        $ticketsModel = new TicketsModel();
        $ticket = $ticketsModel->obtenerPorId($id);
        $id_agente = $_SESSION['USER']['id_usuario'];

        // Verificamos que el ticket exista, sea de este agente y no esté ya cerrado (RN03)
        if ($ticket && $ticket->id_agente == $id_agente && $ticket->estado !== 'Cerrado') {
            $ticketsModel->actualizarEstado($id, 'Cerrado', $id_agente);
        }

        // Redirigimos de vuelta a la vista del ticket para ver el estado actualizado y bloqueado
        header("Location: " . self::$path . "agente/ver/" . $id . "?cerrado=exito");
        exit;
    }
}