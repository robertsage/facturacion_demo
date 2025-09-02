<?php
require_once __DIR__."/../config/db.php";

class Producto {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function listar() {
        return $this->pdo->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        // Validar que el código no exista
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE codigo=:codigo");
        $stmt->execute(['codigo'=>$data['codigo']]);
        if($stmt->fetchColumn() > 0){
            return "El código de producto ya existe";
        }

        $sql = "INSERT INTO productos (codigo,nombre,precio,stock,iva) 
                VALUES (:codigo,:nombre,:precio,:stock,:iva)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':codigo', $data['codigo']);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':precio', $data['precio']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':iva', $data['iva']);    
        return $stmt->execute();
    }

    public function actualizar($data) {
        // Validar que el código no exista en otro registro
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE codigo=:codigo AND id!=:id");
        $stmt->execute(['codigo'=>$data['codigo'], 'id'=>$data['id']]);
        if($stmt->fetchColumn() > 0){
            return "El código de producto ya existe en otro registro";
        }

        $sql = "UPDATE productos SET codigo=:codigo, nombre=:nombre, precio=:precio, stock=:stock, iva=:iva
                WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':codigo', $data['codigo']);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':precio', $data['precio']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':iva', $data['iva']);  
        $stmt->bindParam(':id', $data['id']);  
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id=:id");
        return $stmt->execute(['id'=>$id]);
    }
}
?>
