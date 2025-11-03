<?php
class Errors extends Controller
{
    public function index()
    {
        http_response_code(404);
        echo json_encode([
            'error' => true,
            'message' => 'Recurso no encontrado'
        ]);
        die();
    }
}
?>
