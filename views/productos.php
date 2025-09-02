<?php
require_once __DIR__."/../models/Producto.php";
require_once __DIR__."/../config/db.php";

$model = new Producto($pdo);
$message = '';

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD']=='POST') {
    if (isset($_POST['crear'])) {
        $resultado = $model->crear($_POST);
        $message = ($resultado === true) ? "Producto creado correctamente." : $resultado;
    }

    if (isset($_POST['actualizar'])) {
        $resultado = $model->actualizar($_POST);
        $message = ($resultado === true) ? "Producto actualizado correctamente." : $resultado;
    }

    if (isset($_POST['eliminar'])) {
        $model->eliminar($_POST['id']);
        $message = "Producto eliminado correctamente.";
    }
}

$productos = $model->listar();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Productos</title>
    <style>
        body { font-family: Arial; padding:20px; background:#f5f5f5; }
        table { border-collapse: collapse; width: 100%; background:#fff; padding:10px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#eee; }
        input, select, button { padding:5px; margin:2px; }
        .btn { cursor:pointer; background:#4CAF50; color:white; border:none; border-radius:4px; }
        .btn:hover { background:#45a049; }
        .form-container { background:#fff; padding:20px; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.1); margin-bottom:20px; }
    </style>
    <script>
        function editarProducto(id, codigo, nombre, precio, stock, iva){
            document.getElementById('id').value = id;
            document.getElementById('codigo').value = codigo;
            document.getElementById('nombre').value = nombre;
            document.getElementById('precio').value = precio;
            document.getElementById('stock').value = stock;
            document.getElementById('iva').value = iva;
            document.getElementById('guardarBtn').style.display = 'none';
            document.getElementById('actualizarBtn').style.display = 'inline-block';
        }

        function limpiarForm(){
            document.getElementById('productoForm').reset();
            document.getElementById('id').value = '';
            document.getElementById('guardarBtn').style.display = 'inline-block';
            document.getElementById('actualizarBtn').style.display = 'none';
        }
    </script>
</head>
<body>
<div class="form-container">
    <h2>Gestión de Productos</h2>

    <?php if($message): ?>
        <p style="color:green; font-weight:bold;"><?= $message ?></p>
    <?php endif; ?>

    <form id="productoForm" method="post">
        <input type="hidden" name="id" id="id">
        <input type="text" name="codigo" id="codigo" placeholder="Código" required>
        <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
        <input type="number" step="0.01" name="precio" id="precio" placeholder="Precio" required>
        <input type="number" name="stock" id="stock" placeholder="Stock" required>
        <select name="iva" id="iva">
            <option value="si">Con IVA</option>
            <option value="no">Sin IVA</option>
        </select>
        <br><br>
        <button type="submit" name="crear" id="guardarBtn" class="btn">Guardar</button>
        <button type="submit" name="actualizar" id="actualizarBtn" class="btn" style="display:none;">Actualizar</button>
        <button type="button" class="btn" onclick="limpiarForm()">Limpiar</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Código</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>IVA</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($productos as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= $p['codigo'] ?></td>
        <td><?= $p['nombre'] ?></td>
        <td><?= $p['precio'] ?></td>
        <td><?= $p['stock'] ?></td>
        <td><?= $p['iva'] ?></td>
        <td>
            <button class="btn" onclick="editarProducto('<?= $p['id'] ?>','<?= $p['codigo'] ?>','<?= $p['nombre'] ?>','<?= $p['precio'] ?>','<?= $p['stock'] ?>','<?= $p['iva'] ?>')">Editar</button>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button name="eliminar" class="btn" style="background:red;">Eliminar</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="../index.php">Volver al inicio</a>
</body>
</html>
