<?php
/**
 * Modelo Catálogo
 * Maneja categoria, marcas y unidad_medida para los combos
 */

class Catalogo {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Obtener todas las categorías activas
     */
    public function obtenerCategorias() {
        $sql = "SELECT Id_Categoria, Nombre FROM categoria WHERE Estado = 'Activo' ORDER BY Nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todas las marcas
     */
    public function obtenerMarcas() {
        $sql = "SELECT Id_Marca, Nombre FROM marcas ORDER BY Nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todas las unidades de medida
     */
    public function obtenerUnidades() {
        $sql = "SELECT Id_Unidad, Nombre, Abreviatura FROM unidad_medida ORDER BY Nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}

