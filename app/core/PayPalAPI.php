<?php
/**
 * PayPal API 处理类
 * 支持PayPal Checkout API v2
 */
class PayPalAPI {
    private $clientId;
    private $clientSecret;
    private $apiBaseUrl;
    private $accessToken = null;
    private $tokenExpires = 0;

    public function __construct() {
        $this->clientId = PAYPAL_CLIENT_ID;
        $this->clientSecret = PAYPAL_CLIENT_SECRET;
        $this->apiBaseUrl = PAYPAL_API_BASE_URL;
    }

    /**
     * 获取访问令牌
     */
    private function getAccessToken() {
        if ($this->accessToken && time() < $this->tokenExpires) {
            return $this->accessToken;
        }

        $ch = curl_init($this->apiBaseUrl . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ':' . $this->clientSecret);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Failed to get access token. HTTP Code: $httpCode");
        }

        $data = json_decode($response, true);
        if (!isset($data['access_token'])) {
            throw new Exception("Access token not found in response: " . $response);
        }

        $this->accessToken = $data['access_token'];
        $this->tokenExpires = time() + ($data['expires_in'] ?? 3600) - 60; // 提前1分钟过期

        return $this->accessToken;
    }

    /**
     * 创建订单
     */
    public function createOrder($amount, $currency = 'USD', $description = 'Service Purchase') {
        $payload = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'brand_name' => SITENAME,
                'locale' => 'en-US',
                'landing_page' => 'BILLING',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
                'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                'return_url' => '',
                'cancel_url' => '',
                'store_invoicing_preference' => 'NO_STORE', // 不保存支付信息
                'user_experience_flow' => 'DIRECT', // 直接支付流程
                'shipping_preference' => 'NO_SHIPPING',
                'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                'no_shipping' => true,
                'allow_note' => false,
                'experience_context' => [
                    'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                    'brand_name' => SITENAME,
                    'locale' => 'en-US',
                    'landing_page' => 'BILLING',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'allow_note' => false,
                    'no_shipping' => true,
                    'payment_method_selected' => 'card',
                    'store_invoicing_preference' => 'NO_STORE',
                    'user_experience_flow' => 'DIRECT'
                ]
            ],
            'purchase_units' => [
                [
                    'description' => $description,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', '')
                    ],
                    'custom_id' => '',
                    'soft_descriptor' => 'Digital Service Purchase'
                ]
            ]
        ];

        $response = $this->makeRequest('/v2/checkout/orders', 'POST', $payload);

        if (!isset($response['id'])) {
            throw new Exception("Failed to create order: " . json_encode($response));
        }

        return $response;
    }

    /**
     * 捕获支付
     */
    public function capturePayment($orderId) {
        $response = $this->makeRequest("/v2/checkout/orders/$orderId/capture", 'POST');

        if (!isset($response['status']) || $response['status'] !== 'COMPLETED') {
            throw new Exception("Failed to capture payment: " . json_encode($response));
        }

        return $response;
    }

    /**
     * 获取订单详情
     */
    public function getOrderDetails($orderId) {
        $response = $this->makeRequest("/v2/checkout/orders/$orderId", 'GET');
        return $response;
    }

    /**
     * 发送API请求
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->apiBaseUrl . $endpoint;

        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Accept-Language: en_US'
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception("CURL Error: $curlError");
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response: " . $response);
        }

        // 处理API错误
        if ($httpCode >= 400) {
            $message = $decoded['message'] ?? 'Unknown API error';
            $details = isset($decoded['details']) ? ' - ' . json_encode($decoded['details']) : '';
            throw new Exception("API Error ($httpCode): $message$details");
        }

        return $decoded;
    }

    /**
     * 记录日志
     */
    private function log($message) {
        $logFile = APPROOT . '/doc/paypal_api.log';
        $logMessage = "[" . date("Y-m-d H:i:s") . "] " . $message . "\n";
        error_log($logMessage, 3, $logFile);
    }
}