<?php
class Official extends Controller {
    protected $serviceModel;

    public function __construct(){
        $this->serviceModel = $this->model('Service');
    }

    public function index(){
        $officialServices = $this->serviceModel->getOfficialServices();
        $data = [
            'officialServices' => $officialServices
        ];
        $this->view('official/index', $data);
    }
}
