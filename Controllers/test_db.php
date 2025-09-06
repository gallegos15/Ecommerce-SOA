
<?php
$conn = new mysqli('localhost', 'root', '', 'tienda_virtual');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa";
}
?>