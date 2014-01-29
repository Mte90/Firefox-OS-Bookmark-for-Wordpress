<?php

/**
 * Plugin Name.
 *
 * @package   Firefox_OS_Bookmark
 * @author    Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Mte90
 */

define( 'WP_USE_THEMES', false );        
require('../../../wp-blog-header.php');

$manifest = ( array ) get_option( 'firefox-os-bookmark' );

$manifest_ready = str_replace('\\','',json_encode($manifest));

echo $manifest_ready;