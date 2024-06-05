<?php

/*
* Plugin Name: Arena Wisata Galery
* Plugin URI: https://arenawisata.co.id/
* Description: Handle Galery. Upload dokumentasi wisata.
* Version: 0.1
* Author: Dimas Vindyawan
* Text Domain: my-basics-plugin
*/

$directory = __DIR__ . '\page';
$pluginFile = __FILE__;

add_action('admin_menu', 'arenawisata_galery');
function arenawisata_galery()
{
    $_title = 'Arenawisata Galery';
    add_menu_page(
        $_title,
        $_title,
        // 'administrator',
        'publish_posts', //Set to all user
        __FILE__,
        'arenawisata_galery_page',
        'dashicons-format-image',
        6
    );
    add_submenu_page(
        __FILE__,
        'Galery',
        'Galery',
        // 'administrator',
        'publish_posts', //Set to all user
        'galery',
        'arenawisata_galery_sp_galery'
    );
    add_submenu_page(
        __FILE__,
        'Upload',
        'Upload',
        // 'administrator',
        'publish_posts', //Set to all user
        'upload',
        'arenawisata_galery_sp_upload'
    );
}
// Main page
function arenawisata_galery_page()
{
    global $directory;
    include_once($directory . '/home.php');
}
// SUBMENU PAGE
function arenawisata_galery_sp_galery()
{
    global $directory;
    include_once($directory . '/galery.php');
}
function arenawisata_galery_sp_upload()
{
    global $directory, $pluginFile, $wpdb;
    include_once($directory . '/upload_page.php');
}


// ADD CSS File
add_action('admin_enqueue_scripts', 'add_styleandscript');
function add_styleandscript()
{
    wp_enqueue_style('homestyle', plugins_url("page/css/home.css?" . time(), __FILE__));
    wp_enqueue_script('homestyle', plugins_url("page/script/home.js?" . time(), __FILE__), NULL, NULL, true);
}


// DATABASE
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__ . '/'));
};

include_once($directory . '/model/initialtable.php');
// Create table after plugin is activated
new Initialtable(__FILE__);
