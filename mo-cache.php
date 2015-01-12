<?php
/**
 * @package MOCache
 */

/*
Plugin Name: MO Cache
Plugin URI: https://github.com/khromov/wordpress-mo-cache
Description: Improving the site performance by caching translation files using the WordPress standard cache mechanism.
Author: Masaki Takeuchi, khromov
Version: 12.1
Author URI: https://github.com/khromov/wordpress-mo-cache
License: MIT
*/

class MOCache
{
	const VERSION = '12.1';
	const GROUP   = 'mo';

	public static function setup()
	{
		//Override loading of mo files
		add_filter('override_load_textdomain', array( new self, 'load' ), 10, 3 );
	}
	
	/** This will substitute load_textdomain, which usually resides in wp-includes/l10n.php **/
	public function load($override, $domain, $mofile)
	{
		global $l10n;
		
		//Perform actions which are hooked to load_textdomain
		do_action('load_textdomain', $domain, $mofile);
		
		//Apply filters for plugins that modify .mo file paths (adding, deleting them etc)
		$mofile = apply_filters('load_textdomain_mofile', $mofile, $domain );
		
		//Check if the translation file exists.
		if (!is_readable($mofile))
			return false;
		
		//Add a cache global group. This relies on object-cache.php, whichever you have.
		if(function_exists('wp_cache_add_global_groups'))
			wp_cache_add_global_groups(self::GROUP);

		//Generate object cache key name
		$key = md5(self::VERSION . "-${GLOBALS['wp_version']}-$mofile");
		
		$mo = new MO();
		
		//Do we have cache?
		if ($cache = wp_cache_get($key, self::GROUP))
		{
			//Yes! Set the entries
			$mo->entries = $cache['entries'];
			$mo->set_headers( $cache['headers'] );
		}
		else //No cache, let's prime it
		{
			//Attempt to load from file
			if (!$mo->import_from_file($mofile))
				return false;
			
			//If we managed to load the file, set the entries
			$cache = array(
				'entries' => $mo->entries,
				'headers' => $mo->headers,
			);
			
			//Put in cache! //serialize()
			wp_cache_set($key, $cache, self::GROUP);
		}

		if (isset($l10n[$domain]))
			$mo->merge_with($l10n[$domain]);
		
		$l10n[$domain] = &$mo;
		
		return true;
	}
}

MOCache::setup();
