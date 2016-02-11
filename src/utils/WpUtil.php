<?php

namespace wpdeliverable;

use \Exception;

/**
 * Wordpress utils.
 */
if (!class_exists("wpdeliverable\\WpUtil")) {
	class WpUtil {

		/**
		 * Bootstrap from inside a plugin.
		 */
		public static function getWpLoadPath() {
			$path=dirname($_SERVER['SCRIPT_FILENAME']);

			while (!file_exists($path."/wp-load.php")) {
				if ($path=="/")
					throw new Exception("Not inside wordpress");

				$path=dirname($path);
			}

			return $path."/wp-load.php";
		}
	}
}