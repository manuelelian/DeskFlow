<?php

namespace app\controllers;

use \Controller;
use \Response;
use app\models\TicketsModel;
use app\models\MensajesModel;
use app\models\UserModel;
use app\models\HistorialTicketsModel;
use app\models\ArchivosModel;

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
            // Registramos la asignación en el historial
            $historialModel = new HistorialTicketsModel();
            $historialModel->registrarAccion([
                'id_ticket'  => $id,
                'id_usuario' => $id_agente,
                'accion'     => 'Ticket Tomado',
                'detalle'    => 'El agente tomó el ticket y pasó a estado En Proceso.'
            ]);

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
                        // Obtenemos el ID del mensaje recién insertado usando PDO
                        $id_mensaje = \DataBase::connection()->lastInsertId();

                        // Procesamos el archivo adjunto si fue enviado
                        if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
                            $archivo = $_FILES['adjunto'];
                            $tamanoMaximo = 5 * 1024 * 1024; // 5 MB

                            $mimesPermitidos = [
                                'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
                                'text/plain', 'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ];

                            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx'];
                            $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeReal = finfo_file($finfo, $archivo['tmp_name']);
                            finfo_close($finfo);

                            if ($archivo['size'] > $tamanoMaximo) {
                                $error = "El archivo excede el tamaño máximo permitido de 5MB.";
                            } elseif (!in_array($ext, $extensionesPermitidas) || !in_array($mimeReal, $mimesPermitidos)) {
                                $error = "Tipo de archivo no permitido. Solo se permiten imágenes, PDF, DOC o TXT.";
                            } else {
                                $nombreGuardado = uniqid('adjunto_', true) . '.' . $ext;
                                $directorioDestino = 'public/uploads/';
                                $rutaCompleta = $directorioDestino . $nombreGuardado;

                                if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                                    $archivosModel = new ArchivosModel();
                                    $archivosModel->guardarArchivo([
                                        'id_ticket'       => $id,
                                        'id_mensaje'      => $id_mensaje,
                                        'nombre_original' => $archivo['name'],
                                        'ruta_archivo'    => $rutaCompleta,
                                        'tipo_mime'       => $mimeReal,
                                        'tamano_bytes'    => $archivo['size'],
                                        'creado_por'      => $id_agente
                                    ]);
                                }
                            }
                        }

                        if (empty($error)) {
                            // Patrón PRG: Recargamos para limpiar el POST y evitar envíos duplicados
                            header("Location: " . self::$path . "agente/ver/" . $id);
                            exit;
                        }
                    } else {
                        $error = "No se pudo enviar la respuesta. Intentá nuevamente.";
                    }
                } else {
                    $error = "El mensaje no puede estar vacío.";
                }
            }
        }

        // 3. Obtenemos el hilo de mensajes del ticket y los archivos adjuntos
        $mensajes = $mensajesModel->obtenerMensajesPorTicket($id);
        $archivosModel = new ArchivosModel();
        $archivos = $archivosModel->obtenerArchivosPorTicket($id);

        $archivosPorMensaje = [];
        $archivosTicket = [];
        foreach ($archivos as $archivo) {
            if (!empty($archivo->id_mensaje)) {
                $archivosPorMensaje[$archivo->id_mensaje][] = $archivo;
            } else {
                $archivosTicket[] = $archivo;
            }
        }

        // Obtenemos la lista de agentes activos para poder reasignar el ticket
        $userModel = new UserModel();
        $agentes = $userModel->listarPorRol(2);
        
        // 4. Preparamos los parámetros para la vista
        $parametros_de_vista = [
            "head"                => SiteController::head(),
            "header"              => SiteController::header(),
            "topbar"              => SiteController::topbar(),
            "menu"                => SiteController::menu(),
            "ticket"              => $ticket,
            "mensajes"            => $mensajes,
            "archivosPorMensaje"  => $archivosPorMensaje,
            "archivosTicket"      => $archivosTicket,
            "agentes"             => $agentes,
            "error"               => $error
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
            if ($ticketsModel->actualizarEstado($id, 'Cerrado', $id_agente)) {
                // Registramos el cierre en el historial
                $historialModel = new HistorialTicketsModel();
                $historialModel->registrarAccion([
                    'id_ticket'  => $id,
                    'id_usuario' => $id_agente,
                    'accion'     => 'Ticket Cerrado',
                    'detalle'    => 'El agente dio por resuelto y cerró el ticket.'
                ]);
            }
        }

        // Redirigimos de vuelta a la vista del ticket para ver el estado actualizado y bloqueado
        header("Location: " . self::$path . "agente/ver/" . $id . "?cerrado=exito");
        exit;
    }
    
    public function actionCambiarPrioridad()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_ticket = filter_input(INPUT_POST, 'id_ticket', FILTER_VALIDATE_INT);
            $prioridad = $_POST['prioridad'] ?? '';
            $id_agente = $_SESSION['USER']['id_usuario'];
            
            $prioridades_validas = ['Baja', 'Media', 'Alta', 'Critica'];

            if ($id_ticket && in_array($prioridad, $prioridades_validas)) {
                $ticketsModel = new TicketsModel();
                $ticket = $ticketsModel->obtenerPorId($id_ticket);

                // Verificamos que el ticket pertenezca al agente actual
                if ($ticket && $ticket->id_agente == $id_agente) {
                    if ($ticketsModel->actualizarPrioridad($id_ticket, $prioridad, $id_agente)) {
                        $historialModel = new HistorialTicketsModel();
                        $historialModel->registrarAccion([
                            'id_ticket'  => $id_ticket,
                            'id_usuario' => $id_agente,
                            'accion'     => 'Cambio de Prioridad',
                            'detalle'    => 'El agente cambió la prioridad del ticket a: ' . $prioridad
                        ]);
                    }
                }
                header("Location: " . self::$path . "agente/ver/" . $id_ticket . "?cambio=prioridad");
                exit;
            }
        }
        header("Location: " . self::$path . "agente/personal");
        exit;
    }

    public function actionReasignarTicket()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_ticket = filter_input(INPUT_POST, 'id_ticket', FILTER_VALIDATE_INT);
            $nuevo_agente = filter_input(INPUT_POST, 'id_agente', FILTER_VALIDATE_INT);
            $id_agente_actual = $_SESSION['USER']['id_usuario'];

            if ($id_ticket && $nuevo_agente && $nuevo_agente != $id_agente_actual) {
                $ticketsModel = new TicketsModel();
                $ticket = $ticketsModel->obtenerPorId($id_ticket);

                // Verificamos que el ticket pertenezca al agente que lo quiere derivar
                if ($ticket && $ticket->id_agente == $id_agente_actual) {
                    if ($ticketsModel->reasignarAgente($id_ticket, $nuevo_agente, $id_agente_actual)) {
                        $historialModel = new HistorialTicketsModel();
                        $historialModel->registrarAccion([
                            'id_ticket'  => $id_ticket,
                            'id_usuario' => $id_agente_actual,
                            'accion'     => 'Ticket Reasignado',
                            'detalle'    => 'El agente derivó el ticket al agente ID #' . $nuevo_agente
                        ]);
                        
                        // Como ya no es suyo, lo redirigimos a su bandeja personal
                        header("Location: " . self::$path . "agente/personal?reasignado=exito");
                        exit;
                    }
                }
            }
        }
        header("Location: " . self::$path . "agente/personal");
        exit;
    }

    public function actionReabrirTicket()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_ticket = filter_input(INPUT_POST, 'id_ticket', FILTER_VALIDATE_INT);
            $id_agente = $_SESSION['USER']['id_usuario'];

            if ($id_ticket) {
                $ticketsModel = new TicketsModel();
                $ticket = $ticketsModel->obtenerPorId($id_ticket);

                // Validamos que el ticket sea de este agente y esté Cerrado
                if ($ticket && $ticket->id_agente == $id_agente && $ticket->estado === 'Cerrado') {
                    if ($ticketsModel->reabrirTicket($id_ticket, $id_agente)) {
                        $historialModel = new HistorialTicketsModel();
                        $historialModel->registrarAccion([
                            'id_ticket'  => $id_ticket,
                            'id_usuario' => $id_agente,
                            'accion'     => 'Ticket Reabierto',
                            'detalle'    => 'El agente reabrió el ticket (vuelve a estado En Proceso).'
                        ]);
                    }
                }
                header("Location: " . self::$path . "agente/ver/" . $id_ticket . "?cambio=reabierto");
                exit;
            }
        }
        header("Location: " . self::$path . "agente/personal");
        exit;
    }
}