<?php


// dierectly access denied
defined('ABSPATH') || exit;

$files = [
    'classes/class-admin-menu',
];

foreach ( $files as $file ) {
    if( file_exists(  dirname( __FILE__ ) . '/' . $file . '.php' ) ){
        require_once dirname( __FILE__ ) . '/' . $file . '.php';
    }else{
        $not_found = dirname(__FILE__) . '/' . $file . '.php';
        throw new Exception("file not found {$not_found}", 1);
    }
}
