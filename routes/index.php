<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'              => (new HomeController)->index(),
    
    // Product routes
    'products'       => (new ProductController)->index(),
    'product-detail' => (new ProductController)->detail(),
    'product-attributes' => (new ProductController)->attributes(),
    'variant-images' => (new ProductController)->variantImages(),
    'search-api'     => (new ProductController)->searchApi(),
    'search-smart'   => (new ProductController)->searchSmart(),
    
    // Post routes
    'posts'          => (new PostController)->index(),
    'post-detail'    => (new PostController)->detail(),
    
    // Cart routes
    'cart-list'      => (new CartController)->index(),
    'cart-add'       => (new CartController)->add(),
    'cart-update'    => (new CartController)->update(),
    'cart-delete'    => (new CartController)->delete(),
    'cart-delete-multiple' => (new CartController)->deleteMultiple(),
    'cart-count'     => (new CartController)->count(),
    'cart-set-selected' => (new CartController)->setSelected(),
    
    // Checkout routes
    'checkout'       => (new CheckoutController)->index(),
    'checkout-process' => (new CheckoutController)->process(),
    
    // Coupon routes
    'coupon-validate' => (new CouponController)->validate(),
    'coupon-available' => (new CouponController)->getAvailable(),
    'coupon-remove' => (new CouponController)->remove(),

    // Routes đơn hàng cho user
    'order-history'  => (new OrderController)->history(),
    'order-detail'   => (new OrderController)->detail(),
    'order-cancel'   => (new OrderController)->cancel(),

    // Routes quản trị
    'admin-dashboard'    => (new AdminDashboardController)->index(),
    'admin-statistics'   => (new AdminStatisticsController)->index(),
    'admin-orders'       => (new AdminOrderController)->index(),
    'admin-order-update' => (new AdminOrderController)->updateStatus(),
    // Quản lý tài khoản
    'admin-users'        => (new AdminUserController)->index(),
    'admin-user-role'    => (new AdminUserController)->updateRole(),
    'admin-user-toggle-lock' => (new AdminUserController)->toggleLock(),
    
    // Quản lý mã giảm giá
    'admin-coupons'      => (new AdminCouponController)->index(),
    'admin-coupon-create' => (new AdminCouponController)->create(),
    'admin-coupon-update' => (new AdminCouponController)->update(),
    'admin-coupon-delete' => (new AdminCouponController)->delete(),
    'admin-coupons-trash' => (new AdminCouponController)->trash(),
    'admin-coupon-restore' => (new AdminCouponController)->restore(),
    'admin-coupon-force-delete' => (new AdminCouponController)->forceDelete(),
    
    // Quản lý danh mục
    'admin-categories'      => (new AdminCategoryController)->index(),
    'admin-category-create' => (new AdminCategoryController)->create(),
    'admin-category-edit'   => (new AdminCategoryController)->edit(),
    'admin-category-store'  => (new AdminCategoryController)->store(),
    'admin-category-update' => (new AdminCategoryController)->update(),
    'admin-category-delete' => (new AdminCategoryController)->delete(),
    
    // Quản lý sản phẩm
    'admin-products'      => (new AdminProductController)->index(),
    'admin-product-create' => (new AdminProductController)->create(),
    'admin-product-edit'   => (new AdminProductController)->edit(),
    'admin-product-store'  => (new AdminProductController)->store(),
    'admin-product-update' => (new AdminProductController)->update(),
    'admin-product-delete' => (new AdminProductController)->delete(),
    'admin-product-variant-store' => (new AdminProductController)->storeVariant(),
    'admin-product-variant-update' => (new AdminProductController)->updateVariant(),
    'admin-product-variant-delete' => (new AdminProductController)->deleteVariant(),
    
    // Auth routes
    'show-login'         => (new AuthController)->showLogin(),
    'show-register'      => (new AuthController)->showRegister(),
    'show-forgot'        => (new AuthController)->showForgotPassword(),
    'show-reset-password'=> (new AuthController)->showResetPassword(),
    'verify-account'     => (new AuthController)->verifyAccount(),
    'login'              => (new AuthController)->login(),
    'register'           => (new AuthController)->register(),
    'logout'             => (new AuthController)->logout(),
    'forgot-password'    => (new AuthController)->handleForgotPassword(),
    'reset-password'     => (new AuthController)->resetPassword(),
    'profile'            => (new AuthController)->showProfile(),
    'update-profile'     => (new AuthController)->updateProfile(),
    
    default          => (new HomeController)->index(),
};