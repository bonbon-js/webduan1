<?php

$action = $_GET['action'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$accountController = new AccountController();

match ($action) {
    '/'                 => (new HomeController)->index(),
    'register'          => $method === 'POST' 
                            ? $authController->register() 
                            : $authController->showRegister(),
    'login'             => $method === 'POST' 
                            ? $authController->login() 
                            : $authController->showLogin(),
    'logout'            => $authController->logout(),
    'profile'           => $method === 'POST' 
                            ? $authController->updateProfile() 
                            : $authController->showProfile(),
    'forgot-password'   => $method === 'POST' 
                            ? $authController->forgotPassword() 
                            : $authController->showForgotPassword(),
    'reset-password'    => $method === 'POST' 
                            ? $authController->resetPassword() 
                            : $authController->showResetPassword(),
    'manage-users'      => $authController->manageUsers(),
    'accounts'          => $accountController->index(),
    'accounts-create'   => $accountController->create(),
    'accounts-store'    => $accountController->store(),
    'accounts-edit'     => $method === 'POST'
                            ? $accountController->update()
                            : $accountController->showEdit(),
    'accounts-delete'   => $accountController->delete(),
    default             => (new HomeController)->index(),
};