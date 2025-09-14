<?php
class Pages extends Controller {
    public function __construct(){
        
    }
    
    public function index(){
        $data = [
            'title' => 'Home',
            'description' => 'A decentralized platform for SEO services. Join our webGroup to elevate your online presence.',
            'keywords' => 'SEO, backlinks, link building, search engine optimization, webGoup'
        ];
        $this->view('pages/index', $data);
    }

    public function about(){
        $data = [
            'title' => 'About Us',
            'description' => 'Learn about webGoup and our mission to provide a decentralized platform for SEO services.',
            'keywords' => 'about webGoup, SEO platform, decentralized SEO'
        ];
        $this->view('pages/about', $data);
    }
}
