<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Clientes extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        // session_destroy();
    }

    public function index()
    {
        if (empty($_SESSION['correoCliente'])) {
            header('Location: ' . BASE_URL);
            return;
        }
        $data['perfil'] = 'si';
        $data['title'] = 'Tu perfil';
        $data['verificar'] = $this->model->getVerificar($_SESSION['correoCliente']);
        $this->views->getView('principal', "perfil", $data);
    }

    public function registroDirecto()
    {
        if (isset($_POST['nombre']) && isset($_POST['clave'])) {
            if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['clave'])) {
                $mensaje = ['msg' => 'TODOS LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning'];
            } else {
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $clave  = $_POST['clave'];
                $verificar = $this->model->getVerificar($correo);
                if (empty($verificar)) {
                    $token = md5($correo);
                    $hash  = password_hash($clave, PASSWORD_DEFAULT);
                    $data  = $this->model->registroDirecto($nombre, $correo, $hash, $token);
                    if ($data > 0) {
                        $_SESSION['correoCliente'] = $correo;
                        $_SESSION['nombreCliente'] = $nombre;
                        $mensaje = ['msg' => 'Registrado con éxito', 'icono' => 'success', 'token' => $token];
                    } else {
                        $mensaje = ['msg' => 'Error al registrarse', 'icono' => 'error'];
                    }
                } else {
                    $mensaje = ['msg' => 'Ya tienes una cuenta', 'icono' => 'warning'];
                }
            }

            if (empty($_POST['clave'])) {
                $mensaje = ['msg' => 'La clave no puede estar vacía', 'icono' => 'warning'];
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                return;
            }

            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    public function enviarCorreo()
    {
        if (isset($_POST['correo']) && isset($_POST['token'])) {
            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug  = 0;
                $mail->isSMTP();
                $mail->Host       = HOST_SMTP;
                $mail->SMTPAuth   = true;
                $mail->Username   = USER_SMTP;
                $mail->Password   = PASS_SMTP;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = PUERTO_SMTP;

                $mail->setFrom('urbancloud4@gmail.com', TITLE);
                $mail->addAddress($_POST['correo']);

                $mail->isHTML(true);
                $mail->Subject = 'Mensaje desde la: ' . TITLE;
                $mail->Body    = 'Para verificar tu correo en nuestra tienda <a href="' . BASE_URL . 'clientes/verificarCorreo/' . $_POST['token'] . '">CLICK AQUÍ</a>';
                $mail->AltBody = 'GRACIAS POR LA PREFERENCIA';

                $mail->send();
                $mensaje = ['msg' => 'CORREO ENVIADO, REVISA TU BANDEJA DE ENTRADA - SPAM', 'icono' => 'success'];
            } catch (Exception $e) {
                $mensaje = ['msg' => 'ERROR AL ENVIAR CORREO: ' . $mail->ErrorInfo, 'icono' => 'error'];
            }
        } else {
            $mensaje = ['msg' => 'ERROR FATAL: ', 'icono' => 'error'];
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        return;
    }

    public function verificarCorreo($token)
    {
        $verificar = $this->model->getToken($token);
        if (!empty($verificar)) {
            $this->model->actualizarVerify($verificar['id']);
            header('Location: ' . BASE_URL . 'clientes');
        }
    }

    public function loginDirecto()
    {
        if (isset($_POST['correoLogin']) && isset($_POST['claveLogin'])) {
            if (empty($_POST['correoLogin']) || empty($_POST['claveLogin'])) {
                $mensaje = ['msg' => 'TODOS LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning'];
            } else {
                $correo = $_POST['correoLogin'];
                $clave  = $_POST['claveLogin'];
                $verificar = $this->model->getVerificar($correo);
                if (!empty($verificar)) {
                    if (password_verify($clave, $verificar['clave'])) {
                        $_SESSION['correoCliente'] = $verificar['correo'];
                        $_SESSION['nombreCliente'] = $verificar['nombre'];
                        $mensaje = ['msg' => 'Ok', 'icono' => 'success'];
                    } else {
                        $mensaje = ['msg' => 'Contraseña incorrecta', 'icono' => 'error'];
                    }
                } else {
                    $mensaje = ['msg' => 'El correo no existe', 'icono' => 'warning'];
                }
            }

            if (empty($_POST['claveLogin'])) {
                $mensaje = ['msg' => 'La clave no puede estar vacía', 'icono' => 'warning'];
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                return;
            }

            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    // ===== Registrar pedido (PayPal) =====
    public function registrarPedido()
{
    header('Content-Type: application/json; charset=utf-8');

    try {
        // Body
        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, true);
        if (!is_array($body)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msg' => 'JSON inválido', 'raw' => $raw], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Puede venir: { pedido: {...}, productos: [...] } o el objeto PayPal directo
        $pedidos   = $body['pedido']    ?? $body;
        $productos = $body['productos'] ?? [];
        if (!is_array($productos)) $productos = [];

        // ---- DATOS PRINCIPALES (con fallbacks a la CAPTURA) ----
        $id_transaccion = $pedidos['id']
            ?? ($pedidos['purchase_units'][0]['payments']['captures'][0]['id'] ?? '');

        $monto = $pedidos['purchase_units'][0]['payments']['captures'][0]['amount']['value']
            ?? $pedidos['purchase_units'][0]['amount']['value']
            ?? '0';

        $estado = $pedidos['status']
            ?? ($pedidos['purchase_units'][0]['payments']['captures'][0]['status'] ?? '');

        $fecha  = date('Y-m-d H:i:s');

        // Payer
        $email    = $pedidos['payer']['email_address'] ?? '';
        $nombre   = trim(($pedidos['payer']['name']['given_name'] ?? '') . ' ' . ($pedidos['payer']['name']['surname'] ?? ''));
        $apellido = $pedidos['payer']['name']['surname'] ?? '';

        // Dirección (puede no venir shipping si NO_SHIPPING)
        $direccion = $pedidos['purchase_units'][0]['shipping']['address']['address_line_1']
                  ?? $pedidos['payer']['address']['address_line_1']
                  ?? 'N/A';

        $ciudad = $pedidos['purchase_units'][0]['shipping']['address']['admin_area_2']
               ?? $pedidos['payer']['address']['admin_area_2']
               ?? ($pedidos['purchase_units'][0]['shipping']['address']['admin_area_1'] ?? 'N/A');

        $email_user = $_SESSION['correoCliente'] ?? ($pedidos['payer']['email_address'] ?? '');

        // Validación mínima
        if ($id_transaccion === '' || $estado === '') {
            http_response_code(422);
            echo json_encode([
                'ok'  => false,
                'msg' => 'Faltan campos requeridos (id/status)',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Insertar pedido
        $idPedido = $this->model->registrarPedido(
            (string)$id_transaccion,
            (string)$monto,
            (string)$estado,
            $fecha,
            (string)$email,
            ($nombre   !== '' ? $nombre   : 'N/A'),
            ($apellido !== '' ? $apellido : 'N/A'),
            (string)$direccion,
            (string)$ciudad,
            (string)$email_user
        );

        // Insertar detalles usando los datos enviados (sin getProductos())
        if ($idPedido > 0) {
            foreach ($productos as $p) {
                $nom   = isset($p['nombre'])    ? (string)$p['nombre']  : 'N/A';
                $prec  = isset($p['precio'])    ? (string)$p['precio']  : '0';
                $cant  = isset($p['cantidad'])  ? (int)$p['cantidad']   : 0;
                if ($cant <= 0) continue;

                $this->model->registrarDetalle($nom, $prec, $cant, (int)$idPedido);
            }

            $mensaje = ['ok' => true, 'msg' => 'Pedidos registrados', 'icono' => 'success', 'id' => $idPedido];
        } else {
            $mensaje = ['ok' => false, 'msg' => 'Error al registrar pedido', 'icono' => 'error'];
        }

        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        return;

    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'ok'    => false,
            'msg'   => 'Excepción en registrarPedido',
            'error' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        return;
    }
}
//listar productos pendientes
    public function listarPendientes()
    {
        $data = $this->model->getPedidos(1);
        for ($i=0; $i < count($data); $i++) { 
            $data[$i]['accion'] = '<div class="text-center"><button class="btn btn-primary" type="button" onclick="verPedido('.$data[$i]['id'].')"><i class="fas fa-eye"></i></button></div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function verPedido($idPedido)
    {
        $data ['productos'] = $this->model->verPedido($idPedido);
        $data ['moneda'] = MONEDA;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function salir()
    {
        session_destroy();
        header('Location: ' . BASE_URL);
    }
}
