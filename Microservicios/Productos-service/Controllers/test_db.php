
<?php
$conn = new mysqli('localhost', 'root', '', 'bd_productos');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa";
}
?>