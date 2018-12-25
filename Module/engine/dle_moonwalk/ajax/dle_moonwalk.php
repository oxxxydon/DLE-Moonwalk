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
define('ROOT_DIR', substr(dirname(__FILE__), 0, -25));
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

include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/functions.php'));

if ($config['lang_' . $config['skin']] && file_exists(DLEPlugins::Check(ROOT_DIR . '/language/' . $config['lang_' . $config['skin']] . '/website.lng'))) {
    include_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $config['lang_' . $config['skin']] . '/website.lng'));
} else {
    include_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $config['langs'] . '/website.lng'));
}
	
require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php'));
include ENGINE_DIR . '/dle_moonwalk/helpers/dle_moonwalk.php';

if (!$config['http_home_url']) {
    $config['http_home_url'] = explode('engine/dle_moonwalk/ajax/dle_moonwalk_film.php', $_SERVER['PHP_SELF']);
    $config['http_home_url'] = reset($config['http_home_url']);
}

$isSSL = dleMoonwalk::isSSL();
if (strpos($config['http_home_url'], '//') === 0) {
    $config['http_home_url'] = $isSSL ? $config['http_home_url'] = 'https:' . $config['http_home_url'] : $config['http_home_url'] = 'http:' . $config['http_home_url'];
} elseif (strpos($config['http_home_url'], '/') === 0) {
    $config['http_home_url'] = $isSSL ? $config['http_home_url'] = 'https://' . $_SERVER['HTTP_HOST'] . $config['http_home_url'] : 'http://' . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
} elseif($isSSL && stripos($config['http_home_url'], 'http://') !== false) {
    $config['http_home_url'] = str_replace('http://', 'https://', $config['http_home_url']);
}

if (substr($config['http_home_url'], -1, 1) != '/') {
    $config['http_home_url'] .= '/';
}

dle_session();
require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));

if ($_REQUEST['skin']) {
	$_REQUEST['skin'] = $_REQUEST['dle_skin'] = trim(totranslit($_REQUEST['skin'], false, false));
}

if ($_REQUEST['dle_skin']) {
	$_REQUEST['dle_skin'] = trim(totranslit($_REQUEST['dle_skin'], false, false));
	
	if($_REQUEST['dle_skin'] && @is_dir(ROOT_DIR . '/templates/' . $_REQUEST['dle_skin'])) {
		$config['skin'] = $_REQUEST['dle_skin'];
	} else {
		$_REQUEST['dle_skin'] = $_REQUEST['dle_skin'] = $config['skin'];
	}
} elseif ($_COOKIE['dle_skin']) {
	$_COOKIE['dle_skin'] = trim(totranslit((string)$_COOKIE['dle_skin'], false, false));

	if ($_COOKIE['dle_skin'] && is_dir(ROOT_DIR . '/templates/' . $_COOKIE['dle_skin'])) {
		$config['skin'] = $_COOKIE['dle_skin'];
	}
}

if (!$_SERVER['HTTP_REFERER'] || strpos($_SERVER['HTTP_REFERER'], $config['http_home_url']) === false) {
	return;
}

@header('Content-type: text/html; charset=' . $config['charset']);

define('TEMPLATE_DIR', ROOT_DIR . '/templates/' . $config['skin']);

date_default_timezone_set($config['date_adjust']);
$_TIME = time();

if (!$is_logged) {
	$member_id['user_group'] = 5;
}

if ($is_logged && $member_id['banned'] == 'yes') {
	die('User banned');
}

$user_group = get_vars('usergroup');

include ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';
include ENGINE_DIR . '/dle_moonwalk/language/dle_moonwalk_site_lang.lng';

$idKinopoisk = $dle_moonwalk_config['main']['id_kinopoisk'];
$idWorldArt = $dle_moonwalk_config['main']['id_worldart'];

$newsId = isset($_POST['newsId']) ? intval($_POST['newsId']) : false;
if ($newsId === false) {
	return;
}

$row = $db->super_query("SELECT xfields FROM " . PREFIX . "_post WHERE id='{$newsId}'");
$xfields = xfieldsdataload($row['xfields']);
if (!$xfields[$idKinopoisk] && !$xfields[$idWorldArt]) {
	return;
}

$idCache = $idFilm = $xfields[$idKinopoisk];
if (!$xfields[$idKinopoisk]) {
    $idFilm = $xfields[$idWorldArt];
    $idCache = 'w_' . $xfields[$idWorldArt];
}

$cache = dleMoonwalk::getVideoCache($idCache, $dle_moonwalk_config['film']['cache_time']);
if ($cache) {
	echo $cache;
	exit;
}

