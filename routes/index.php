<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'              => (new HomeController)->index(),
    
    // Cart routes
    'cart-list'      => (new CartController)->index(),
    'cart-add'       => (new CartController)->add(),
    'cart-update'    => (new CartController)->update(),
    'cart-delete'    => (new CartController)->delete(),
    'cart-count'     => (new CartController)->count(),
    
    // Checkout routes
    'checkout'       => (new CheckoutController)->index(),
    'checkout-process' => (new CheckoutController)->process(),

    // Routes đơn hàng cho user
    'order-history'  => (new OrderController)->history(),
    'order-detail'   => (new OrderController)->detail(),
    'order-cancel'   => (new OrderController)->cancel(),

    // Routes quản trị
    'admin-panel'        => (new AdminPanelController)->index(),
    'admin-stats'        => (new AdminStatsController)->index(),
    'admin-orders'       => (new AdminOrderController)->index(),
    'admin-order-update' => (new AdminOrderController)->updateStatus(),
    // Quản lý tài khoản
    'admin-users'        => (new AdminUserController)->index(),
    'admin-user-role'    => (new AdminUserController)->updateRole(),
    'admin-user-delete' => (new AdminUserController)->delete(),
    
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