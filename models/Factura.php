<?php
require_once __DIR__."/../config/db.php";

class Factura {
    private $pdo;

    public function __construct($pdo) { 
        $this->pdo = $pdo; 
    }

    // Listar todas las facturas
    public function listar() {
        $sql = "SELECT f.id, c.nombre AS cliente, f.fecha, f.subtotal, f.iva, f.total
                FROM facturas f 
                JOIN clientes c ON f.cliente_id=c.id
                ORDER BY f.id DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar cliente por identificación o nombre
    public function buscarCliente($busqueda) {
        $sql = "SELECT * FROM clientes WHERE identificacion LIKE :b OR nombre LIKE :b LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':b' => "%$busqueda%"]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar todos los productos disponibles
    public function listarProductos() {
        $sql = "SELECT * FROM productos WHERE stock>0 ORDER BY nombre";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear factura y detalle con IVA 15%
    public function crear($cliente_id, $usuario_id, $productos) {
        try {
            $this->pdo->beginTransaction();

            $subtotal = 0;
            foreach($productos as $p){
                $subtotal += $p['cantidad'] * $p['precio_unitario'];
            }
            $iva = $subtotal * 0.15; // 15% IVA
            $total = $subtotal + $iva;

            // Insertar factura
            $stmt = $this->pdo->prepare("INSERT INTO facturas (cliente_id, usuario_id, subtotal, iva, total) VALUES (:cliente_id, :usuario_id, :subtotal, :iva, :total)");
            $stmt->execute([
                ':cliente_id' => $cliente_id,
                ':usuario_id' => $usuario_id,
                ':subtotal' => $subtotal,
                ':iva' => $iva,
                ':total' => $total
            ]);

            $factura_id = $this->pdo->lastInsertId();

            // Insertar detalle
            $stmtDetalle = $this->pdo->prepare("INSERT INTO detalle_factura (factura_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (:factura_id, :producto_id, :cantidad, :precio_unitario, :subtotal)");
            foreach($productos as $p){
                $stmtDetalle->execute([
                    ':factura_id' => $factura_id,
                    ':producto_id' => $p['producto_id'],
                    ':cantidad' => $p['cantidad'],
                    ':precio_unitario' => $p['precio_unitario'],
                    ':subtotal' => $p['cantidad'] * $p['precio_unitario']
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return $e->getMessage();
        }
    }

    // Obtener detalle de una factura
    public function detalle($factura_id) {
        $sqlFactura = "SELECT f.id, f.fecha, c.nombre AS cliente, c.identificacion, f.subtotal, f.iva, f.total
                       FROM facturas f
                       JOIN clientes c ON f.cliente_id = c.id
                       WHERE f.id = :id";
        $stmt = $this->pdo->prepare($sqlFactura);
        $stmt->execute([':id' => $factura_id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlDetalle = "SELECT p.nombre, df.cantidad, df.precio_unitario, df.subtotal
                       FROM detalle_factura df
                       JOIN productos p ON df.producto_id = p.id
                       WHERE df.factura_id = :id";
        $stmt2 = $this->pdo->prepare($sqlDetalle);
        $stmt2->execute([':id' => $factura_id]);
        $detalle = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return ['factura' => $factura, 'detalle' => $detalle];
    }
}
?>
