<?php

class UserModel extends BaseModel
{
    protected $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findByToken(string $token): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE session_token = :token LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} 
                (first_name, last_name, gender, birthday, phone, address, email, password, role, full_name, session_token, session_expires, created_at) 
                VALUES (:first_name, :last_name, :gender, :birthday, :phone, :address, :email, :password, :role, :full_name, :session_token, :session_expires, :created_at)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'gender'          => $data['gender'],
            'birthday'        => $data['birthday'],
            'phone'           => $data['phone'],
            'address'         => $data['address'],
            'email'           => $data['email'],
            'password'        => $data['password'],
            'role'            => $data['role'] ?? 'customer',
            'full_name'       => trim($data['first_name'] . ' ' . $data['last_name']),
            'session_token'   => $data['session_token'] ?? null,
            'session_expires' => $data['session_expires'] ?? null,
            'created_at'      => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateVerificationStatus(int $userId): void
    {
        $sql = "UPDATE {$this->table} SET session_token = NULL, session_expires = NULL WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    public function updatePassword(int $userId, string $hash): void
    {
        $sql = "UPDATE {$this->table} SET password = :password WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'password' => $hash,
            'id'       => $userId,
        ]);
    }
}

