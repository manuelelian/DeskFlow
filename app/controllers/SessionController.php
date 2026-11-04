<?php 

namespace app\controllers;

use \Controller;
use app\models\UserModel;

class SessionController extends Controller
{
    private static function setSession(){
        if (!isset($_SESSION['SESSION']['STATUS'])) {
            $_SESSION['SESSION']['STATUS'] = false;
        }
    }

    public static function sessionVerificacion(){
        self::setSession();
        return $_SESSION['SESSION']['STATUS'] ? 'Online' : 'OffLine';
    }

    public static function setSessionData($usuario){
        if (isset($usuario->nombre) && isset($usuario->apellido) && isset($usuario->email) && isset($usuario->id)) {
            $_SESSION['SESSION']['STATUS']  = true;
            $_SESSION['USER']['name']       = $usuario->nombre;
            $_SESSION['USER']['lastName']   = $usuario->apellido;
            $_SESSION['USER']['mail']       = $usuario->email;     
            $_SESSION['USER']['id_usuario'] = $usuario->id;
            $_SESSION['USER']['id_rol']     = $usuario->id_rol; 
            return true;
        }
        return false;
    }

    public static function getRol(){
        return $_SESSION['USER']['id_rol'] ?? null;
    }

    public static function onlyAdmins(){
        $status = self::sessionVerificacion();
        if ($status === 'OffLine' || self::getRol() != 3) {
            header("Location: /DeskFlow/account/login");
            exit;
        }
        return true;
    }

    public static function onlyLogin(){
        $status = self::sessionVerificacion();
        if ($status === 'OffLine') {
            header("Location: /DeskFlow/account/login");
            exit;
        }
        return true;
    }
}