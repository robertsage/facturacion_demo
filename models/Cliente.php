<?php
require_once __DIR__."/../config/db.php";

class Cliente {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function listar() {
        return $this->pdo->query("SELECT * FROM clientes")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        $sql = "INSERT INTO clientes (tipo, identificacion, nombre, direccion, telefono, email) 
                VALUES (:tipo,:identificacion,:nombre,:direccion,:telefono,:email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':tipo', $data['tipo']);
        $stmt->bindParam(':identificacion', $data['identificacion']);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':email', $data['email']);
        return $stmt->execute();
    }

    public function actualizar($data) {
        $sql = "UPDATE clientes 
                SET tipo = :tipo, identificacion = :identificacion, nombre = :nombre,
                    direccion = :direccion, telefono = :telefono, email = :email
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':tipo', $data['tipo']);
        $stmt->bindParam(':identificacion', $data['identificacion']);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id', $data['id']);
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Verificar si el cliente tiene facturas
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM facturas WHERE cliente_id = :id");
        $stmt->execute(['id' => $id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar este cliente porque tiene facturas asociadas.'
            ];
        }

        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);

        return $result ? 
            ['success' => true, 'message' => 'Cliente eliminado correctamente.'] :
            ['success' => false, 'message' => 'Error al eliminar el cliente.'];
    }

    public function buscarCliente($busqueda) {
        $sql = "SELECT * FROM clientes WHERE identificacion LIKE :b OR nombre LIKE :b LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':b' => "%$busqueda%"]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
