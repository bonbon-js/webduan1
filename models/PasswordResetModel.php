<?php

class PasswordResetModel extends BaseModel
{
    protected $table = 'password_resets';

    // Tạo token reset password
    public function createResetToken($userId, $token, $otpCode = null)
    {
        // Xóa các token cũ của user này
        $this->clearOldTokens($userId);
        
        $sql = "INSERT INTO {$this->table} (user_id, token, otp_code, expires_at, is_used, created_at) 
                VALUES (:user_id, :token, :otp_code, DATE_ADD(NOW(), INTERVAL 1 HOUR), 0, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':token' => $token,
            ':otp_code' => $otpCode,
        ]);
    }

    // Kiểm tra token reset password
    public function verifyResetToken($token)
    {
        $sql = "SELECT pr.*, u.email, u.full_name 
                FROM {$this->table} pr 
                INNER JOIN users u ON pr.user_id = u.user_id 
                WHERE pr.token = :token AND pr.expires_at > NOW() AND pr.is_used = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    // Đánh dấu token đã sử dụng
    public function markTokenAsUsed($token)
    {
        $sql = "UPDATE {$this->table} SET is_used = 1 WHERE token = :token";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':token' => $token]);
    }

    // Xóa các token cũ
    private function clearOldTokens($userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND (expires_at < NOW() OR is_used = 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
    }

    // Kiểm tra OTP code (nếu có)
    public function verifyOtpCode($userId, $otpCode)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND otp_code = :otp_code AND expires_at > NOW() AND is_used = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':otp_code' => $otpCode
        ]);
        return $stmt->fetch();
    }
}

