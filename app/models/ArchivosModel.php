<?php

namespace app\models;

use \Model;
use \DataBase;

class ArchivosModel extends Model {

    /**
     * Guarda el registro de un archivo subido en la base de datos
     * Recibe un array con: id_ticket, id_mensaje (opcional), nombre_original, ruta_archivo, tipo_mime, tamano_bytes, creado_por
     * Retorna el resultado de la ejecución
     */
    public function guardarArchivo($datos) {
        $sql = "INSERT INTO archivos (id_ticket, id_mensaje, nombre_original, ruta_archivo, tipo_mime, tamano_bytes, creado_por) 
                VALUES (:id_ticket, :id_mensaje, :nombre_original, :ruta_archivo, :tipo_mime, :tamano_bytes, :creado_por)";
        
        $params = [
            ':id_ticket'       => $datos['id_ticket'],
            // Usamos un operador ternario: si no viene id_mensaje, insertamos null
            ':id_mensaje'      => isset($datos['id_mensaje']) ? $datos['id_mensaje'] : null,
            ':nombre_original' => $datos['nombre_original'],
            ':ruta_archivo'    => $datos['ruta_archivo'],
            ':tipo_mime'       => $datos['tipo_mime'],
            ':tamano_bytes'    => $datos['tamano_bytes'],
            ':creado_por'      => $datos['creado_por']
        ];

        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene todos los archivos asociados a un ticket específico
     * Retorna un arreglo de objetos con los datos de los archivos
     */
    public function obtenerArchivosPorTicket($id_ticket) {
        // Traemos los archivos del ticket que no estén eliminados, del más viejo al más nuevo
        $sql = "SELECT * FROM archivos 
                WHERE id_ticket = :id_ticket AND fecha_eliminacion IS NULL 
                ORDER BY fecha_creacion ASC";
        
        $params = [
            ':id_ticket' => $id_ticket
        ];

        return DataBase::query($sql, $params);
    }

    /**
     * Realiza un borrado lógico de un archivo
     * Recibe el ID del archivo y el ID del usuario que ejecuta la acción
     */
    public function eliminarLogico($id, $eliminado_por) {
        $sql = "UPDATE archivos 
                SET fecha_eliminacion = NOW(), eliminado_por = :eliminado_por 
                WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':eliminado_por' => $eliminado_por,
            ':id'            => $id
        ];
        
        return DataBase::execute($sql, $params);
    }

    /**
     * Obtiene los detalles de un archivo específico por su ID
     * Retorna un Objeto con los datos o false si no existe
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM archivos WHERE id = :id AND fecha_eliminacion IS NULL";
        
        $params = [
            ':id' => $id
        ];
        
        // Usamos getRecord() porque el ID es único
        return DataBase::getRecord($sql, $params);
    }

    /**
     * Obtiene todos los archivos adjuntos a un mensaje específico
     * Retorna un arreglo de objetos con los datos de los archivos
     */
    public function obtenerArchivosPorMensaje($id_mensaje) {
        $sql = "SELECT * FROM archivos 
                WHERE id_mensaje = :id_mensaje AND fecha_eliminacion IS NULL 
                ORDER BY fecha_creacion ASC";
        
        $params = [
            ':id_mensaje' => $id_mensaje
        ];

        // Usamos query() porque un mismo mensaje podría tener varios archivos adjuntos
        return DataBase::query($sql, $params);
    }
}