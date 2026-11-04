<?php

namespace app\models;

use \Model;
use \DataBase;

class RecuperacionModel extends Model {

    /**
     * Guarda un token de recuperación para un usuario
     * Recibe el ID del usuario, el token generado y la fecha de expiración calculada
     */
    public function guardarToken($id_usuario, $token, $fecha_expiracion) {
        $sql = "INSERT INTO recuperacion_passwords (id_usuario, token, fecha_expiracion) 
                VALUES (:id_usuario, :token, :fecha_expiracion)";
        
        $params = [
            ':id_usuario'       => $id_usuario,
            ':token'            => $token,
            ':fecha_expiracion' => $fecha_expiracion
        ];

        return DataBase::execute($sql, $params);
    }

    /**
     * Verifica si un token es válido y no ha expirado
     * Retorna el objeto con los datos del token o false
     */
    public function obtenerPorToken($token) {
        // Buscamos el token y le pedimos a MySQL que solo lo traiga si la fecha_expiracion 
        // es mayor (>) a la fecha y hora exacta de este mismo instante (NOW())
        $sql = "SELECT * FROM recuperacion_passwords 
                WHERE token = :token AND fecha_expiracion > NOW()";
        
        $params = [
            ':token' => $token
        ];

        return DataBase::getRecord($sql, $params);
    }

    /**
     * Elimina los tokens asociados a un usuario (útil después de un cambio de clave exitoso)
     */
    public function eliminarTokensPorUsuario($id_usuario) {
        // ¡Ojo aquí! A diferencia de los otros modelos donde hacíamos "borrado lógico", 
        // aquí SÍ hacemos un DELETE físico. Los tokens son basura temporal de un solo uso; 
        // no necesitamos mantener auditoría de tokens viejos en el sistema.
        $sql = "DELETE FROM recuperacion_passwords WHERE id_usuario = :id_usuario";
        
        $params = [
            ':id_usuario' => $id_usuario
        ];

        return DataBase::execute($sql, $params);
    }
}