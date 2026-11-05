<?php

namespace app\controllers;

use \Controller;
use \Response;
use app\models\TicketsModel;
use app\models\CategoriasModel;
use app\models\MensajesModel;
use app\models\HistorialTicketsModel;
use app\models\ArchivosModel;

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
                        // Obtenemos el ID del mensaje recién insertado usando la conexión PDO
                        $id_mensaje = \DataBase::connection()->lastInsertId();

                        // Procesamos el archivo adjunto si fue enviado
                        if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
                            $archivo = $_FILES['adjunto'];
                            $tamanoMaximo = 5 * 1024 * 1024; // 5 MB en bytes

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
                                        'creado_por'      => $id_cliente
                                    ]);
                                }
                            }
                        }

                        if (empty($error)) {
                            // Recargamos la página para limpiar el POST y mostrar el mensaje nuevo
                            header("Location: " . self::$path . "cliente/ver/" . $id);
                            exit;
                        }
                    } else {
                        $error = "No se pudo enviar el mensaje. Intentá nuevamente.";
                    }
                } else {
                    $error = "El mensaje no puede estar vacío.";
                }
            }
        }

        // 3. Obtenemos todo el hilo de mensajes del ticket y los archivos adjuntos
        $mensajes = $mensajesModel->obtenerMensajesPorTicket($id);
        $archivosModel = new ArchivosModel();
        $archivos = $archivosModel->obtenerArchivosPorTicket($id);

        // Agrupamos los archivos por id_mensaje para mostrarlos fácilmente en la vista
        $archivosPorMensaje = [];
        $archivosTicket = [];
        foreach ($archivos as $archivo) {
            if (!empty($archivo->id_mensaje)) {
                $archivosPorMensaje[$archivo->id_mensaje][] = $archivo;
            } else {
                $archivosTicket[] = $archivo;
            }
        }

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
            "error"               => $error
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'ver', $parametros_de_vista);
    }

    public function actionResolverTicket()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_ticket = $_POST['id_ticket'] ?? null;
            $mensaje_resolucion = trim($_POST['mensaje_resolucion'] ?? '');
            $id_cliente = $_SESSION['USER']['id_usuario'];

            if ($id_ticket && !empty($mensaje_resolucion)) {
                $ticketsModel = new TicketsModel();
                $ticket = $ticketsModel->obtenerPorId($id_ticket);

                // Validamos que el ticket exista, sea del cliente y no esté cerrado (RN04 y RN03)
                if ($ticket && $ticket->id_cliente == $id_cliente && $ticket->estado !== 'Cerrado') {
                    
                    // 1. Guardamos el motivo como mensaje final en el chat
                    $mensajesModel = new MensajesModel();
                    $mensajesModel->insertarMensaje([
                        'id_ticket'  => $id_ticket,
                        'id_usuario' => $id_cliente,
                        'contenido'  => "✅ TICKET RESUELTO POR EL CLIENTE:\n" . $mensaje_resolucion
                    ]);

                    // 2. Cerramos el ticket
                    $ticketsModel->actualizarEstado($id_ticket, 'Cerrado');

                    // 3. Disparamos el registro en el historial
                    $historialModel = new HistorialTicketsModel();
                    $historialModel->registrarAccion([
                        'id_ticket'  => $id_ticket,
                        'id_usuario' => $id_cliente,
                        'accion'     => 'Cierre por Cliente',
                        'detalle'    => 'El cliente marcó el ticket como resuelto y dejó un mensaje de cierre.'
                    ]);

                    // Redirigimos al detalle para ver el ticket cerrado
                    header("Location: " . self::$path . "cliente/ver/" . $id_ticket);
                    exit;
                }
            }
        }
        
        // Si hay error o acceso inválido, redirigimos al dashboard del cliente
        header("Location: " . self::$path . "cliente");
        exit;
    }
}