<?php
require_once __DIR__."/../models/Factura.php";
require_once __DIR__."/../config/db.php";

if(!isset($_GET['id'])){
    die("Factura no especificada.");
}

$model = new Factura($pdo);
$factura_id = intval($_GET['id']);
$data = $model->detalle($factura_id);

if(!$data['factura']){
    die("Factura no encontrada.");
}

$factura = $data['factura'];
$detalle = $data['detalle'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detalle de Factura #<?= $factura['id'] ?></title>
    <style>
        body { font-family: Arial; background:#f5f5f5; padding:20px; }
        .container { background:#fff; padding:20px; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:90%; max-width:800px; margin:auto; }
        h2,h3 { color:#333; }
        table { width:100%; border-collapse: collapse; margin-top:10px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#eee; }
        .totales { margin-top:10px; font-weight:bold; text-align:right; }
        .btn { padding:5px 10px; margin:5px 0; cursor:pointer; background:#4CAF50; color:white; border:none; border-radius:4px; text-decoration:none; }
        .btn:hover { background:#45a049; }
    </style>
</head>
<body>
<div class="container">
<h2>Factura #<?= $factura['id'] ?></h2>
<p><strong>Cliente:</strong> <?= htmlspecialchars($factura['cliente']) ?></p>
<p><strong>Fecha:</strong> <?= $factura['fecha'] ?></p>

<h3>Productos</h3>
<table>
    <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio Unitario</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach($detalle as $d): ?>
    <tr>
        <td><?= htmlspecialchars($d['nombre']) ?></td>
        <td><?= $d['cantidad'] ?></td>
        <td>$<?= number_format($d['precio_unitario'],2) ?></td>
        <td>$<?= number_format($d['subtotal'],2) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="totales">
    Subtotal: $<?= number_format($factura['subtotal'],2) ?><br>
    IVA (15%): $<?= number_format($factura['iva'],2) ?><br>
    Total: $<?= number_format($factura['total'],2) ?>
</div>

<a href="facturas.php" class="btn">Volver a Facturas</a>
</div>
</body>
</html>

