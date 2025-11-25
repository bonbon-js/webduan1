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

    // Routes quản trị đơn hàng
    'admin-orders'       => (new AdminOrderController)->index(),
    'admin-order-update' => (new AdminOrderController)->updateStatus(),
    
    // Auth routes
    'show-login'            => (new AuthController)->showLogin(),
    'show-register'         => (new AuthController)->showRegister(),
    'show-forgot'           => (new AuthController)->showForgotPassword(),
    'login'                 => (new AuthController)->login(),
    'register'              => (new AuthController)->register(),
    'logout'                => (new AuthController)->logout(),
    'forgot-password'       => (new AuthController)->handleForgotPassword(),
    'verify-account'        => (new AuthController)->verifyAccount(),
    'reset-password'        => (new AuthController)->showResetPassword(),
    'reset-password-submit' => (new AuthController)->resetPassword(),
    
    default          => (new HomeController)->index(),
};