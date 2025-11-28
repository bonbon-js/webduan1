<?php

/**
 * VNPay Payment Gateway Integration
 * 
 * Cần cấu hình trong configs/env.php:
 * - VNPAY_TMN_CODE: Mã website của bạn trên VNPay
 * - VNPAY_HASH_SECRET: Mã bảo mật từ VNPay
 * - VNPAY_URL: URL thanh toán VNPay (sandbox hoặc production)
 */

class VnPay
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;

    public function __construct()
    {
        $this->tmnCode = defined('VNPAY_TMN_CODE') ? VNPAY_TMN_CODE : '';
        $this->hashSecret = defined('VNPAY_HASH_SECRET') ? VNPAY_HASH_SECRET : '';
        $this->url = defined('VNPAY_URL') ? VNPAY_URL : 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(array $params): string
    {
        $vnp_TmnCode = $this->tmnCode;
        $vnp_HashSecret = $this->hashSecret;
        $vnp_Url = $this->url;
        $vnp_ReturnUrl = $params['return_url'] ?? BASE_URL . '?action=vnpay-return';

        $vnp_TxnRef = $params['txn_ref'] ?? time(); // Mã đơn hàng
        $vnp_OrderInfo = $params['order_info'] ?? 'Thanh toan don hang';
        $vnp_OrderType = $params['order_type'] ?? 'other';
        $vnp_Amount = $params['amount'] * 100; // VNPay yêu cầu số tiền nhân 100
        $vnp_Locale = $params['locale'] ?? 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $vnp_CreateDate = date('YmdHis');

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnp_SecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Xác thực callback từ VNPay
     */
    public function validateCallback(array $data): array
    {
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash']);

        ksort($data);
        $i = 0;
        $hashdata = "";
        foreach ($data as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);

        if ($secureHash === $vnp_SecureHash) {
            return [
                'success' => true,
                'txn_ref' => $data['vnp_TxnRef'] ?? '',
                'response_code' => $data['vnp_ResponseCode'] ?? '',
                'transaction_no' => $data['vnp_TransactionNo'] ?? '',
                'amount' => ($data['vnp_Amount'] ?? 0) / 100,
                'order_info' => $data['vnp_OrderInfo'] ?? '',
            ];
        }

        return ['success' => false, 'message' => 'Invalid signature'];
    }
}

