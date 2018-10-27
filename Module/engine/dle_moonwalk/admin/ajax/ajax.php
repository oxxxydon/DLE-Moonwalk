<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev (https://lazydev.pro)
 * @version   1.1.1
 * @link      https://lazydev.pro
 */

@error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', substr(dirname(__FILE__), 0, -31));
define('ENGINE_DIR', ROOT_DIR . '/engine');

$is_logged = false;

if (file_exists(ENGINE_DIR . '/classes/plugins.class.php')) {
	require_once (ENGINE_DIR . '/classes/plugins.class.php');
	include_once (DLEPlugins::Check(ENGINE_DIR . '/inc/include/functions.inc.php'));

	dle_session();
	require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));
} else {
	include ENGINE_DIR . '/data/config.php';
	require_once ENGINE_DIR . '/classes/mysql.php';
	require_once ENGINE_DIR . '/data/dbconfig.php';
	require_once ENGINE_DIR . '/inc/include/functions.inc.php';

	dle_session();
	require_once ENGINE_DIR . '/modules/sitelogin.php';
}
require_once ENGINE_DIR .'/dle_moonwalk/language/dle_moonwalk.lng';
@header("Content-type: text/html; charset=" . $config['charset']);

date_default_timezone_set($config['date_adjust']);
$_TIME = time();

if (!$is_logged || $member_id['user_group'] != 1) {
	echo json_encode(['head' => $dle_moonwalk_lang['error'], 'text' => $dle_moonwalk_lang[94], 'icon' => 'error']);
	return;
}

$_POST['user_hash'] = trim($_POST['user_hash']);
if ($_POST['user_hash'] == '' || $_POST['user_hash'] != $dle_login_hash) {
	echo json_encode(['head' => $dle_moonwalk_lang['error'], 'text' => $dle_moonwalk_lang[94], 'icon' => 'error']);
	return;
}

$action = isset($_POST['action']) ? trim(strip_tags($_POST['action'])) : false;

if ($action == 'options') {
	
	$data_form = isset($_POST['data_form']) ? $_POST['data_form'] : false;
	if ($data_form) {
		parse_str($data_form, $array_post);
	}
	
	$new_array = [];
	foreach ($array_post as $index => $item) {
		foreach ($item as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					if ($v != '' && $v != '-') {
						$v = is_numeric($v) ? intval($v) : strip_tags(stripslashes($v));
						$new_array[$index][$key][] = $v;
					}
				}
			} else {
				if ($value != '' && $value != '-') {
					$value = is_numeric($value) ? intval($value) : strip_tags(stripslashes($value));
					$new_array[$index][$key] = $value;
				}
			}
		}
	}
	
	$handler = fopen(ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php', 'w');
	fwrite($handler, "<?PHP \n\n//DLE Moonwalk by LazyDev\n\n\$dle_moonwalk_config = ");
	fwrite($handler, var_export($new_array, true));
	fwrite($handler, ";\n\n?>");
	fclose($handler);
	echo json_encode(['head' => $dle_moonwalk_lang['its_ok'], 'text' => $dle_moonwalk_lang['options_save'], 'icon' => 'success']);
}
?>