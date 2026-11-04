<?php

namespace app\models;

use \Model;
use \DataBase;

class MensajesModel extends Model {

    /**
     * Inserta un nuevo mensaje en el hilo de un ticket
     * Recibe un array asociativo con: id_ticket, id_usuario, contenido
     * Retorna el resultado de la ejecución
     */
    public function insertarMensaje($datos) {
        $sql = "INSERT INTO mensajes (id_ticket, id_usuario, contenido) 
                VALUES (:id_ticket, :id_usuario, :contenido)";
        
        $params = [
            ':id_ticket'  => $datos['id_ticket'],
            ':id_usuario' => $datos['id_usuario'],
            ':contenido'  => $datos['contenido']
        ];

        // Usamos el método estático execute() del profesor para insertar de forma segura
        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene todos los mensajes asociados a un ticket específico junto con los datos del autor
     * Recibe el ID del ticket
     * Retorna un arreglo de objetos con los mensajes, ordenados del más antiguo al más nuevo
     */
    public function obtenerMensajesPorTicket($id_ticket) {
        // Unimos la tabla mensajes (m) con usuarios (u) para traer nombre, apellido y rol del autor
        $sql = "SELECT m.*, u.nombre, u.apellido, u.id_rol 
                FROM mensajes m
                INNER JOIN usuarios u ON m.id_usuario = u.id
                WHERE m.id_ticket = :id_ticket AND m.fecha_eliminacion IS NULL 
                ORDER BY m.fecha_envio ASC";
        
        $params = [
            ':id_ticket' => $id_ticket
        ];

        // Usamos query() porque esperamos múltiples mensajes (un hilo)
        return DataBase::query($sql, $params);
    }

    /**
     * Obtiene los detalles de un mensaje específico por su ID
     * Retorna un Objeto con los datos o false si no existe
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM mensajes WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':id' => $id
        ];
        
        // Usamos getRecord porque es un ID único
        return DataBase::getRecord($sql, $params);
    }
    
    /**
     * Actualiza el contenido de un mensaje existente
     * Recibe el ID del mensaje y el nuevo contenido
     * Retorna el resultado de la ejecución
     */
    public function actualizarMensaje($id, $contenido) {
        $sql = "UPDATE mensajes 
                SET contenido = :contenido 
                WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':contenido' => $contenido,
            ':id'        => $id
        ];
        
        // Usamos execute() para realizar el UPDATE
        return DataBase::execute($sql, $params);
    }

    /**
     * Realiza un borrado lógico de un mensaje (lo oculta)
     * Recibe el ID del mensaje a eliminar y el ID del usuario que realiza la acción
     * Retorna el resultado de la ejecución
     */
    public function eliminarLogico($id, $eliminado_por) {
        $sql = "UPDATE mensajes 
                SET fecha_eliminacion = NOW(), eliminado_por = :eliminado_por 
                WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':eliminado_por' => $eliminado_por,
            ':id'            => $id
        ];
        
        // Usamos execute() para realizar el UPDATE
        return DataBase::execute($sql, $params);
    }
}