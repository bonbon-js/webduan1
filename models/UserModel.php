<?php

class UserModel extends BaseModel
{
    protected $table = 'users';

    public function findByEmail(string $email): ?array
    {
        // Tìm kiếm case-insensitive
        $sql = "SELECT * FROM {$this->table} WHERE LOWER(email) = LOWER(:email) LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} 
                (first_name, last_name, gender, birthday, phone, address, email, password, role, created_at, full_name) 
                VALUES (:first_name, :last_name, :gender, :birthday, :phone, :address, :email, :password, :role, NOW(), :full_name)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'gender'     => $data['gender'],
            'birthday'   => $data['birthday'],
            'phone'      => $data['phone'] ?? null,
            'address'    => $data['address'] ?? null,
            'email'      => $data['email'],
            'password'   => $data['password'],
            'role'       => $data['role'] ?? 'customer',
            'full_name'  => trim($data['first_name'] . ' ' . $data['last_name']),
        ]);

        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new PDOException('Database error: ' . ($errorInfo[2] ?? 'Unknown error'));
        }

        $userId = (int)$this->pdo->lastInsertId();
        if ($userId <= 0) {
            throw new Exception('Failed to get user ID after insert');
        }

        return $userId;
    }

    public function getAll(?string $keyword = null, ?string $role = null, ?string $status = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($keyword) {
            $sql .= " AND (full_name LIKE :keyword OR email LIKE :keyword OR phone LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }

        if ($role) {
            $sql .= " AND role = :role";
            $params['role'] = $role;
        }

        if ($status === 'pending') {
            $sql .= " AND session_token IS NOT NULL";
        } elseif ($status === 'verified') {
            $sql .= " AND session_token IS NULL";
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRole(int $userId, string $role): void
    {
        $sql = "UPDATE {$this->table} SET role = :role WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'role' => $role,
            'id'   => $userId,
        ]);
    }

    public function setVerificationToken(int $userId, string $token, string $expiresAt): void
    {
        // Đảm bảo token được trim và không có khoảng trắng
        $token = trim($token);
        
        $sql = "UPDATE {$this->table} SET session_token = :token, session_expires = :expires WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'token'   => $token,
            'expires' => $expiresAt,
            'id'      => $userId,
        ]);
        
        // Log để debug
        error_log('setVerificationToken: User ID ' . $userId . ', Token length: ' . strlen($token) . ', First 20 chars: ' . substr($token, 0, 20));
    }

    public function findByVerificationToken(string $token): ?array
    {
        // Tìm kiếm token chính xác
        // Loại bỏ khoảng trắng và các ký tự không mong muốn
        $token = trim($token);
        
        if (empty($token)) {
            error_log('findByVerificationToken: Empty token provided');
            return null;
        }
        
        // Tìm kiếm token chính xác (case-sensitive vì token là hex string)
        // Chỉ tìm các token chưa được sử dụng (session_token IS NOT NULL)
        $sql = "SELECT * FROM {$this->table} 
                WHERE session_token = :token 
                AND session_token IS NOT NULL 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            error_log('findByVerificationToken: User found - ID: ' . ($user['user_id'] ?? 'N/A') . ', Token length: ' . strlen($token));
        } else {
            error_log('findByVerificationToken: No user found for token (length: ' . strlen($token) . ', first 20 chars: ' . substr($token, 0, 20) . ')');
        }
        
        return $user ?: null;
    }

    public function markVerified(int $userId): void
    {
        $sql = "UPDATE {$this->table} SET session_token = NULL, session_expires = NULL WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $sql = "UPDATE {$this->table} SET password = :password WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'password' => $passwordHash,
            'id'       => $userId,
        ]);
    }

    public function delete(int $userId): void
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    public function findById(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function update(int $userId, array $data): void
    {
        $sql = "UPDATE {$this->table} SET 
                first_name = :first_name,
                last_name = :last_name,
                full_name = :full_name,
                gender = :gender,
                birthday = :birthday,
                phone = :phone,
                address = :address,
                email = :email
                WHERE user_id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'full_name'  => trim($data['first_name'] . ' ' . $data['last_name']),
            'gender'     => $data['gender'],
            'birthday'   => $data['birthday'],
            'phone'      => $data['phone'] ?? null,
            'address'    => $data['address'] ?? null,
            'email'      => $data['email'],
            'id'         => $userId,
        ]);
    }
}

