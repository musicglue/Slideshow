<?php
/*
 Plugin Name: Slideshow
 Plugin URI: http://wordpress.org/extend/plugins/slideshow-jquery-image-gallery/
 Description: The slideshow plugin is easily deployable on your website. Add any image that has already been uploaded to add to your slideshow, add text slides, or even add a video. Options and styles are customizable for every single slideshow on your website.
 Version: 2.2.17
 Requires at least: 3.3
 Author: StefanBoonstra
 Author URI: http://stefanboonstra.com/
 License: GPLv2
*/

/**
 * Class SlideshowPluginMain fires up the application on plugin load and provides some
 * methods for the other classes to use like the auto-includer and the
 * base path/url returning method.
 *
 * @since 1.0.0
 * @author Stefan Boonstra
 */
class SlideshowPluginMain
{
	/** @var string $version */
	static $version = '2.2.17';

	/**
	 * Bootstraps the application by assigning the right functions to
	 * the right action hooks.
	 *
	 * @since 1.0.0
	 */
	static function bootStrap()
	{
		self::autoInclude();

		// Initialize localization on init
		add_action('init', array(__CLASS__, 'localize'));

		// Ajax requests
		SlideshowPluginAJAX::init();

		// Register slideshow post type
		SlideshowPluginPostType::init();

		// Add general settings page
		SlideshowPluginGeneralSettings::init();

		// Initialize stylesheet builder
		SlideshowPluginSlideshowStylesheet::init();

		// Deploy slideshow on do_action('slideshow_deploy'); hook.
		add_action('slideshow_deploy', array('SlideshowPlugin', 'deploy'));

		// Initialize shortcode
		SlideshowPluginShortcode::init();

		// Register widget
		add_action('widgets_init', array('SlideshowPluginWidget', 'registerWidget'));

		// Initialize plugin updater
		SlideshowPluginInstaller::init();

		// Include backend script TODO Move temporary statement
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueueBackendScripts'), 1);
	}

	/**
	 * TODO Move temporary function
	 *
	 * TODO Make sure this doesn't conflict with any other plugin, as there's currently an issue with the Gravity Forms plugin:
	 * TODO http://wordpress.org/support/topic/this-plugin-breaks-gravity-forms-edit-fields
	 *
	 * Includes backend script
	 *
	 * @since 2.2.12
	 */
	static function enqueueBackendScripts()
	{
		wp_enqueue_media();

		wp_enqueue_script(
			'slideshow-jquery-image-gallery-backend-script',
			self::getPluginUrl() . '/js/min/all.backend.min.js',
			array(
				'jquery',
				'jquery-ui-sortable',
				'wp-color-picker'
			),
			SlideshowPluginMain::$version
		);

		wp_enqueue_style(
			'slideshow-jquery-image-gallery-backend-style',
			self::getPluginUrl() . '/css/all.backend.css',
			array(
				'wp-color-picker'
			),
			SlideshowPluginMain::$version
		);
	}

	/**
	 * Translates the plugin
	 *
	 * @since 1.0.0
	 */
	static function localize()
	{
		load_plugin_textdomain(
			'slideshow-plugin',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages/'
		);
	}

	/**
	 * Returns url to the base directory of this plugin.
	 *
	 * @since 1.0.0
	 * @return string pluginUrl
	 */
	static function getPluginUrl()
	{
		return plugins_url('', __FILE__);
	}

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @since 1.0.0
	 * @return string pluginPath
	 */
	static function getPluginPath()
	{
		return dirname(__FILE__);
	}

	/**
	 * This function will load classes automatically on-call.
	 *
	 * @since 1.0.0
	 */
	static function autoInclude()
	{
		if (!function_exists('spl_autoload_register'))
		{
			return;
		}

		function slideshowPluginAutoLoader($name)
		{
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $name . '.php';

			if (is_file($file))
			{
				require_once $file;
			}
		}

		spl_autoload_register('slideshowPluginAutoLoader');
	}
}

/**
 * Activate plugin
 */
SlideShowPluginMain::bootStrap();