<?php
require_once __DIR__."/../models/Cliente.php";
require_once __DIR__."/../config/db.php";

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
if(!$q) { echo json_encode([]); exit; }

$modelCliente = new Cliente($pdo);
$cliente = $modelCliente->buscarCliente($q);

echo $cliente ? json_encode($cliente) : json_encode([]);
