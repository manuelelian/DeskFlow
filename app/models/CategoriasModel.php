<?php

namespace app\models;

use \Model;
use \DataBase;

class CategoriasModel extends Model
{
    /**
     * Obtiene solo las categorías ACTIVAS (Usado por el Cliente al crear un ticket)
     */
    public function listarCategorias()
    {
        $sql = "SELECT * FROM categorias 
                WHERE fecha_eliminacion IS NULL AND estado = 'Activa' 
                ORDER BY nombre ASC";
        
        return DataBase::query($sql);
    }

    /**
     * Obtiene TODAS las categorías (Activas e Inactivas) para el panel del Administrador
     */
    public function listarTodasLasCategorias()
    {
        $sql = "SELECT * FROM categorias ORDER BY id ASC";
        
        return DataBase::query($sql);
    }

    /**
     * Obtiene los detalles de una categoría específica por su ID
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM categorias WHERE id = :id";
        $params = [':id' => $id];
        
        return DataBase::getRecord($sql, $params);
    }

    /**
     * Crea una nueva categoría en la base de datos (nace 'Activa' por defecto)
     */
    public function agregarCategoria($datos)
    {
        $sql = "INSERT INTO categorias (nombre, descripcion, estado, creado_por) 
                VALUES (:nombre, :descripcion, 'Activa', :creado_por)";
        
        $params = [
            ':nombre'      => $datos['nombre'],
            ':descripcion' => $datos['descripcion'],
            ':creado_por'  => $datos['creado_por']
        ];

        return DataBase::execute($sql, $params);
    }

    /**
     * Actualiza el nombre y descripción de una categoría registrando la auditoría
     */
    public function actualizarCategoria($id, $datos)
    {
        $sql = "UPDATE categorias 
                SET nombre = :nombre, 
                    descripcion = :descripcion, 
                    fecha_actualizacion = NOW(), 
                    modificado_por = :modificado_por 
                WHERE id = :id";

        $params = [
            ':nombre'         => $datos['nombre'],
            ':descripcion'    => $datos['descripcion'],
            ':modificado_por' => $datos['modificado_por'],
            ':id'             => $id
        ];

        return DataBase::execute($sql, $params);
    }

    /**
     * Desactiva una categoría (Borrado Lógico + cambio de estado a 'Inactiva')
     */
    public function eliminarLogico($id, $eliminado_por)
    {
        $sql = "UPDATE categorias 
                SET estado = 'Inactiva', 
                    fecha_eliminacion = NOW(), 
                    eliminado_por = :eliminado_por 
                WHERE id = :id";
        
        $params = [
            ':eliminado_por' => $eliminado_por,
            ':id'            => $id
        ];
        
        return DataBase::execute($sql, $params);
    }

    /**
     * Reactiva una categoría previamente desactivada y registra quién la modificó
     */
    public function reactivarCategoria($id, $modificado_por)
    {
        $sql = "UPDATE categorias 
                SET estado = 'Activa', 
                    fecha_eliminacion = NULL, 
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
}