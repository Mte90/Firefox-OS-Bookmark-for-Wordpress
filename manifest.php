<?php

/**
 * Firefox_OS_Bookmark
 *
 * @package   Firefox_OS_Bookmark
 * @author    Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Mte90
 */

define( 'WP_USE_THEMES', false );        
require('../../../wp-blog-header.php');

//Get options
$manifest = ( array ) get_option( 'firefox-os-bookmark' );
//Local path
$clean_url = ABSPATH.str_replace(get_bloginfo('url'),'',$manifest['icon']);
//Absolute url
$url = parse_url(dirname($manifest['icon']));
//Execute the resize
$img = wp_get_image_editor( $clean_url );
unset($manifest['icon']);
$manifest['icons'] = array();

if ( ! is_wp_error( $img ) ) {
 
    $sizes_array = array(
        array ('width' => 16, 'height' => 16, 'crop' => true),
		array ('width' => 32, 'height' => 32, 'crop' => true),
		array ('width' => 48, 'height' => 48, 'crop' => true),
		array ('width' => 60, 'height' => 60, 'crop' => true),
		array ('width' => 64, 'height' => 64, 'crop' => true),
		array ('width' => 90, 'height' => 90, 'crop' => true),
		array ('width' => 120, 'height' => 120, 'crop' => true),
		array ('width' => 128, 'height' => 128, 'crop' => true),
    );
 
    $resize = $img->multi_resize( $sizes_array );
 
    foreach ($resize as $row) {
		$manifest['icons'][$row['width']] = $url['path'].$row['file'];
    }
}
$manifest['installs_allowed_from'] = ["*"];

//Clean JSON
$manifest_ready = str_replace('\\','',json_encode($manifest));

header('Content-type: application/x-web-app-manifest+json');

echo $manifest_ready;