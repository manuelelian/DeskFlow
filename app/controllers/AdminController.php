<?php

namespace app\controllers;

use \Controller;
use \Response;
use app\models\UserModel;
use app\models\CategoriasModel;
use app\models\TicketsModel;
use app\models\MensajesModel;
use app\models\HistorialTicketsModel;
use app\models\ArchivosModel;

class AdminController extends Controller
{
    public function __construct()
    {
        // 1. Verificamos que exista una sesión activa (Middleware del profesor)
        self::$sessionStatus = SessionController::sessionVerificacion();
        SessionController::onlyLogin();

        // 2. Seguridad estricta: Si NO es Administrador (Rol 3), lo expulsamos al home
        if (SessionController::getRol() != 3) {
            static::path();
            $ruta = self::$path . 'home';
            header("Location: $ruta");
            exit;
        }
    }

    public function actionIndex($var=null)
    {
        static::path();

        // 3. Preparamos los parámetros base para la vista del Dashboard Admin
        $parametros_de_vista = [
            "head"   => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu"   => SiteController::menu()
        ];

        // 4. Renderizamos la vista 'index' dentro de la subcarpeta 'admin'
        Response::render($this->viewDir(__NAMESPACE__), 'index', $parametros_de_vista);
    }

    public function actionUsuarios()
    {
        static::path();
        // 1. Instanciamos el modelo de usuarios
        $userModel = new UserModel();
        // 2. Traemos solo a Clientes (1) y Agentes (2) para la gestión
        $usuarios = $userModel->listarClientesYAgentes();
        // 3. Empaquetamos los datos para la vista
        $parametros_de_vista = [
            "head"     => SiteController::head(),
            "header"   => SiteController::header(),
            "topbar"   => SiteController::topbar(),
            "menu"     => SiteController::menu(),
            "usuarios" => $usuarios
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'usuarios', $parametros_de_vista);
    }

