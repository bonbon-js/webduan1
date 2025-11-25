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
    
    // Auth routes (Temporarily disabled)
    // 'show-login'     => (new AuthController)->showLogin(),
    // 'show-register'  => (new AuthController)->showRegister(),
    // 'login'          => (new AuthController)->login(),
    // 'register'       => (new AuthController)->register(),
    // 'logout'         => (new AuthController)->logout(),
    
    default          => (new HomeController)->index(),
};