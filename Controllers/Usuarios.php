<?php
// Helpers JWT (si están disponibles)
require_once __DIR__ . '/../Config/Helpers.php';

class Usuarios extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();

        // Permitir acceso si hay sesión de admin
        if (!empty($_SESSION['email'])) {
            return;
        }

        // Intentar validar JWT en header Authorization: Bearer <token>
        $payload = jwt_validate_request();
        if ($payload && isset($payload['role']) && $payload['role'] === 'admin') {
            return; // autorizado
        }

        // No autorizado: devolver JSON con instrucción (útil para AJAX)
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        $headers = null;
        if (function_exists('getallheaders')) $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        echo json_encode([
            'ok' => false,
            'msg' => 'No autorizado. Incluye header Authorization: Bearer <token>',
            'received_authorization' => $authHeader
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    public function index()
    {
        $data['title'] = 'usuarios';
        $this->views->getView('admin/usuarios', "index", $data);
    }

    public function listar()
    {
        $data = $this->model->getUsuarios(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
    <button class="btn btn-primary" type="button" onclick="editUser(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
    <button class="btn btn-danger" type="button" onclick="eliminarUser(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>
</div>';
        }
        echo json_encode($data);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $nombre = $_POST['nombre'];
            $apellidos = $_POST['apellidos'];
            $correo = $_POST['correo'];
            $clave = $_POST['clave'];
            $id = $_POST['id'];
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            if (empty($_POST['nombre']) || empty($_POST['apellidos'])) {
                $respuesta = array('msg' => 'Todos los campos son requeridos', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $result = $this->model->verificarCorreo($correo);
                    if (empty($result)) {
                        $data = $this->model->registrar($nombre, $apellidos, $correo, $hash);
                        if ($data > 0) {
                            // Generar JWT para el usuario administrador creado (role: admin)
                            if (function_exists('jwt_encode')) {
                                $payload = ['sub' => $data, 'email' => $correo, 'role' => 'admin'];
                                $jwt = jwt_encode($payload);
                                $respuesta = array('msg' => 'usuario registrado', 'icono' => 'success', 'jwt' => $jwt);
                            } else {
                                $respuesta = array('msg' => 'usuario registrado', 'icono' => 'success');
                            }
                        } else {
                            $respuesta = array('msg' => 'error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'correo ya existe', 'icono' => 'warning');
                    }
                } else {
                    $data = $this->model->modificar($nombre, $apellidos, $correo, $id);
                    if ($data == 1) {
                        $respuesta = array('msg' => 'usuario modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }
    //eliminar user
    public function delete($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->eliminar($idUser);
            if ($data == 1) {
                $respuesta = array('msg' => 'usuario dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar user
    public function edit($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->getUsuario($idUser);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

}