    public function actionCrearAgente()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // 1. Validaciones básicas de seguridad
            if (!empty($nombre) && !empty($apellido) && !empty($email) && !empty($password)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $userModel = new UserModel();

                    // Comprobamos que el email no esté registrado
                    if (!$userModel->buscarPorEmail($email)) {
                        $nuevoAgente = [
                            'id_rol'   => 2, // 2 = Agente (Regla RN01)
                            'nombre'   => $nombre,
                            'apellido' => $apellido,
                            'email'    => $email,
                            'password' => password_hash($password, PASSWORD_DEFAULT)
                        ];

                        $userModel->crearUsuario($nuevoAgente);
                    }
                }
            }
        }

        // Redirigimos siempre al listado de usuarios tras procesar
        header("Location: " . self::$path . "admin/usuarios");
        exit;
    }

    public function actionEliminarUsuario($id = null)
    {
        static::path();

        if ($id) {
            $admin_actual = $_SESSION['USER']['id_usuario'];

            // Evitamos que el administrador se desactive a sí mismo accidentalmente
            if ($id != $admin_actual) {
                $userModel = new UserModel();
                // Ejecutamos el borrado lógico con auditoría RN05
                $userModel->eliminarLogico($id, $admin_actual);
            }
        }

        header("Location: " . self::$path . "admin/usuarios");
        exit;
    }

    public function actionEditarUsuario()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id       = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nombre   = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $id_rol   = filter_input(INPUT_POST, 'id_rol', FILTER_VALIDATE_INT);

            // Validamos que los datos vengan completos y el rol sea válido (1: Cliente, 2: Agente, 3: Admin)
            if ($id && !empty($nombre) && !empty($apellido) && !empty($email) && in_array($id_rol, [1, 2, 3])) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $userModel = new UserModel();

                    // Verificamos si el email ya existe en otro usuario distinto al que estamos editando
                    $usuarioConMismoEmail = $userModel->buscarPorEmail($email);
                    if (!$usuarioConMismoEmail || $usuarioConMismoEmail->id == $id) {
                        
                        $datosActualizar = [
                            'nombre'   => $nombre,
                            'apellido' => $apellido,
                            'email'    => $email,
                            'id_rol'   => $id_rol
                        ];

                        $admin_actual = $_SESSION['USER']['id_usuario'];
                        
                        // Ejecutamos la actualización con auditoría RN05
                        $userModel->actualizarUsuarioAdmin($id, $datosActualizar, $admin_actual);
                    }
                }
            }
        }

        // Redirigimos siempre a la lista de usuarios
        header("Location: " . self::$path . "admin/usuarios");
        exit;
    }

    public function actionCategorias()
    {
        static::path();

        $categoriasModel = new CategoriasModel();
        // Traemos TODAS las categorías (Activas e Inactivas) para el panel del Admin
        $categorias = $categoriasModel->listarTodasLasCategorias();

        $parametros_de_vista = [
            "head"       => SiteController::head(),
            "header"     => SiteController::header(),
            "topbar"     => SiteController::topbar(),
            "menu"       => SiteController::menu(),
            "categorias" => $categorias
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'categorias', $parametros_de_vista);
    }

    public function actionCrearCategoria()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            if (!empty($nombre)) {
                $categoriasModel = new CategoriasModel();
                $nuevaCategoria = [
                    'nombre'      => $nombre,
                    'descripcion' => $descripcion,
                    'creado_por'  => $_SESSION['USER']['id_usuario'] // Auditoría RN05
                ];
                $categoriasModel->agregarCategoria($nuevaCategoria);
            }
        }

        header("Location: " . self::$path . "admin/categorias");
        exit;
    }

    public function actionEditarCategoria()
    {
        static::path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nombre      = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            if ($id && !empty($nombre)) {
                $categoriasModel = new CategoriasModel();
                $datos = [
                    'id'             => $id,
                    'nombre'         => $nombre,
                    'descripcion'    => $descripcion,
                    'modificado_por' => $_SESSION['USER']['id_usuario'] // Auditoría RN05
                ];
                $categoriasModel->actualizarCategoria($datos);
            }
        }

        header("Location: " . self::$path . "admin/categorias");
        exit;
    }

    public function actionEliminarCategoria($id = null)
    {
        static::path();

        if ($id) {
            $categoriasModel = new CategoriasModel();
            $admin_actual = $_SESSION['USER']['id_usuario'];
            $categoriasModel->eliminarLogico($id, $admin_actual); // Auditoría RN05
        }

        header("Location: " . self::$path . "admin/categorias");
        exit;
    }

    public function actionReactivarCategoria($id = null)
    {
        static::path();

        if ($id) {
            $categoriasModel = new CategoriasModel();
            $admin_actual = $_SESSION['USER']['id_usuario'];
            
            // Reactivamos la categoría y registramos la auditoría de modificación
            $categoriasModel->reactivarCategoria($id, $admin_actual);
        }

        header("Location: " . self::$path . "admin/categorias");
        exit;
    }

    public function actionTickets()
    {
        static::path();

        // 1. Instanciamos el modelo de Tickets
        $ticketsModel = new TicketsModel();

        // 2. Traemos absolutamente todos los tickets activos del sistema
        $tickets = $ticketsModel->listarTodosLosTickets();

        // 3. Preparamos los parámetros para la vista
        $parametros_de_vista = [
            "head"    => SiteController::head(),
            "header"  => SiteController::header(),
            "topbar"  => SiteController::topbar(),
            "menu"    => SiteController::menu(),
            "tickets" => $tickets
        ];

        // 4. Renderizamos la vista 'tickets' dentro de app/views/admin/
        Response::render($this->viewDir(__NAMESPACE__), 'tickets', $parametros_de_vista);
    }

    public function actionEliminarTicket($id = null)
    {
        static::path();

        if ($id) {
            $ticketsModel = new \app\models\TicketsModel();
            $admin_actual = $_SESSION['USER']['id_usuario'];
            // Ejecutamos el borrado lógico respetando la auditoría
            $ticketsModel->eliminarLogico($id, $admin_actual);
        }

        header("Location: " . self::$path . "admin/tickets");
        exit;
    }

    public function actionVerTicket($id = null)
    {
        static::path();

        if (!$id) {
            header("Location: " . self::$path . "admin/tickets");
            exit;
        }

        // 1. Buscamos los datos del ticket
        $ticketsModel = new TicketsModel();
        $ticket = $ticketsModel->obtenerPorId($id);

        if (!$ticket) {
            header("Location: " . self::$path . "admin/tickets");
            exit;
        }

        // 2. Traemos todos los mensajes activos de ese ticket y los archivos adjuntos
        $mensajesModel = new MensajesModel();
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

        // 3. Preparamos los parámetros para la vista
        $parametros_de_vista = [
            "head"                => SiteController::head(),
            "header"              => SiteController::header(),
            "topbar"              => SiteController::topbar(),
            "menu"                => SiteController::menu(),
            "ticket"              => $ticket,
            "mensajes"            => $mensajes,
            "archivosPorMensaje"  => $archivosPorMensaje,
            "archivosTicket"      => $archivosTicket
        ];

        // 4. Renderizamos la vista 'ver_ticket' dentro de app/views/admin/
        Response::render($this->viewDir(__NAMESPACE__), 'ver_ticket', $parametros_de_vista);
    }

    public function actionEliminarMensaje($id_mensaje = null)
    {
        static::path();

        if ($id_mensaje) {
            $mensajesModel = new MensajesModel();
            
            // 1. Obtenemos el mensaje antes de borrarlo para saber a qué ticket volver
            $mensaje = $mensajesModel->obtenerPorId($id_mensaje);

            if ($mensaje) {
                $admin_actual = $_SESSION['USER']['id_usuario'];
                
                // 2. Aplicamos el borrado lógico guardando quién lo moderó
                $mensajesModel->eliminarLogico($id_mensaje, $admin_actual);

                // 3. Redirigimos de vuelta al detalle de ese ticket con aviso de éxito
                header("Location: " . self::$path . "admin/verTicket/" . $mensaje->id_ticket . "?moderado=exito");
                exit;
            }
        }

        header("Location: " . self::$path . "admin/tickets");
        exit;
    }

    public function actionReactivarUsuario($id = null)
    {
        static::path();

        if ($id) {
            $userModel = new UserModel();
            $admin_actual = $_SESSION['USER']['id_usuario'];
            
            // Reactivamos el usuario y registramos quién lo hizo
            $userModel->reactivarUsuario($id, $admin_actual);
        }

        header("Location: " . self::$path . "admin/usuarios");
        exit;
    }
}