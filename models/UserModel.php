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

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} 
                (first_name, last_name, gender, birthday, phone, address, email, password, role, created_at, full_name) 
                VALUES (:first_name, :last_name, :gender, :birthday, :phone, :address, :email, :password, :role, NOW(), :full_name)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'gender'     => $data['gender'],
            'birthday'   => $data['birthday'],
            'phone'      => $data['phone'],
            'address'    => $data['address'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'role'       => $data['role'] ?? 'customer',
            'full_name'  => trim($data['first_name'] . ' ' . $data['last_name']),
        ]);

        return (int)$this->pdo->lastInsertId();
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
        $sql = "UPDATE {$this->table} SET session_token = :token, session_expires = :expires WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'token'   => $token,
            'expires' => $expiresAt,
            'id'      => $userId,
        ]);
    }

    public function findByVerificationToken(string $token): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE session_token = :token LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
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

    // Lấy tổng số người dùng
    public function getTotalCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM {$this->table}");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    // Lấy số lượng admin
    public function getAdminCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM {$this->table} WHERE role = 'admin'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    // Lấy số lượng khách hàng
    public function getCustomerCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM {$this->table} WHERE role = 'customer'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    // Lấy thông tin user theo ID
    public function findById(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Cập nhật thông tin cá nhân
    public function updateProfile(int $userId, array $data): void
    {
        $sql = "UPDATE {$this->table} 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    full_name = :full_name,
                    gender = :gender, 
                    birthday = :birthday, 
                    phone = :phone, 
                    address = :address
                WHERE user_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'full_name'  => trim($data['first_name'] . ' ' . $data['last_name']),
            'gender'     => $data['gender'],
            'birthday'   => $data['birthday'] ?: null,
            'phone'      => $data['phone'],
            'address'    => $data['address'],
            'id'         => $userId,
        ]);
    }
}

