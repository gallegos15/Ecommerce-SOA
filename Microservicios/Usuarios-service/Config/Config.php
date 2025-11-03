<?php
// URL base del microservicio (ajusta según tu entorno o puerto)
const BASE_URL = "http://localhost:8001/";  

// Conexión a la base de datos (solo la de usuarios)
const HOST = "localhost";
const USER = "root";
const PASS = "";
const DB = "db_usuarios";  // 🔹 base específica del microservicio
const CHARSET = "charset=utf8";

// Configuración general (si deseas mantener algunos valores globales)
const TITLE = "Usuarios Service";
const MONEDA = "USD";

// Si este microservicio no usa PayPal ni SMTP, puedes comentarlos o eliminarlos.
// const CLIENT_ID = "...";
// const PAYPAL_API_BASE = "...";
// const USER_SMTP = "...";
// const PASS_SMTP = "...";
// const PUERTO_SMTP = 465;
// const HOST_SMTP = "smtp.gmail.com";
?>

