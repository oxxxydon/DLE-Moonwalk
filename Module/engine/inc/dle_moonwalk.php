<?PHP
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   1.1.2
 * @link      https://lazydev.pro
 */

if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	header('HTTP/1.1 403 Forbidden');
	header('Location: ../../');
	die('Hacking attempt!');
}

if ($member_id['user_group'] != 1) {
	msg('error', $lang['addnews_denied'], $lang['db_denied']);
}

include ENGINE_DIR . '/dle_moonwalk/admin/index.php';
?>