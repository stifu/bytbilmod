<?php
/**
 * Bytbil Mod - Entry Point
 * 
 * @copyright 	(C) Copyright 2010 SoftPower Technology AB
 * @Module 		Bytbil Mod
 * @license		GNU/GPL
 * Any questions regarding this module shall be directed to:
 * stefan@softpower.se.
 * 
 * This piece of software is released "as is" and comes with no waranties;
 * use and modify it at your own discression. If you have any concern regarding the 
 * quality of the code or its security; please let me know.
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::stylesheet('styles.css', 'modules/mod_bytbil/tmpl/css/');
JHTML::script('jscript.js', 'modules/mod_bytbil/tmpl/js/', $mootools = false);

//$document->addStyleSheet(dirname(__FILE__).DS.'tmpl'.DS.'css'.DS.'styles.css');

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );
 

$xml = bytbilHelper::initialize($params);
$page = bytbilHelper::currentPage('main');
$subPage = bytbilHelper::currentPage('sub');
$defaultURL = bytbilHelper::thisURL($page);

require( JModuleHelper::getLayoutPath( 'mod_bytbil' ) );
?>