dleMoonwalk::realize()->config($config, $dle_moonwalk_config, $dle_moonwalk_site_lang);

$goodQualityArray = ['WEB-DL' => 1, 'WEB.DL' => 1, 'WEB-DLRip' => 1, 'BDRip' => 1, 'BluRay' => 1, 'Blu-ray' => 1, 'BDRemux' => 1, 'Rip1080_HDR' => 1, 'HDTV' => 1, 'HDRip' => 1, 'HDTVRip' => 1];
$normalQualityArray = ['DVDScr' => 1, 'DVDRip' => 1, 'WEBRip' => 1, 'SATRip' => 1, 'DVB' => 1, 'DVD' => 1];
$badQualityArray = ['TC' => 1, 'TS' => 1, 'Cam' => 1, 'CAMRip' => 1];

$url = 'http://dlemoon.online/api/videos.json?api_token=' . $dle_moonwalk_config['main']['api_token'] . '&kinopoisk_id=' . urlencode($idFilm);
$json = dleMoonwalk::curl($url);
$jsonArray = json_decode($json, true);
if ($jsonArray['error']) {
	return;
} else {
	$template = file_get_contents(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk/dle_moonwalk_film.tpl');
	preg_match_all('#\[voice-list\](.*?)\[\/voice-list\]#is', $template, $voiceList);
}

$badData = $goodData = $normalData = [];

foreach ($jsonArray as $key => $value) {
	$qualityMoonwalk = $value['camrip'] ? 'CAMRip' : 'HDRip';
	if ($value['source_type']) {
		$qualityMoonwalk = $value['source_type'];
	}

	if (!$value['translator']) {
		$value['translator'] = $dle_moonwalk_site_lang['original'];
	}
    
	if ($dle_moonwalk_config['main']['ssl']) {
        $value['iframe_url'] = preg_replace("#http(s)?://[^/]+/#iu", 'https://streamguard.cc/', $value['iframe_url']);
    } elseif ($dle_moonwalk_config['main']['domain'] != '') {
        $value['iframe_url'] = preg_replace("#http(s)?://[^/]+/#iu", 'http://' . $dle_moonwalk_config['main']['domain'] . '/', $value['iframe_url']);
    }
    
	if (isset($goodQualityArray[$qualityMoonwalk])) {
		$goodData[] = [$qualityMoonwalk, $value['iframe_url'], $value['instream_ads'], $value['token'], $value['translator']];
	} elseif (isset($normalQualityArray[$qualityMoonwalk])) {
		$normalData[] = [$qualityMoonwalk, $value['iframe_url'], $value['instream_ads'], $value['token'], $value['translator']];
	} else {
		$badData[] = [$qualityMoonwalk, $value['iframe_url'], $value['instream_ads'], $value['token'], $value['translator']];
	}
}

$dataReverse = array_merge($goodData, $normalData, $badData);
if ($dataReverse[0][4] == $dle_moonwalk_site_lang['Uk']) {
	$uaVoice = $dataReverse[0];
	unset($dataReverse[0]);
	array_push($dataReverse, $uaVoice);
}

$voiceListArray = [];
foreach ($dataReverse as $value) {
	if (!$value[4]) {
		$value[4] = 'Оригинал';
	}
    
	$tempVoice = str_replace('{token}', $value[1], $voiceList[1][0]);
	$tempVoice = str_replace('{voice}', $value[4], $tempVoice);
	$tempVoice = str_replace('{quality}', $value[0], $tempVoice);

	if ($value[2] == 1) {
		$tempVoice = str_replace('[adv]', '', $tempVoice);
		$tempVoice = str_replace('[/adv]', '', $tempVoice);
		$tempVoice = preg_replace('#\[not-adv\](.*?)\[\/not-adv\]#is', '', $tempVoice);
		$tempVoice = str_replace('{text-adv}', htmlentities($dle_moonwalk_site_lang['adv']), $tempVoice);
	} else {
		$tempVoice = preg_replace('#\[adv\](.*?)\[\/adv\]#is', '', $tempVoice);
		$tempVoice = str_replace('[not-adv]', '', $tempVoice);
		$tempVoice = str_replace('[/not-adv]', '', $tempVoice);
		$tempVoice = str_replace('{text-adv}', htmlentities($dle_moonwalk_site_lang['not_adv']), $tempVoice);
	}
	
	$voiceListArray[] = $tempVoice;
}

reset($dataReverse);
$template = preg_replace('#\[voice-list\](.*?)\[\/voice-list\]#is', implode($voiceListArray), $template);
$template = str_replace('{video}', $dataReverse[key($dataReverse)][1], $template);

dleMoonwalk::setVideoCache($idCache, $template);
echo $template;
?>