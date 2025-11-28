<?php

class ContactController
{
    public function index()
    {
        $view = 'contact';
        $title = 'Liên Hệ - BonBonWear';
        require_once PATH_VIEW . 'main.php';
    }

    public function submit()
    {
        header('Content-Type: application/json');
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        // Validation
        if (empty($name) || empty($email) || empty($message)) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc (Họ tên, Email, Nội dung).'
            ]);
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email không hợp lệ.'
            ]);
            exit;
        }
        
        // Ở đây bạn có thể lưu vào database hoặc gửi email
        // Tạm thời chỉ trả về success
        // TODO: Lưu vào bảng contacts hoặc gửi email
        
        echo json_encode([
            'success' => true,
            'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.'
        ]);
        exit;
    }
}

