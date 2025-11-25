<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'              => (new HomeController)->index(),
    
    // Product routes (BB-01, BB-12)
    'products'       => (new ProductController)->index(),
    'product-detail' => (new ProductController)->detail(),
    
    // Admin Product routes (BB-03)
    'admin-products'        => (new ProductController)->adminList(),
    'admin-product-create'  => (new ProductController)->adminCreate(),
    'admin-product-store'   => (new ProductController)->adminStore(),
    'admin-product-edit'    => (new ProductController)->adminEdit(),
    'admin-product-update'  => (new ProductController)->adminUpdate(),
    'admin-product-delete'  => (new ProductController)->adminDelete(),
    
    // Admin Category routes (BB-03)
    'admin-categories'       => (new CategoryController)->adminList(),
    'admin-category-create'  => (new CategoryController)->adminCreate(),
    'admin-category-store'   => (new CategoryController)->adminStore(),
    'admin-category-edit'    => (new CategoryController)->adminEdit(),
    'admin-category-update'  => (new CategoryController)->adminUpdate(),
    'admin-category-delete'  => (new CategoryController)->adminDelete(),
    
    // Admin Variant routes (BB-03)
    'admin-product-variants'      => (new ProductController)->adminVariants(),
    'admin-variant-create'        => (new ProductController)->adminVariantCreate(),
    'admin-variant-store'         => (new ProductController)->adminVariantStore(),
    'admin-variant-edit'          => (new ProductController)->adminVariantEdit(),
    'admin-variant-update'        => (new ProductController)->adminVariantUpdate(),
    'admin-variant-delete'        => (new ProductController)->adminVariantDelete(),
    
    // Admin Attribute routes (BB-03)
    'admin-attributes'            => (new AttributeController)->adminList(),
    'admin-attribute-create'      => (new AttributeController)->adminCreate(),
    'admin-attribute-store'       => (new AttributeController)->adminStore(),
    'admin-attribute-edit'        => (new AttributeController)->adminEdit(),
    'admin-attribute-update'      => (new AttributeController)->adminUpdate(),
    'admin-attribute-delete'      => (new AttributeController)->adminDelete(),
    'admin-attribute-add-value'   => (new AttributeController)->adminAddValue(),
    'admin-attribute-delete-value' => (new AttributeController)->adminDeleteValue(),
    
    // Admin Product Images routes (BB-03 - Quản lý nhiều hình ảnh)
    'admin-product-images'        => (new ProductController)->adminImages(),
    'admin-product-upload-image'  => (new ProductController)->adminUploadImage(),
    'admin-product-delete-image'  => (new ProductController)->adminDeleteImage(),
    'admin-product-set-primary'   => (new ProductController)->adminSetPrimaryImage(),
    
    // Admin Product Attributes routes (BB-03 - Gán thuộc tính cho sản phẩm)
    'admin-product-attributes'    => (new ProductController)->adminAttributes(),
    'admin-product-assign-attribute' => (new ProductController)->adminAssignAttribute(),
    'admin-product-remove-attribute' => (new ProductController)->adminRemoveAttribute(),
    
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