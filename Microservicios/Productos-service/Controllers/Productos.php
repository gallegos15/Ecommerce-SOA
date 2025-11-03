<?php
class Productos extends Controller
{
    public function __construct()
    {
        parent::__construct();
        header("Content-Type: application/json; charset=utf-8");
    }

    // 🔹 GET /productos  → devuelve lista de productos activos
    public function index()
    {
        $productos = $this->model->getProductos(1);
        echo json_encode($productos, JSON_UNESCAPED_UNICODE);
        die();
    }

    // 🔹 GET /productos/show/{id}  → devuelve producto por ID
    public function show($idPro)
    {
        if (is_numeric($idPro)) {
            $producto = $this->model->getProducto($idPro);
            echo json_encode($producto, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'ID inválido']);
        }
        die();
    }

    // 🔹 POST /productos/store → registra nuevo producto (JSON o formulario)
    public function store()
    {
        // Permitir tanto JSON como form-data
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input && isset($_POST['nombre'])) {
            $input = $_POST;
        }

        $nombre = $input['nombre'] ?? null;
        $descripcion = $input['descripcion'] ?? '';
        $precio = $input['precio'] ?? 0;
        $cantidad = $input['cantidad'] ?? 0;
        $categoria = $input['categoria'] ?? 1;
        $imagen = $input['imagen'] ?? 'assets/img/productos/default.png';

        if (empty($nombre) || empty($precio) || empty($cantidad)) {
            echo json_encode(['msg' => 'Todos los campos son requeridos', 'icono' => 'warning']);
            die();
        }

        $data = $this->model->registrar($nombre, $descripcion, $precio, $cantidad, $imagen, $categoria);
        if ($data > 0) {
            echo json_encode(['msg' => 'Producto registrado correctamente', 'icono' => 'success']);
        } else {
            echo json_encode(['msg' => 'Error al registrar el producto', 'icono' => 'error']);
        }
        die();
    }

    // 🔹 PUT /productos/update/{id}
    public function update($idPro)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_numeric($idPro)) {
            echo json_encode(['msg' => 'ID inválido']);
            die();
        }

        $nombre = $input['nombre'] ?? null;
        $descripcion = $input['descripcion'] ?? '';
        $precio = $input['precio'] ?? 0;
        $cantidad = $input['cantidad'] ?? 0;
        $categoria = $input['categoria'] ?? 1;
        $imagen = $input['imagen'] ?? 'assets/img/productos/default.png';

        $data = $this->model->modificar($nombre, $descripcion, $precio, $cantidad, $imagen, $categoria, $idPro);
        if ($data == 1) {
            echo json_encode(['msg' => 'Producto modificado correctamente', 'icono' => 'success']);
        } else {
            echo json_encode(['msg' => 'Error al modificar el producto', 'icono' => 'error']);
        }
        die();
    }

    // 🔹 DELETE /productos/delete/{id}
    public function delete($idPro)
    {
        if (is_numeric($idPro)) {
            $data = $this->model->eliminar($idPro);
            if ($data == 1) {
                $respuesta = array('msg' => 'Producto dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'Error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ID inválido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
}
