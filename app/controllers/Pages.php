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

    public function terms(){
        $data = [
            'title' => 'Terms of Service',
            'description' => 'Read webGoup\'s terms of service, fee structure, and user guidelines.',
            'keywords' => 'terms of service, webGoup terms, SEO platform rules'
        ];
        $this->view('pages/terms', $data);
    }

    public function seoGuidelines(){
        $data = [
            'title' => 'SEO Guidelines & Compliance',
            'description' => 'Essential guidelines for safe and compliant SEO practices on webGoup platform.',
            'keywords' => 'SEO guidelines, Google policies, search engine optimization, compliance, webmaster guidelines'
        ];
        $this->view('pages/seo-guidelines', $data);
    }

    public function privacy(){
        $data = [
            'title' => 'Privacy Policy',
            'description' => 'Learn how webGoup collects, uses, and protects your personal information.',
            'keywords' => 'privacy policy, data protection, personal information, webGoup privacy'
        ];
        $this->view('pages/privacy', $data);
    }
}
