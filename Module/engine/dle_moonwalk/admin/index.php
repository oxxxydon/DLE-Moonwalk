<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   1.1.3
 * @link      https://lazydev.pro
 */
 
if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

require_once ENGINE_DIR .'/dle_moonwalk/language/dle_moonwalk_admin_lang.lng';
require_once ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';
require_once ENGINE_DIR . '/dle_moonwalk/admin/functions.php';

$dle_moonwalk_config['version'] = '1.1.3';

switch ($action) {
	default:
		include ENGINE_DIR . '/dle_moonwalk/admin/options.php';
		break;
}
?>