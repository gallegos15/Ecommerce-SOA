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
    public function shop()
    {
        $data['title'] = 'Nosotros Productos';
        $this->views->getView('principal', "shop", $data);
    }
    //vista shop-single
    public function shop_single($id_producto)
    {
        $data['title'] = '---------------';
        $this->views->getView('principal', "shop_single", $data);
    }
    //vista contact
    public function contact()
    {
        $data['title'] = 'Contacto';
        $this->views->getView('principal', "contact", $data);
    }
}