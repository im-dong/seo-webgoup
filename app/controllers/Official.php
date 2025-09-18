<?php
class Official extends Controller {
    protected $serviceModel;

    public function __construct(){
        $this->serviceModel = $this->model('Service');
    }

    public function index(){
        $officialServices = $this->serviceModel->getOfficialServices();
        $data = [
            'title' => 'Professional Services',
            'description' => 'Explore premium SEO services from vetted providers with webGoup quality guarantees.',
            'keywords' => 'professional services, premium SEO, vetted providers, quality guaranteed SEO',
            'officialServices' => $officialServices
        ];
        $this->view('official/index', $data);
    }
}
