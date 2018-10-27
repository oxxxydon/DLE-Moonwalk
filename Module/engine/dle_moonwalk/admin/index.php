<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev (https://lazydev.pro)
 * @version   1.1.1
 * @link      https://lazydev.pro
 */
 
if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

require_once ENGINE_DIR .'/dle_moonwalk/language/dle_moonwalk.lng';
require_once ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';
require_once ENGINE_DIR . '/dle_moonwalk/admin/class.admin.php';

$dle_moonwalk_config['version'] = '1.1.1';

$admin = new AdminTemplate($db, $member_id, $user_group, $config, $dle_moonwalk_config, $dle_moonwalk_lang, $cat_info, $dle_login_hash);

$admin->setMenu(
	[
		['', 'dle_moonwalk', 'home', $dle_moonwalk_lang[0]],
		['options', 'dle_moonwalk&action=options', 'settings', $dle_moonwalk_lang[1]]
	], $action);

$admin->headerTemplate();
$version = $admin->getVersion();
if ($dle_moonwalk_config['version'] < $version) {
echo <<<HTML
<script>
$(function() {
	$.toast({
		heading: "{$dle_moonwalk_lang[190]}",
		text: "{$dle_moonwalk_lang[191]} {$version}",
		showHideTransition: 'slide',
		position: 'top-right',
		icon: 'warning',
		stack: false,
		hideAfter: 10000
	});
});
</script>
HTML;
}
$admin->content($action);
$admin->footerTemplate();
?>