<?php

class CartController
{
    public function index()
    {
        // Lấy giỏ hàng từ session
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Logo cho view
        $logoUrl = BASE_URL . 'assets/images/logo.png';
        if (file_exists(PATH_ROOT . 'assets/images/logo.png')) {
            $data = file_get_contents(PATH_ROOT . 'assets/images/logo.png');
            $type = pathinfo(PATH_ROOT . 'assets/images/logo.png', PATHINFO_EXTENSION);
            $logoUrl = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        $view = 'cart';
        $title = 'Giỏ Hàng - BonBonWear';
        require_once PATH_VIEW . 'main.php';
    }

    public function add()
    {
        // Nhận dữ liệu JSON từ fetch
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            // Fallback cho form submit thường (nếu có)
            $productId = $_POST['product_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;
            $size = $_POST['size'] ?? 'M';
            $color = $_POST['color'] ?? 'Black';
        } else {
            $productId = $data['product_id'] ?? null;
            $quantity = $data['quantity'] ?? 1;
            $size = $data['size'] ?? 'M';
            $color = $data['color'] ?? 'Black';
        }

        if ($productId) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Giả lập thông tin sản phẩm (Trong thực tế sẽ query DB)
            $products = [
                1 => ['name' => 'Áo Thun Basic', 'price' => 350000, 'image' => 'assets/images/banner-hero.jpg'], 
                2 => ['name' => 'Quần Jean Slim', 'price' => 550000, 'image' => 'assets/images/banner-hero.jpg'],
                3 => ['name' => 'Áo Khoác Bomber', 'price' => 850000, 'image' => 'assets/images/banner-hero.jpg'],
                4 => ['name' => 'Váy Dạ Hội', 'price' => 1200000, 'image' => 'assets/images/banner-hero.jpg'],
            ];
            
            $product = $products[$productId] ?? [
                'name' => 'Sản phẩm ' . $productId, 
                'price' => 500000, 
                'image' => 'assets/images/banner-hero.jpg'
            ];

            // Tạo key duy nhất cho sản phẩm dựa trên ID + Size + Color
            $cartKey = $productId . '_' . $size . '_' . $color;

            // Nếu sản phẩm đã có trong giỏ -> tăng số lượng
            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            } else {
                // Thêm mới
                $_SESSION['cart'][$cartKey] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'size' => $size,
                    'color' => $color
                ];
            }

            echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
        exit;
    }

    public function update()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if ($productId && isset($_SESSION['cart'][$productId])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$productId]);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header('Location: ' . BASE_URL . '?action=cart-list');
        exit;
    }
    
    public function count() {
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }
        echo json_encode(['count' => $count]);
        exit;
    }
}
