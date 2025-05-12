<?php
// procesar_registro_emision.php

echo "<h1>Procesando Registro de Emisión...</h1>";

// --- 1. Configuración de la Conexión a la Base de Datos ---
$servidor = "localhost";
$usuario_bd = "root";       // TU USUARIO de phpMyAdmin/MySQL
$password_bd = "";          // TU CONTRASEÑA de phpMyAdmin/MySQL (si no tienes, déjala vacía '')
$nombre_bd = "carbontrack"; // El nombre de tu base de datos

// --- 2. Conectar a MySQL ---
$conexion = new mysqli($servidor, $usuario_bd, $password_bd, $nombre_bd);

if ($conexion->connect_error) {
    die("¡Error de conexión! No se pudo conectar a la base de datos: " . $conexion->connect_error);
} else {
    echo "<p>¡Conexión a la base de datos establecida con éxito!</p>";
}

// --- 3. Recoger los Datos del Formulario ---
// Todos los campos son ahora recogidos y se espera que no estén vacíos si son NOT NULL en la BD
$id_emision_form = isset($_POST['id_emision']) ? $conexion->real_escape_string(trim($_POST['id_emision'])) : '';
$fecha_emision_form = isset($_POST['fecha_emision']) ? $conexion->real_escape_string(trim($_POST['fecha_emision'])) : '';
$cantidad_form = isset($_POST['cantidad_consumida']) ? (float)$_POST['cantidad_consumida'] : 0.0;
$unidad_form = isset($_POST['unidad_consumo']) ? $conexion->real_escape_string(trim($_POST['unidad_consumo'])) : '';
$id_empresa_form = isset($_POST['id_empresa']) ? $conexion->real_escape_string(trim($_POST['id_empresa'])) : '';
$id_fuente_emision_form = isset($_POST['id_fuente_emision']) ? $conexion->real_escape_string(trim($_POST['id_fuente_emision'])) : '';
$id_usuario_registra_form = isset($_POST['id_usuario_registra']) ? $conexion->real_escape_string(trim($_POST['id_usuario_registra'])) : '';

echo "<p>Datos recibidos del formulario:</p>";
echo "<ul>";
echo "<li>ID Emisión: " . htmlspecialchars($id_emision_form) . "</li>";
echo "<li>Fecha Emisión: " . htmlspecialchars($fecha_emision_form) . "</li>";
echo "<li>Cantidad: " . htmlspecialchars($cantidad_form) . "</li>";
echo "<li>Unidad: " . htmlspecialchars($unidad_form) . "</li>";
echo "<li>ID Empresa: " . htmlspecialchars($id_empresa_form) . "</li>";
echo "<li>ID Fuente Emisión: " . htmlspecialchars($id_fuente_emision_form) . "</li>";
echo "<li>ID Usuario Registra: " . htmlspecialchars($id_usuario_registra_form) . "</li>";
echo "</ul>";

// --- 4. Validar Datos ---
// Ahora todos los campos que esperamos son obligatorios
if (empty($id_emision_form) || empty($fecha_emision_form) || $cantidad_form <= 0 || empty($unidad_form) || empty($id_empresa_form) || empty($id_fuente_emision_form) || empty($id_usuario_registra_form)) {
    die("Error: Todos los campos del formulario son requeridos y deben ser válidos (ID Emisión, Fecha, Cantidad, Unidad, ID Empresa, ID Fuente, ID Usuario).");
}

// --- 5. Preparar la Instrucción SQL para Guardar (INSERT) ---
// Asegúrate que tu tabla se llame 'emision' y tenga estas 7 columnas.
$sql = "INSERT INTO emision 
            (id_emision, fecha, cantidad, unidad, id_empresa, id_fuente_emision, id_usuario_registra) 
        VALUES (?, ?, ?, ?, ?, ?, ?)"; // 7 signos de interrogación

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta SQL: " . $conexion->error . "<br>SQL: " . $sql);
}

// --- 6. Vincular los Datos a la Instrucción SQL ---
// Tipos: s(id_emision), s(fecha), d(cantidad), s(unidad), s(id_empresa), s(id_fuente_emision), s(id_usuario_registra)
// Todos los IDs son VARCHAR (s), fecha es DATE (s), cantidad es DECIMAL (d), unidad es VARCHAR (s)
$stmt->bind_param("ssdsdss",
    $id_emision_form,
    $fecha_emision_form,
    $cantidad_form,
    $unidad_form,
    $id_empresa_form,
    $id_fuente_emision_form,
    $id_usuario_registra_form
);

// --- 7. Ejecutar la Instrucción ---
echo "<p>Intentando ejecutar la consulta INSERT...</p>";
if ($stmt->execute()) {
    echo "<h2>¡ÉXITO! Nueva emisión registrada correctamente.</h2>";
    echo "<p><a href='formulario_emision.html'>Registrar otra emisión</a></p>";
} else {
    // Muestra el error específico de MySQL si falla la ejecución
    echo "<h2>¡ERROR! No se pudo registrar la emisión.</h2>";
    echo "<p><strong>Error de MySQL:</strong> " . $stmt->error . "</p>";
    echo "<p>Por favor, verifica que los IDs de Empresa, Fuente y Usuario existan en sus respectivas tablas y que el ID de Emisión sea único.</p>";
}

// --- 8. Cerrar todo ---
$stmt->close();
$conexion->close();
echo "<p>Conexión cerrada.</p>";

?>