<?php


class Plugins {


	/**
	 * Find plugin by key and launch his
	 *
	 * @param string $key
	 * @param mixed $params
	 */
	public static function intercept($key, $params = array()) {
		$plugins = glob(ROOT . '/sys/plugins/' . $key . '*');
	
		if (count($plugins) > 0 && is_array($plugins)) {
			foreach ($plugins as $plugin) {
				if (!is_dir($plugin)) continue;
				
				$pl_conf = file_get_contents($plugin . '/config.dat');
				$pl_conf = unserialize($pl_conf);
				if (empty($pl_conf['active'])) continue;
				
				
				include_once $plugin . '/index.php';
				
				$pl_obj = new $pl_conf['className']($params);
				$params = $pl_obj->common($params);
			}
		}
		
		return $params;
	}
}
?>