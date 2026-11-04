<?php

namespace app\controllers;

use app\controllers\UserController; // Del profe
use app\models\UserModel;
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
}