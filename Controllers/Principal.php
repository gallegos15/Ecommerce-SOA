<?php
class Principal extends Controller
{
    public function __construct() {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        
    }
    //vista about
    public function about()
    {
        $data['title'] = 'Nosotros';
        $this->views->getView('principal', "about", $data);
    }
    //vista shop
    public function shop($page = 1)
    {
        $pagina = (empty($page)) ? 1 : $page;
        $porPagina = 10;
        $desde = ($pagina - 1) * $porPagina;
        $data['title'] = 'Nosotros Productos';
        $data['productos'] = $this->model->getProductos($desde, $porPagina);
        $data['pagina'] = $pagina;
        $total = $this->model->getTotalProductos();
        $data['total'] = ceil($total['total'] / $porPagina);
        $this->views->getView('principal', "shop", $data);
    }
    //vista detail=shop-single
    public function shop_single($id_producto)
    {
        $data['producto'] = $this->model->getProducto($id_producto);
        $id_categoria = $data['producto']['id_categoria'];
        $data['relacionados'] = $this->model->getAleatorios($id_categoria, $data['producto']['id'] );
        $data['title'] = $data['producto']['nombre'];
        $this->views->getView('principal', "shop_single", $data);
    }
    //vista categorias
    public function categorias($datos)
    {
        $id_categoria = 1;
        $page = 1;
        $array = explode(',', $datos);
        if (isset($array[0])) {
            if (!empty($array[0])) {
                $id_categoria = $array[0];
            } 
        if (isset($array[1])) {
            if (!empty($array[1])) {
                $page = $array[1];
            } 
        }
        $pagina = (empty($page)) ? 1 : $page;
        $porPagina = 16;
        $desde = ($pagina - 1) * $porPagina;

        $data['pagina'] = $pagina;
        $total = $this->model->getTotalProductosCat($id_categoria);
        $data['total'] = ceil($total['total'] / $porPagina);

        $data['productos'] = $this->model->getProductosCat($id_categoria, $desde, $porPagina);
        $data['title'] = 'Categorias';
        $data['id_categoria'] = $id_categoria;
        $this->views->getView('principal', "categorias", $data);
    }}
    //vista contact
    public function contact()
    {
        $data['title'] = 'Contacto';
        $this->views->getView('principal', "contact", $data);
    }

    //vista lista deseos
    public function deseo()
    {
        $data['title'] = 'Tu lista de deseo';
        $this->views->getView('principal', "deseo", $data);
    }
    //obtener productos a partir de la lista de deseos
    public function getListaDeseo()
    {
        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $array = array();
        foreach ($json as $producto) {
            $result = $this->model->getListaDeseo($producto['idProducto']);
            $data ['id'] = $result['id'];
            $data ['nombre'] = $result['nombre'];
            $data ['descripcion'] = $result['descripcion'];
            $data ['precio'] = $result['precio'];
            $data ['cantidad'] = $producto['cantidad'];
            $data ['imagen'] = $result['imagen'];
            array_push($array, $data);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    die();
    }
}