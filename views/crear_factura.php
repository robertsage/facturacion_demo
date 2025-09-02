<?php
require_once __DIR__."/../models/Factura.php";
require_once __DIR__."/../models/Cliente.php";
require_once __DIR__."/../models/Producto.php";
require_once __DIR__."/../config/db.php";

$modelFactura = new Factura($pdo);
$modelCliente = new Cliente($pdo);
$modelProducto = new Producto($pdo);

$productos = $modelProducto->listar(); // productos disponibles
$mensaje = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cliente_id = $_POST['cliente_id'] ?? null;
    $usuario_id = 1; // demo
    $productosSeleccionados = [];

    if(isset($_POST['producto_id'])){
        foreach($_POST['producto_id'] as $index => $prod_id){
            $cantidad = $_POST['cantidad'][$index];
            $precio = $_POST['precio'][$index];
            if($cantidad > 0){
                $productosSeleccionados[] = [
                    'producto_id' => $prod_id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio
                ];
            }
        }
    }

    if(!$cliente_id){
        $mensaje = "Debe seleccionar un cliente válido.";
    } elseif(empty($productosSeleccionados)){
        $mensaje = "Debe seleccionar al menos un producto.";
    } else {
        $resultado = $modelFactura->crear($cliente_id, $usuario_id, $productosSeleccionados);
        $mensaje = ($resultado === true) ? "Factura creada correctamente." : "Error: $resultado";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Factura</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; padding:20px; }
        .container { background:#fff; padding:20px; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.1); max-width:900px; margin:auto; }
        h2,h3 { color:#333; margin-bottom:10px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#eee; }
        input[type="text"], input[type="number"], select { padding:5px; margin:3px; width:calc(100% - 12px); }
        .btn { padding:5px 10px; margin:3px; cursor:pointer; background:#4CAF50; color:white; border:none; border-radius:4px; }
        .btn:hover { background:#45a049; }
        .btn-red { background:#e74c3c; }
        .btn-red:hover { background:#c0392b; }
        .totales { margin-top:15px; font-weight:bold; }
        .mensaje { padding:10px; background:#d4edda; color:#155724; border-radius:4px; margin-bottom:15px; }
        .section { margin-bottom:20px; }
        #producto_select { width:auto; display:inline-block; }
    </style>
</head>
<body>
<div class="container">

    <h2>Crear Factura</h2>

    <?php if($mensaje): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form id="facturaForm" method="post">

        <!-- Sección Cliente -->
        <div class="section">
            <h3>Cliente</h3>
            <input type="text" id="cliente_buscar" placeholder="Nombre o identificación">
            <input type="hidden" name="cliente_id" id="cliente_id">
            <button type="button" class="btn" onclick="buscarCliente()">Buscar</button>
            <div id="cliente_info" style="margin-top:5px; font-weight:bold;"></div>
        </div>

        <!-- Sección Productos -->
        <div class="section">
            <h3>Productos</h3>
            <table id="tabla_productos">
                <tr><th>Producto</th><th>Precio Unitario</th><th>Cantidad</th><th>Acción</th></tr>
            </table>

            <div style="margin-top:10px;">
                <select id="producto_select">
                    <?php foreach($productos as $p): ?>
                        <option value="<?= $p['id'] ?>" data-precio="<?= $p['precio'] ?>"><?= htmlspecialchars($p['nombre']) ?> ($<?= number_format($p['precio'],2) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn" onclick="agregarProducto()">Agregar Producto</button>
            </div>
        </div>

        <!-- Totales -->
        <div class="section totales">
            Subtotal: $<span id="subtotal">0.00</span><br>
            IVA (15%): $<span id="iva">0.00</span><br>
            Total: $<span id="total">0.00</span>
        </div>

        <button type="submit" class="btn">Guardar Factura</button>
        <a href="facturas.php" class="btn btn-red">Volver a Facturas</a>
    </form>

</div>

<script>
let productosSeleccionados = [];

// Buscar cliente vía AJAX
function buscarCliente(){
    const q = document.getElementById('cliente_buscar').value.trim();
    if(!q) return alert('Ingrese nombre o identificación');

    fetch(`buscar_cliente.php?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            if(data && data.id){
                document.getElementById('cliente_info').innerText = `Cliente: ${data.nombre} - ${data.identificacion}`;
                document.getElementById('cliente_id').value = data.id;
            } else {
                alert('Cliente no encontrado. Debe agregarlo primero.');
                document.getElementById('cliente_info').innerText = '';
                document.getElementById('cliente_id').value = '';
            }
        })
        .catch(err => console.error(err));
}

// Agregar producto a la lista
function agregarProducto(){
    const select = document.getElementById('producto_select');
    const productoId = select.value;
    const nombre = select.options[select.selectedIndex].text;
    const precio = parseFloat(select.options[select.selectedIndex].dataset.precio);

    if(productosSeleccionados.find(p=>p.producto_id==productoId)){
        alert('Producto ya agregado');
        return;
    }

    productosSeleccionados.push({producto_id:productoId, nombre:nombre, precio:precio, cantidad:1});
    actualizarTabla();
}

// Eliminar producto de la lista
function eliminarProducto(index){
    productosSeleccionados.splice(index,1);
    actualizarTabla();
}

// Actualizar tabla y inputs ocultos
function actualizarTabla(){
    const tabla = document.getElementById('tabla_productos');
    tabla.innerHTML = '<tr><th>Producto</th><th>Precio Unitario</th><th>Cantidad</th><th>Acción</th></tr>';

    productosSeleccionados.forEach((p,index)=>{
        const row = tabla.insertRow();
        row.insertCell(0).innerText = p.nombre;
        row.insertCell(1).innerText = p.precio.toFixed(2);

        const qtyCell = row.insertCell(2);
        const input = document.createElement('input');
        input.type='number'; input.min=1; input.value=p.cantidad;
        input.onchange=function(){ p.cantidad=parseInt(this.value); actualizarTotales(); };
        qtyCell.appendChild(input);

        const btnCell = row.insertCell(3);
        const btn = document.createElement('button');
        btn.type='button'; btn.className='btn btn-red'; btn.innerText='Eliminar';
        btn.onclick=()=>eliminarProducto(index);
        btnCell.appendChild(btn);

        // Inputs ocultos
        const hiddenId=document.createElement('input');
        hiddenId.type='hidden'; hiddenId.name='producto_id[]'; hiddenId.value=p.producto_id;
        const hiddenPrecio=document.createElement('input');
        hiddenPrecio.type='hidden'; hiddenPrecio.name='precio[]'; hiddenPrecio.value=p.precio;
        const hiddenCantidad=document.createElement('input');
        hiddenCantidad.type='hidden'; hiddenCantidad.name='cantidad[]'; hiddenCantidad.value=p.cantidad;
        row.appendChild(hiddenId); row.appendChild(hiddenPrecio); row.appendChild(hiddenCantidad);
    });

    actualizarTotales();
}

// Calcular totales
function actualizarTotales(){
    let subtotal = productosSeleccionados.reduce((sum,p)=>sum+p.precio*p.cantidad,0);
    let iva = subtotal*0.15;
    let total = subtotal+iva;
    document.getElementById('subtotal').innerText=subtotal.toFixed(2);
    document.getElementById('iva').innerText=iva.toFixed(2);
    document.getElementById('total').innerText=total.toFixed(2);
}

// Validación formulario
document.getElementById('facturaForm').onsubmit = function(){
    if(!document.getElementById('cliente_id').value){
        alert('Seleccione un cliente válido');
        return false;
    }
    if(productosSeleccionados.length===0){
        alert('Seleccione al menos un producto');
        return false;
    }
};
</script>
</body>
</html>
