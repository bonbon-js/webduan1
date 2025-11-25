<?php

class UserModel extends BaseModel
{
    protected $table = 'users';

    // Đăng ký user mới
    public function register($data)
    {
        $sql = "INSERT INTO {$this->table} (full_name, email, password, phone, address, role, created_at) 
                VALUES (:full_name, :email, :password, :phone, :address, :role, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':full_name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':role' => $data['role'] ?? 'user',
        ]);
    }

    // Kiểm tra email đã tồn tại chưa
    public function checkEmailExists($email)
    {
        $sql = "SELECT user_id FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() !== false;
    }

    // Đăng nhập
    public function login($email, $password)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // Lấy user theo email
    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // Lấy user theo ID
    public function getUserById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Cập nhật thông tin cá nhân
    public function updateProfile($userId, $data)
    {
        $sql = "UPDATE {$this->table} SET full_name = :full_name, phone = :phone, address = :address 
                WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':full_name' => $data['name'],
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':id' => $userId
        ]);
    }

    // Đặt lại mật khẩu
    public function resetPassword($userId, $newPassword)
    {
        $sql = "UPDATE {$this->table} SET password = :password WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':id' => $userId
        ]);
    }

    // Lưu session token
    public function saveSessionToken($userId, $token)
    {
        $sql = "UPDATE {$this->table} SET session_token = :token, session_token_expires = DATE_ADD(NOW(), INTERVAL 30 DAY) 
                WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':token' => $token,
            ':id' => $userId
        ]);
    }

    // Xác thực session token
    public function verifySessionToken($token)
    {
        $sql = "SELECT * FROM {$this->table} WHERE session_token = :token AND session_token_expires > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    // Xóa session token (đăng xuất)
    public function clearSessionToken($userId)
    {
        $sql = "UPDATE {$this->table} SET session_token = NULL, session_token_expires = NULL WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }

    // Lấy user theo session token
    public function getUserBySessionToken($token)
    {
        $sql = "SELECT * FROM {$this->table} WHERE session_token = :token AND session_token_expires > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    // Lấy danh sách user với tùy chọn tìm kiếm (không phân trang)
    public function getAllUsers($keyword = '')
    {
        $sql = "SELECT user_id, full_name, email, phone, address, role, created_at FROM {$this->table}";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE full_name LIKE :keyword OR email LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Lấy danh sách user có phân trang
    public function getUsers($limit = 10, $offset = 0, $keyword = '')
    {
        $sql = "SELECT user_id, full_name, email, phone, role, created_at FROM {$this->table}";

        if (!empty($keyword)) {
            $sql .= " WHERE full_name LIKE :keyword OR email LIKE :keyword OR phone LIKE :keyword";
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Đếm tổng số user cho phân trang
    public function countUsers($keyword = '')
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE full_name LIKE :keyword OR email LIKE :keyword OR phone LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    // Tạo mới user từ trang quản trị
    public function createUser($data)
    {
        $sql = "INSERT INTO {$this->table} (full_name, email, password, phone, role, created_at) 
                VALUES (:full_name, :email, :password, :phone, :role, NOW())";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':phone' => $data['phone'] ?: null,
            ':role' => $data['role'] ?? 'user',
        ]);
    }

    // Cập nhật user theo ID
    public function updateById($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET full_name = :full_name,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    role = :role
                WHERE user_id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'] ?: null,
            ':address' => $data['address'] ?: null,
            ':role' => $data['role'] ?? 'user',
            ':id' => $id,
        ]);
    }

    // Xóa user
    public function deleteUser($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
