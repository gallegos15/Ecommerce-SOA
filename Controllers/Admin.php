<?php
// Helpers para JWT
require_once __DIR__ . '/../Config/Helpers.php';

class Admin extends Controller
{
    
    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        $data['title'] = 'Acceso al sistema';
        $this->views->getView('admin', "login", $data);
    }
    public function validar()
    {
        if (isset($_POST['email']) && isset($_POST['clave'])) {
            if (empty($_POST['email']) || empty($_POST['clave'])) {
                $respuesta = array('msg' => 'Todos los campos son requeridos', 'icono' => 'warning');
            } else {
                $data = $this->model->getUsuario($_POST['email']);
                if (empty($data)) {
                    $respuesta = array('msg' => 'El correo no existe', 'icono' => 'warning');
                } else {
                    if (password_verify($_POST['clave'], $data['clave'])) {
                        // Set session
                        $_SESSION['email'] = $data['correo'];
                        // Generar JWT para el administrador
                        $payload = ['sub' => $data['id'] ?? null, 'email' => $data['correo'], 'role' => 'admin'];
                        $jwt = jwt_encode($payload);
                        // Crear cookie HttpOnly para experiencia de navegación
                        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
                        // Aseguramos que la cookie tenga la misma expiración que el JWT
                        $expiry = time() + (defined('JWT_EXPIRY_SECONDS') ? JWT_EXPIRY_SECONDS : 3600);
                        setcookie('admin_jwt', $jwt, $expiry, '/', '', $secure, true);

                        $respuesta = array('msg' => 'Datos correctos', 'icono' => 'success', 'jwt' => $jwt);
                    } else {
                        $respuesta = array('msg' => 'Contraseña incorrecta', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'Error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    public function salir()
{
        session_start();
        // Eliminar cookie admin_jwt
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie('admin_jwt', '', time() - 3600, '/', '', $secure, true);
        session_destroy();
        header('Location: ' . BASE_URL . 'admin');
        exit;
}

    public function home()
    {
        $data['title'] = 'Panel Administrativo';
        $this->views->getView('admin/administracion', "index", $data);
    }

    /**
     * Endpoint para validar JWT del admin y devolver el payload.
     * Acceso: GET /admin/me con header Authorization: Bearer <token>
     */
    public function me()
    {
        header('Content-Type: application/json; charset=utf-8');
        $payload = jwt_validate_request(); // retorna array o false

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'msg' => 'No autorizado - token inválido o expirado'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Verificar role admin (opcional)
        if (!isset($payload['role']) || $payload['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Acceso denegado - no es administrador'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Éxito: devolver payload
        http_response_code(200);
        echo json_encode(['ok' => true, 'payload' => $payload], JSON_UNESCAPED_UNICODE);
        return;
    }

}
