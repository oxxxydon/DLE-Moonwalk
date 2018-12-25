<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   2.0.0
 * @link      https://lazydev.pro/
 */

@error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', substr(dirname(__FILE__), 0, -29));
define('ENGINE_DIR', ROOT_DIR . '/engine');

$is_logged = false;

if (file_exists(ENGINE_DIR . '/classes/plugins.class.php')) {
	include_once ENGINE_DIR . '/classes/plugins.class.php';
} else {
	@include_once (ENGINE_DIR . '/data/config.php');
	require_once (ENGINE_DIR . '/classes/mysql.php');
	require_once (ENGINE_DIR . '/data/dbconfig.php');

	abstract class DLEPlugins {
		public static function Check($source = '') {
			return $source;
		}
	}
}

include_once (DLEPlugins::Check(ENGINE_DIR . '/inc/include/functions.inc.php'));
dle_session();
require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));
require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/thumb.class.php'));

if (file_exists(DLEPlugins::Check(ROOT_DIR . '/language/' . $config['langs'] . '/adminpanel.lng'))) {
    include_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $config['langs'] . '/adminpanel.lng'));
}

require_once ENGINE_DIR . '/dle_moonwalk/language/dle_moonwalk_admin_lang.lng';
@header("Content-type: text/html; charset=" . $config['charset']);

date_default_timezone_set($config['date_adjust']);
$_TIME = time();
if (!$is_logged || $member_id['user_group'] != 1) {
	echo json_encode(['head' => $dle_moonwalk_admin_lang['error'], 'text' => $dle_moonwalk_admin_lang[109], 'icon' => 'error']);
	exit;
}

$_POST['user_hash'] = trim($_POST['user_hash']);
if ($_POST['user_hash'] == '' || $_POST['user_hash'] != $dle_login_hash) {
	echo json_encode(['head' => $dle_moonwalk_admin_lang['error'], 'text' => $dle_moonwalk_admin_lang[109], 'icon' => 'error']);
	exit;
}

$action = isset($_POST['action']) ? trim(strip_tags($_POST['action'])) : false;

include ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';
include ENGINE_DIR . '/dle_moonwalk/helpers/dle_moonwalk.php';

if ($action == 'searchAdmin') {
	$fieldChoose = isset($_POST['fieldChoose']) ? trim(strip_tags($_POST['fieldChoose'])) : false;
	$searchData = isset($_POST['searchData']) ? trim(strip_tags($_POST['searchData'])) : false;

	if (!$searchData) {
		echo json_encode(['head' => $dle_moonwalk_admin_lang['error'], 'text' => $dle_moonwalk_admin_lang[110], 'icon' => 'error']);
		exit;
	}
	
	echo dleMoonwalk::realize()->config($config, $dle_moonwalk_config, $dle_moonwalk_admin_lang)->parseAdmin($fieldChoose, $searchData)->templateAdmin();
} elseif ($action == 'setData') {
    $user_group = get_vars('usergroup');
    $news_id = isset($_POST['news_id']) ? intval($_POST['news_id']) : 0;
	$type = isset($_POST['type']) ? trim(strip_tags($_POST['type'])) : false;
	$token = isset($_POST['token']) ? trim(strip_tags($_POST['token'])) : false;
	echo dleMoonwalk::realize()->config($config, $dle_moonwalk_config, $dle_moonwalk_admin_lang)->setData($type, $token, $member_id, $user_group, $db, $news_id);
}