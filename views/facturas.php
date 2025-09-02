<?php
require_once __DIR__."/../models/Factura.php";
require_once __DIR__."/../config/db.php";

$modelFactura = new Factura($pdo);
$facturas = $modelFactura->listar();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listado de Facturas</title>
    <style>
        body { font-family: Arial; margin:20px; background:#f5f5f5; }
        h2 { color:#333; }
        table { border-collapse: collapse; width: 90%; background:#fff; padding:20px; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.1); margin-top:10px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#eee; }
        .btn { padding:5px 10px; margin:2px; cursor:pointer; text-decoration:none; background-color:#4CAF50; color:white; border-radius:4px; border:none; }
        .btn:hover { background:#45a049; }
        .top-bar { margin-bottom:10px; }
    </style>
</head>
<body>

<h2>Listado de Facturas</h2>

<div class="top-bar">
    <a href="crear_factura.php" class="btn">Crear Nueva Factura</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Subtotal</th>
        <th>IVA</th>
        <th>Total</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($facturas as $f): ?>
    <tr>
        <td><?= $f['id'] ?></td>
        <td><?= htmlspecialchars($f['cliente']) ?></td>
        <td><?= $f['fecha'] ?></td>
        <td>$<?= number_format($f['subtotal'],2) ?></td>
        <td>$<?= number_format($f['iva'],2) ?></td>
        <td>$<?= number_format($f['total'],2) ?></td>
        <td>
            <a href="detalle_factura.php?id=<?= $f['id'] ?>" class="btn">Ver Detalle</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
 <a href="../index.php">Volver al inicio</a>
</body>
</html>
