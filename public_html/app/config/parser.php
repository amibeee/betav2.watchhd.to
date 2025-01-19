<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(

	// ------------------------------------------------------------------------
	// Register extensions to their parsers, either classname or array config
	// ------------------------------------------------------------------------
	'extensions' => array(
		'twig'     => array('class' => 'View_Twig', 'extension' => 'twig')
	),

	// TWIG ( http://www.twig-project.org/documentation )
	// ------------------------------------------------------------------------
	'View_Twig' => array(
		'auto_encode' => true,
		'views_paths' => array(APPPATH.'views'),
		'delimiters'  => array(
			'tag_block'    => array('left' => '{%', 'right' => '%}'),
			'tag_comment'  => array('left' => '{#', 'right' => '#}'),
			'tag_variable' => array('left' => '{{', 'right' => '}}'),
		),
		'environment' => array(
			'debug'               => false,
			'charset'             => 'utf-8',
			'base_template_class' => 'Twig_Template',
			'cache'               => false,
			'auto_reload'         => true,
			'strict_variables'    => false,
			'autoescape'          => true,
			'optimizations'       => -1,
		),
		'extensions' => array(
			'Twig_Fuel_Extension',
		),
	),


);
