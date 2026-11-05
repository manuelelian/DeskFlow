<?php

namespace app\controllers;

use app\controllers\UserController; // Del profe
use app\models\UserModel;
use app\models\RecuperacionModel;
require_once APP_PATH . "helpers/BrevoHelper.php";
use app\helpers\BrevoHelper;
use \Controller;
use \Response;
use \DataBase;

class AccountController extends Controller
{
    public function __construct()
    {
        // Verifica la sesión usando el método del profe
        self::$sessionStatus = SessionController::sessionVerificacion();
    }

    // =========================================================================
    // NUESTRO MÉTODO DE REGISTRO ADAPTADO AL ESTILO DEL PROFESOR
    // =========================================================================
    public function actionRegistro()
    {
        // Si está logueado, lo mandamos al inicio
        if (self::$sessionStatus === "Online") {
            static::path();
            $ruta = self::$path . 'home';
            header("Location: $ruta");
            exit;
        }

        static::path(); // Carga las rutas relativas del profe
        
        $datos = [];
        $error_registro = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre           = trim($_POST['nombre'] ?? '');
            $apellido         = trim($_POST['apellido'] ?? '');
            $email            = trim($_POST['email'] ?? '');
            $password         = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $datos = ['nombre' => $nombre, 'apellido' => $apellido, 'email' => $email];

            // Validaciones de seguridad
            if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
                $error_registro = "Todos los campos son obligatorios.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_registro = "El formato del correo electrónico no es válido.";
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
                $error_registro = "La contraseña no cumple con los requisitos de seguridad.";
            } elseif ($password !== $confirm_password) {
                $error_registro = "Las contraseñas no coinciden.";
            } else {
                $userModel = new UserModel();
                if ($userModel->buscarPorEmail($email)) {
                    $error_registro = "El email ya está registrado. Intenta iniciar sesión.";
                } else {
                    $nuevoUsuario = [
                        'id_rol'   => 1, 
                        'nombre'   => $nombre,
                        'apellido' => $apellido,
                        'email'    => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT)
                    ];

                    if ($userModel->crearUsuario($nuevoUsuario)) {
                        // Usamos la ruta dinámica del profesor para ir al login
                        $ruta = self::$path . 'account/login';
                        header("Location: $ruta");
                        exit;
                    } else {
                        $error_registro = "Hubo un error al crear la cuenta.";
                    }
                }
            }
        }

        // Renderizado usando la clase Response del profesor
        $parametros_de_vista = [
            "error_registro" => $error_registro,
            "datos"          => $datos
        ];
        Response::render($this->viewDir(__NAMESPACE__), 'registro', $parametros_de_vista);
    }

    // =========================================================================
    // MÉTODOS DEL PROFESOR CONSERVADOS Y ACTUALIZADO EL LOGIN
    // =========================================================================
    
    public function actionLogin()
    {
        // Si ya está logueado, lo mandamos al inicio o a su dashboard correspondiente
        if (self::$sessionStatus === "Online") {
            static::path();
            $rol = SessionController::getRol();
            $ruta = ($rol == 1) ? self::$path . 'cliente' : self::$path . 'home';
            header("Location: $ruta");
            exit;
        }

        static::path();

        $email_ingresado = '';
        $error_login = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Guardamos el email para repoblar la vista si hay error
            $email_ingresado = $email;

            if (empty($email) || empty($password)) {
                $error_login = "Por favor, completa todos los campos.";
            } else {
                // Buscamos al usuario en la BD usando el modelo de la Fase 2
                $userModel = new UserModel();
                $usuario = $userModel->buscarPorEmail($email);

                // Verificamos: 1. Que el usuario exista | 2. Que la contraseña coincida
                if (!$usuario || !password_verify($password, $usuario->password)) {
                    // Por seguridad, el error es genérico.
                    $error_login = "Credenciales inválidas.";
                } else {
                    // Credenciales correctas: Delegamos la creación de la sesión al SessionController
                    if (SessionController::setSessionData($usuario)) {
                        // Redirección inteligente basada en el rol (RN01)
                        $rol = SessionController::getRol();
                        if ($rol == 1) {
                            $ruta = self::$path . 'cliente'; // Dashboard del Cliente
                        } elseif ($rol == 2) {
                            $ruta = self::$path . 'agente';  // Panel del Agente
                        } elseif ($rol == 3) {
                            $ruta = self::$path . 'admin';   // Panel del Admin
                        } else {
                            $ruta = self::$path . 'home';
                        }
                        
                        header("Location: $ruta");
                        exit;
                    } else {
                        $error_login = "Error interno al iniciar sesión.";
                    }
                }
            }
        }

        // Pasamos a la vista las variables configuradas en login.php
        $parametros_de_vista = [
            "error_login"     => $error_login,
            "email_ingresado" => $email_ingresado
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'login', $parametros_de_vista);
    }
    
    public function actionSalir(){
        session_destroy();
        static::path();
        $ruta = self::$path.'account/login/';
        $_SESSION['SESSION']['STATUS'] = false;
        header("Location: $ruta");
    }

    // =========================================================================
    // FLUJO DE RECUPERACIÓN DE CONTRASEÑA (RecuperacionModel + Brevo)
    // =========================================================================

    public function actionRecuperar_contrasena()
    {
        if (self::$sessionStatus === "Online") {
            static::path();
            $ruta = self::$path . 'home';
            header("Location: $ruta");
            exit;
        }

        static::path();

        $error = '';
        $exito = '';
        $email = '';
        $link_directo = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $error = "Por favor, ingresá tu correo electrónico.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El formato del correo electrónico no es válido.";
            } else {
                $userModel = new UserModel();
                $usuario = $userModel->buscarPorEmail($email);

                // Mensaje genérico por seguridad para prevenir enumeración de usuarios
                $exito = "Si el correo coincide con una cuenta activa, te enviamos las instrucciones para restablecer tu contraseña.";

                if ($usuario) {
                    // Generamos un token criptográficamente seguro
                    $token = bin2hex(random_bytes(32));
                    // El token expira en 1 hora
                    $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $recuperacionModel = new RecuperacionModel();
                    // Eliminamos tokens previos no usados para este usuario
                    $recuperacionModel->eliminarTokensPorUsuario($usuario->id);
                    // Guardamos el nuevo token
                    $recuperacionModel->guardarToken($usuario->id, $token, $fecha_expiracion);

                    // Construimos la URL completa para el correo
                    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $linkRecuperacion = $protocolo . $host . self::$path . 'account/restablecer_contrasena?token=' . $token;

                    // Enviamos por Brevo
                    $resultadoBrevo = BrevoHelper::enviarCorreoRecuperacion(
                        $usuario->email,
                        $usuario->nombre . ' ' . $usuario->apellido,
                        $linkRecuperacion
                    );

                    // Si Brevo no está configurado (modo desarrollo local), facilitamos el link en pantalla
                    if (!$resultadoBrevo['exito']) {
                        $link_directo = $linkRecuperacion;
                    }
                }
            }
        }

        $parametros_de_vista = [
            "error"        => $error,
            "exito"        => $exito,
            "email"        => $email,
            "link_directo" => $link_directo
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'recuperar_contrasena', $parametros_de_vista);
    }

    public function actionRestablecer_contrasena()
    {
        if (self::$sessionStatus === "Online") {
            static::path();
            $ruta = self::$path . 'home';
            header("Location: $ruta");
            exit;
        }

        static::path();

        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        $error = '';

        if (empty($token)) {
            header("Location: " . self::$path . "account/login");
            exit;
        }

        $recuperacionModel = new RecuperacionModel();
        $registroToken = $recuperacionModel->obtenerPorToken($token);

        if (!$registroToken) {
            $error = "El enlace de recuperación es inválido o ha expirado. Por favor, solicitá uno nuevo.";
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = $_POST['password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';

                if (empty($password) || empty($confirm_password)) {
                    $error = "Por favor, completá todos los campos.";
                } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
                    $error = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un caracter especial.";
                } elseif ($password !== $confirm_password) {
                    $error = "Las contraseñas no coinciden.";
                } else {
                    $userModel = new UserModel();
                    $passwordHasheada = password_hash($password, PASSWORD_DEFAULT);

                    // Actualizamos la contraseña del usuario
                    if ($userModel->actualizarPassword($registroToken->id_usuario, $passwordHasheada)) {
                        // Eliminamos el token de un solo uso
                        $recuperacionModel->eliminarTokensPorUsuario($registroToken->id_usuario);

                        // Redireccionamos al login con confirmación
                        header("Location: " . self::$path . "account/login");
                        exit;
                    } else {
                        $error = "Ocurrió un error al intentar actualizar la contraseña. Intentá nuevamente.";
                    }
                }
            }
        }

        $parametros_de_vista = [
            "token" => $token,
            "error" => $error
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'restablecer_contrasena', $parametros_de_vista);
    }
}