<?php

namespace app\models;

use \Model;
use \DataBase;

class TicketsModel extends Model {

    /**
     * Crea un nuevo ticket en la base de datos
     * Recibe un array asociativo con: id_cliente, id_categoria, titulo, descripcion, prioridad
     * El estado por defecto será 'Abierto' (manejado por MySQL o pasado por parámetro)
     * Retorna el resultado de la ejecución
     */
    public function crearTicket($datos) {
        $sql = "INSERT INTO tickets (id_cliente, id_categoria, titulo, descripcion, prioridad, estado) 
                VALUES (:id_cliente, :id_categoria, :titulo, :descripcion, :prioridad, 'Abierto')";
        
        $params = [
            ':id_cliente'   => $datos['id_cliente'],
            ':id_categoria' => $datos['id_categoria'],
            ':titulo'       => $datos['titulo'],
            ':descripcion'  => $datos['descripcion'],
            ':prioridad'    => $datos['prioridad']
        ];

        // Usamos el método estático execute() del profesor para insertar los datos
        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene todos los tickets creados por un cliente específico
     * Recibe el ID del cliente
     * Retorna un arreglo de objetos con los tickets
     */
    public function listarTicketsPorCliente($id_cliente) {
        // Seleccionamos los tickets del cliente, ordenados del más reciente al más antiguo
        // y nos aseguramos de que no estén eliminados lógicamente
        $sql = "SELECT * FROM tickets 
                WHERE id_cliente = :id_cliente AND fecha_eliminacion IS NULL 
                ORDER BY fecha_creacion DESC";
        
        $params = [
            ':id_cliente' => $id_cliente
        ];

        // Usamos query() porque un cliente puede tener múltiples tickets
        return DataBase::query($sql, $params);
    }

    /**
     * Obtiene todos los tickets que están abiertos y aún no tienen un agente asignado
     * Retorna un arreglo de objetos con los tickets
     */
    public function listarTicketsSinAsignar() {
        // Seleccionamos tickets abiertos, sin agente, no eliminados, del más viejo al más nuevo
        $sql = "SELECT * FROM tickets 
                WHERE estado = 'Abierto' 
                AND id_agente IS NULL 
                AND fecha_eliminacion IS NULL 
                ORDER BY fecha_creacion ASC";
        
        // No hay parámetros dinámicos, usamos query() directamente
        return DataBase::query($sql);
    }

    /**
     * Actualiza el estado de un ticket y opcionalmente el agente asignado o la fecha de cierre
     * Recibe el ID del ticket, el nuevo estado y el ID del agente que toma el caso
     * Retorna el resultado de la ejecución
     */
    public function actualizarEstado($id_ticket, $nuevo_estado, $id_agente = null) {
        // La consulta base siempre actualiza el estado
        $sql = "UPDATE tickets SET estado = :estado";
        $params = [
            ':estado' => $nuevo_estado,
            ':id'     => $id_ticket
        ];

        // Si se está asignando a un agente (cambiando a "En Proceso"), actualizamos el id_agente
        if ($id_agente !== null) {
            $sql .= ", id_agente = :id_agente";
            $params[':id_agente'] = $id_agente;
        }

        // Si el estado es "Cerrado", actualizamos la fecha_cierre
        if ($nuevo_estado === 'Cerrado') {
             $sql .= ", fecha_cierre = NOW()";
        }

        // Finalizamos la consulta con el WHERE
        $sql .= " WHERE id = :id AND fecha_eliminacion IS NULL";

        // Usamos execute() para realizar el UPDATE
        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene todos los detalles de un ticket específico mediante su ID
     * Retorna un Objeto con los datos del ticket o false si no existe
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM tickets WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':id' => $id
        ];
        
        // Usamos getRecord porque el ID es único y solo esperamos un resultado
        return DataBase::getRecord($sql, $params);
    }

    /**
     * Obtiene todos los tickets asignados a un agente específico
     * Recibe el ID del agente
     * Retorna un arreglo de objetos con los tickets
     */
    public function listarTicketsPorAgente($id_agente) {
        // Seleccionamos los tickets asignados al agente que no estén eliminados lógicamente
        // Ordenamos por prioridad (suponiendo que 'Critica' o 'Alta' deban atenderse primero)
        // y luego por fecha de creación (los más antiguos primero).
        $sql = "SELECT * FROM tickets 
                WHERE id_agente = :id_agente AND fecha_eliminacion IS NULL 
                ORDER BY prioridad DESC, fecha_creacion ASC";
        
        $params = [
            ':id_agente' => $id_agente
        ];

        // Usamos query() porque un agente puede tener múltiples tickets asignados
        return DataBase::query($sql, $params);
    }

    /**
     * Obtiene TODOS los tickets activos del sistema con los nombres de cliente, agente y categoría.
     * Uso exclusivo para Supervisión Global del Administrador.
     */
    public function listarTodosLosTickets()
    {
        $sql = "SELECT t.*, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido,
                       a.nombre AS agente_nombre, a.apellido AS agente_apellido,
                       cat.nombre AS categoria_nombre
                FROM tickets t
                INNER JOIN usuarios c ON t.id_cliente = c.id
                LEFT JOIN usuarios a ON t.id_agente = a.id
                INNER JOIN categorias cat ON t.id_categoria = cat.id
                WHERE t.fecha_eliminacion IS NULL 
                ORDER BY t.fecha_creacion DESC";

        return \DataBase::query($sql);
    }

    /**
     * Realiza un borrado lógico de un ticket (desactivación/moderación)
     * Recibe el ID del ticket y el ID del administrador que ejecuta la acción
     */
    public function eliminarLogico($id, $eliminado_por) {
        $sql = "UPDATE tickets 
                SET fecha_eliminacion = NOW(), eliminado_por = :eliminado_por 
                WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':eliminado_por' => $eliminado_por,
            ':id'            => $id
        ];
        
        return DataBase::execute($sql, $params);
    }
    
}