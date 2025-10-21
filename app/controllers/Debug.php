<?php
class Debug extends Controller {
    public function __construct(){
        // This controller doesn't need models
    }

    public function paypal(){
        $data = [
            'title' => 'PayPal Debug',
            'description' => 'PayPal API debug and testing page',
            'config' => [
                'urlroot' => URLROOT,
                'client_id' => defined('PAYPAL_CLIENT_ID') ? substr(PAYPAL_CLIENT_ID, 0, 30) . '...' : 'NOT_DEFINED',
                'client_id_full' => defined('PAYPAL_CLIENT_ID') ? PAYPAL_CLIENT_ID : 'NOT_DEFINED',
                'sandbox' => defined('PAYPAL_SANDBOX') ? PAYPAL_SANDBOX : true,
                'api_base_url' => defined('PAYPAL_API_BASE_URL') ? PAYPAL_API_BASE_URL : 'NOT_DEFINED'
            ]
        ];
        $this->view('debug/paypal', $data);
    }
}