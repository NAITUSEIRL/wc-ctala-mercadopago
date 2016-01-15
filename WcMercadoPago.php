<?php

/*
Plugin Name: Pagos con Mercado Pago
Plugin URI:  https://github.com/NAITUSEIRL/wc-ctala-mercadopago
Description: Utiliza Mercado Pagos para Woocommerce 
Version:     0.1
Author:      Cristian Tala Sánchez
Author URI:  http://www.cristiantala.cl
License:     MIT
License URI: http://opensource.org/licenses/MIT
Domain Path: /languages
Text Domain: ctala-text_domain
*/
include_once 'helpers/debug.php';


// Registramos los menus correspondientes

function ctala_setup_admin_menu() {
    add_menu_page('CTala', 'CTala', 'manage_options', 'ctala', 'ctala_view_admin');
    add_submenu_page('ctala', 'SubMen', 'Admin Page', 'manage_options', 'myplugin-top-level-admin-menu', 'myplugin_admin_page');
}

function ctala_view_admin() {
    include_once 'views/admin/viewAdmin.php';
}

add_action('admin_menu', 'ctala_setup_admin_menu');
?>
