<?php

class PasswordResetModel extends BaseModel
{
    protected $table = 'password_resets';

    public function createToken(int $userId, string $token, string $expiresAt): void
    {
        $sql = "INSERT INTO {$this->table} (user_id, token, expires_at, is_used, created_at) 
                VALUES (:user_id, :token, :expires_at, 0, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValidToken(string $token): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE token = :token AND is_used = 0 AND expires_at >= NOW() 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        return $reset ?: null;
    }

    public function markUsed(int $resetId): void
    {
        $sql = "UPDATE {$this->table} SET is_used = 1 WHERE reset_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $resetId]);
    }
}

