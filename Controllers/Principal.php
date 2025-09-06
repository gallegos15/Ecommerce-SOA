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
        // Navegacion
        $data['pagina'] = $pagina;
        $total =$this->model->getTotalProductos();
        $data['total'] = ceil($total['total'] / $porPagina);
        $this->views->getView('principal', "shop", $data);
    }
    //vista detail=shop-single
    public function shop_single($id_producto)
    {
        $data['producto'] = $this->model->getProducto($id_producto);
        $data['title'] = $data['producto']['nombre'];
        $this->views->getView('principal', "shop_single", $data);
    }
    //vista contact
    public function contact()
    {
        $data['title'] = 'Contacto';
        $this->views->getView('principal', "contact", $data);
    }
}