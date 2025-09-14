<?php
class Official extends Controller {
    protected $serviceModel;

    public function __construct(){
        $this->serviceModel = $this->model('Service');
    }

    public function index(){
        $officialServices = $this->serviceModel->getOfficialServices();
        $data = [
            'title' => 'Our Services',
            'description' => 'Explore the official SEO services offered by webGoup.',
            'keywords' => 'official services, webgoup services, professional SEO',
            'officialServices' => $officialServices
        ];
        $this->view('official/index', $data);
    }
}
