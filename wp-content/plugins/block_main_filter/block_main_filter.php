<?php
/*
  Plugin Name: Block Main Filter
  Description: Registers block for multistep filter
  Author: Miroslav Georgiev
  Text Domain: mg-block-main-filter

*/


// if the file is called directly
if (!defined('ABSPATH')) {
  exit('You are not allowed to access this file directly.');
}


function gutenberg_examples_01_register_block() {
 
    // automatically load dependencies and version
   // $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
 
    wp_register_script(
        'block-main-filter-js',
       get_site_url() . '/wp-content/plugins/block_main_filter/mg_block_main_filter.js',
       array());
 
    register_block_type( 'mg/block-main-filter', array(
    'editor_script' => 'block-main-filter-js'
    ) );
 
}
add_action( 'init', 'gutenberg_examples_01_register_block' );

