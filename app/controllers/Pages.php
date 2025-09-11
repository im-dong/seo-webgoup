<?php
class Pages extends Controller {
    public function __construct(){
        
    }
    
    public function index(){
        $data = [
            'title' => SITENAME,
            'description' => 'A platform for SEO services and link exchange. Register and start trading or purchase our official services.'
        ];
        $this->view('pages/index', $data);
    }

    public function about(){
        $data = [
            'title' => 'About Us',
            'description' => 'This is a platform built to facilitate SEO link trading.'
        ];
        $this->view('pages/about', $data);
    }
}
