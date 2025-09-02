<?php
require_once __DIR__."/../models/Factura.php";
require_once __DIR__."/../config/db.php";

$model = new Factura($pdo);
$facturas = $model->listar();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Facturas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Listado de Facturas</h2>
<table>
<tr>
<th>ID</th>
<th>Cliente</th>
<th>Fecha</th>
<th>Total</th>
<th>Acciones</th>
</tr>
<?php foreach($facturas as $f): ?>
<tr>
<td><?= $f['id'] ?></td>
<td><?= $f['cliente'] ?></td>
<td><?= $f['fecha'] ?></td>
<td><?= number_format($f['total'],2) ?></td>
<td><a href="detalle_factura.php?id=<?= $f['id'] ?>">Ver detalle</a></td>
</tr>
<?php endforeach; ?>
</table>
<a href="crear_factura.php">Crear nueva factura</a>
 <a href="../index.php">Volver al inicio</a>
</body>
</html>
