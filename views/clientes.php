<?php
require_once __DIR__."/../models/Cliente.php";
require_once __DIR__."/../config/db.php";

$model = new Cliente($pdo);
$message = '';

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD']=='POST') {
    if (isset($_POST['crear'])) {
        $resultado = $model->crear($_POST);
        $message = ($resultado === true) ? "Cliente creado correctamente." : $resultado;
    }

    if (isset($_POST['actualizar'])) {
        $resultado = $model->actualizar($_POST);
        $message = ($resultado === true) ? "Cliente actualizado correctamente." : $resultado;
    }

    if (isset($_POST['eliminar'])) {
        $result = $model->eliminar($_POST['id']);
        $message = $result['message'];
    }
}

$clientes = $model->listar();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clientes</title>
    <style>
        body { font-family: Arial; padding:20px; background:#f5f5f5; }
        table { border-collapse: collapse; width: 100%; background:#fff; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#eee; }
        input, select, button { padding:5px; margin:2px; }
        .btn { cursor:pointer; background:#4CAF50; color:white; border:none; border-radius:4px; }
        .btn:hover { background:#45a049; }
        .form-container { background:#fff; padding:20px; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.1); margin-bottom:20px; }
    </style>
    <script>
        function editarCliente(id, tipo, identificacion, nombre, direccion, telefono, email){
            document.getElementById('id').value = id;
            document.getElementById('tipo').value = tipo;
            document.getElementById('identificacion').value = identificacion;
            document.getElementById('nombre').value = nombre;
            document.getElementById('direccion').value = direccion;
            document.getElementById('telefono').value = telefono;
            document.getElementById('email').value = email;
            document.getElementById('guardarBtn').style.display = 'inline-block';
            document.getElementById('actualizarBtn').style.display = 'inline-block';
        }

        function limpiarForm(){
            document.getElementById('clienteForm').reset();
            document.getElementById('id').value = '';
            document.getElementById('actualizarBtn').style.display = 'none';
        }
    </script>
</head>
<body>
<div class="form-container">
    <h2>Gestión de Clientes</h2>

    <?php if($message): ?>
        <p style="color:green; font-weight:bold;"><?= $message ?></p>
    <?php endif; ?>

    <form id="clienteForm" method="post">
        <input type="hidden" name="id" id="id">
        <select name="tipo" id="tipo" required>
            <option value="Natural">Persona Natural</option>
            <option value="Jurídica">Persona Jurídica</option>
        </select><br><br>
        <input type="text" name="identificacion" id="identificacion" placeholder="Identificación" required><br><br>
        <input type="text" name="nombre" id="nombre" placeholder="Nombre" required><br><br>
        <input type="text" name="direccion" id="direccion" placeholder="Dirección"><br><br>
        <input type="text" name="telefono" id="telefono" placeholder="Teléfono"><br><br>
        <input type="email" name="email" id="email" placeholder="Email"><br><br>
        <button type="submit" name="crear" id="guardarBtn" class="btn">Guardar</button>
        <button type="submit" name="actualizar" id="actualizarBtn" class="btn" style="display:none;">Actualizar</button>
        <button type="button" class="btn" onclick="limpiarForm()">Limpiar</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Identificación</th>
        <th>Nombre</th>
        <th>Dirección</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($clientes as $c): ?>
    <tr>
        <td><?= $c['id'] ?></td>
        <td><?= $c['tipo'] ?></td>
        <td><?= $c['identificacion'] ?></td>
        <td><?= $c['nombre'] ?></td>
        <td><?= $c['direccion'] ?></td>
        <td><?= $c['telefono'] ?></td>
        <td><?= $c['email'] ?></td>
        <td>
            <button class="btn" onclick="editarCliente(
                '<?= $c['id'] ?>',
                '<?= $c['tipo'] ?>',
                '<?= $c['identificacion'] ?>',
                '<?= $c['nombre'] ?>',
                '<?= $c['direccion'] ?>',
                '<?= $c['telefono'] ?>',
                '<?= $c['email'] ?>'
            )">Editar</button>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
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
