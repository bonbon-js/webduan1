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

    // Routes quản trị đơn hàng
    'admin-orders'       => (new AdminOrderController)->index(),
    'admin-order-update' => (new AdminOrderController)->updateStatus(),
    // Quản lý tài khoản
    'admin-users'        => (new AdminUserController)->index(),
    'admin-user-role'    => (new AdminUserController)->updateRole(),
    'admin-user-delete' => (new AdminUserController)->delete(),
    
    // Quản lý mã giảm giá
    'admin-coupons'      => (new AdminCouponController)->index(),
    'admin-coupon-create' => (new AdminCouponController)->create(),
    'admin-coupon-update' => (new AdminCouponController)->update(),
    'admin-coupon-delete' => (new AdminCouponController)->delete(),
    'admin-coupons-trash' => (new AdminCouponController)->trash(),
    'admin-coupon-restore' => (new AdminCouponController)->restore(),
    'admin-coupon-force-delete' => (new AdminCouponController)->forceDelete(),
    
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
    
    default          => (new HomeController)->index(),
};