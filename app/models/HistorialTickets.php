<?php

namespace app\models;

use \Model;
use \DataBase;

class HistorialTicketsModel extends Model {

    /**
     * Registra un nuevo evento en el historial del ticket
     * Recibe un array con: id_ticket, id_usuario, accion, detalle
     * Retorna el resultado de la ejecución
     */
    public function registrarAccion($datos) {
        // Insertamos la acción. fecha_accion se completa sola con current_timestamp()
        $sql = "INSERT INTO historial_tickets (id_ticket, id_usuario, accion, detalle) 
                VALUES (:id_ticket, :id_usuario, :accion, :detalle)";
        
        $params = [
            ':id_ticket'  => $datos['id_ticket'],
            ':id_usuario' => $datos['id_usuario'],
            ':accion'     => $datos['accion'],
            ':detalle'    => $datos['detalle']
        ];

        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene toda la línea de tiempo de un ticket específico
     * Recibe el ID del ticket
     * Retorna un arreglo de objetos ordenados cronológicamente
     */
    public function obtenerHistorialPorTicket($id_ticket) {
        // Seleccionamos el historial y lo ordenamos del más antiguo al más nuevo (ASC)
        $sql = "SELECT * FROM historial_tickets 
                WHERE id_ticket = :id_ticket 
                ORDER BY fecha_accion ASC";
        
        $params = [
            ':id_ticket' => $id_ticket
        ];

        // Usamos query() porque un ticket tendrá múltiples acciones en su historial
        return DataBase::query($sql, $params);
    }
}