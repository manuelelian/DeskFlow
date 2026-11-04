<?php

namespace app\models; // Todo en minúsculas para respetar las carpetas de Linux

use \Model;    // Usamos la barra invertida porque es una clase Global del profe
use \DataBase; // DataBase también es una clase Global del profe

class UserModel extends Model {

    // (Eliminamos el __construct porque la clase Model del profe no lo tiene)

    /**
     * Busca un usuario activo por su email
     * Retorna un Objeto con los datos del usuario o false si no existe
     */
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND fecha_eliminacion IS NULL";
        
        // Armamos un arreglo con los parámetros a reemplazar en la consulta
        $params = [':email' => $email];
        
        // Usamos el método estático getRecord del profesor.
        // Esto automáticamente prepara la consulta, bindea el parámetro y devuelve 1 solo registro.
        return DataBase::getRecord($sql, $params);
    }

    /**
     * Crea un nuevo usuario en la base de datos
     * Recibe un array asociativo con los datos: id_rol, nombre, apellido, email, password
     * Retorna el resultado de la ejecución (booleano o el ID insertado dependiendo de cómo funcione execute)
     */
    public function crearUsuario($datos) {
        $sql = "INSERT INTO usuarios (id_rol, nombre, apellido, email, password) 
                VALUES (:id_rol, :nombre, :apellido, :email, :password)";
        
        $params = [
            ':id_rol'   => $datos['id_rol'],
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':email'    => $datos['email'],
            ':password' => $datos['password']
        ];

        // Usamos el método estático execute() del profesor para insertar los datos
        return DataBase::execute($sql, $params);
    }
    
    /**
     * Obtiene la lista de todos los usuarios activos en el sistema
     * Retorna un arreglo de objetos con los datos de los usuarios
     */
    public function listarUsuarios() {
        // Seleccionamos todos los usuarios que no han sido eliminados lógicamente
        $sql = "SELECT * FROM usuarios WHERE fecha_eliminacion IS NULL";
        
        // Usamos el método estático query() del profesor para obtener múltiples registros
        return DataBase::query($sql);
    }

    /**
     * Obtiene los datos de un usuario por su ID
     * Retorna un Objeto con los datos o false si no existe / está eliminado
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':id' => $id
        ];
        
        // Usamos getRecord porque buscamos a un único usuario
        return DataBase::getRecord($sql, $params);
    }

    /**
     * Actualiza únicamente la contraseña de un usuario
     * Recibe el ID del usuario y la nueva contraseña (que ya debe venir hasheada desde el controlador)
     * Retorna el resultado de la ejecución
     */
    public function actualizarPassword($id, $passwordHasheada) {
        $sql = "UPDATE usuarios SET password = :password WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':password' => $passwordHasheada,
            ':id'       => $id
        ];
        
        // Usamos el método estático execute() del profesor para realizar el UPDATE
        return DataBase::execute($sql, $params);
    }

    /**
     * Actualiza los datos básicos del perfil de un usuario
     * Recibe el ID del usuario y un array asociativo con los nuevos datos (nombre, apellido)
     * Retorna el resultado de la ejecución
     */
    public function actualizarPerfil($id, $datos) {
        $sql = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':id'       => $id
        ];
        
        // Usamos el método estático execute() del profesor para realizar el UPDATE
        return DataBase::execute($sql, $params);
    }

    /**
     * Realiza un borrado lógico del usuario (desactivación)
     * Recibe el ID del usuario a eliminar y el ID del usuario que ejecuta la acción
     * Retorna el resultado de la ejecución
     */
    public function eliminarLogico($id, $eliminado_por) {
        // Usamos NOW() de MySQL para guardar la fecha y hora exacta del momento
        $sql = "UPDATE usuarios 
                SET fecha_eliminacion = NOW(), eliminado_por = :eliminado_por 
                WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':eliminado_por' => $eliminado_por,
            ':id'            => $id
        ];
        
        // Ejecutamos el UPDATE con la función del profesor
        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene la lista de usuarios activos filtrados por su rol
     * Recibe el ID del rol (Ej: 1 para Cliente, 2 para Agente y 3 para el Administrador.)
     * Retorna un arreglo de objetos
     */
    public function listarPorRol($id_rol) {
        $sql = "SELECT * FROM usuarios WHERE id_rol = :id_rol AND fecha_eliminacion IS NULL";
        
        $params = [
            ':id_rol' => $id_rol
        ];
        
        // Usamos query() porque esperamos múltiples resultados
        return DataBase::query($sql, $params);
    }

    /**
     * Permite al Administrador editar los datos maestros de un usuario (incluyendo rol y email)
     * Registra auditoría con modificado_por
     */
    public function actualizarUsuarioAdmin($id, $datos, $modificado_por) {
        $sql = "UPDATE usuarios 
                SET nombre = :nombre, 
                    apellido = :apellido, 
                    email = :email, 
                    id_rol = :id_rol, 
                    modificado_por = :modificado_por 
                WHERE id = :id AND fecha_eliminacion IS NULL";

        $params = [
            ':nombre'         => $datos['nombre'],
            ':apellido'       => $datos['apellido'],
            ':email'          => $datos['email'],
            ':id_rol'         => $datos['id_rol'],
            ':modificado_por' => $modificado_por,
            ':id'             => $id
        ];

        return \DataBase::execute($sql, $params);
    }

    /**
     * Obtiene TODOS los usuarios (Activos e Inactivos) para el panel del Administrador
     */
    public function listarTodosLosUsuarios() {
        $sql = "SELECT * FROM usuarios ORDER BY id ASC";
        return DataBase::query($sql);
    }

    /**
     * Reactiva un usuario previamente desactivado (borrado lógico) y registra auditoría
     */
    public function reactivarUsuario($id, $modificado_por) {
        $sql = "UPDATE usuarios 
                SET fecha_eliminacion = NULL, 
                    eliminado_por = NULL, 
                    fecha_actualizacion = NOW(), 
                    modificado_por = :modificado_por 
                WHERE id = :id";
        
        $params = [
            ':modificado_por' => $modificado_por,
            ':id'             => $id
        ];
        
        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene TODOS los clientes y agentes (Roles 1 y 2) activos e inactivos.
     * Excluye a los administradores para proteger la jerarquía en la gestión.
     */
    public function listarClientesYAgentes() {
        $sql = "SELECT * FROM usuarios WHERE id_rol IN (1, 2) ORDER BY id ASC";
        return DataBase::query($sql);
    }
}