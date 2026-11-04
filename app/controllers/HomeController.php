<?php

namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;
use app\models\UserModel;

class HomeController extends Controller
{

    // Constructor
    public function __construct()
    {
        self::$sessionStatus = SessionController::sessionVerificacion();
    }


    public function actionIndex($var = null)
    {
        // Bloqueamos el acceso si no hay sesión
        SessionController::onlyLogin();     

        // 1. Instanciamos el modelo de usuarios
        $userModel = new UserModel();
        
        // 2. Usamos el método correcto que armamos en la Fase 2
        $usuarios = $userModel->listarUsuarios();
        
        // var_dump($usuarios); // Descomentalo si querés ver los datos crudos por pantalla

        static::path();
        $nombre_de_archivoDeVista = 'home';
        
        // 3. Preparamos los datos para la vista
        $parametros_de_vista = [
            "head"     => SiteController::head(),
            "header"   => SiteController::header(),
            "topbar"   => SiteController::topbar(),
            "menu"     => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "usuarios" => $usuarios // Agregamos los usuarios para usarlos en el HTML
        ]; 
        
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }
}