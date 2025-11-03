
<?php
$conn = new mysqli('localhost', 'root', '', 'db_usuarios');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa";
}
?>