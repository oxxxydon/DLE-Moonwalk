<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   1.1.2
 * @link      https://lazydev.pro
 */

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include ENGINE_DIR . '/data/config.php';
require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';

$charset = 'utf8';
$action = isset($_POST['action']) ? strip_tags($_POST['action']) : false;

function removeOldVersion($dir) {
	$files = array_diff(scandir($dir), ['.', '..']);
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? removeOldVersion("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}
function dirEmpty($dir) {
	$handle = opendir($dir);
	while (false !== ($entry = readdir($handle))) {
		if ($entry != '.' && $entry != '..') {
			return FALSE;
		}
	}
	return TRUE;
}
$dataBase = [];
$dataBase[] = "DELETE FROM `{PREFIX}_admin_sections` WHERE `name`='dle_moonwalk'";
$dataBase[] = "INSERT INTO `{PREFIX}_admin_sections` (`name`, `title`, `descr`, `icon`, `allow_groups`) VALUES ('dle_moonwalk', 'DLE Moonwalk', 'Работа с Moonwalk', '', '1')";
$dataBase[] = "DROP TABLE IF EXISTS `{PREFIX}_dle_moonwalk`;";
$dataBase[] = "CREATE TABLE IF NOT EXISTS `{PREFIX}_dle_moonwalk` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`newsId` int(10) NOT NULL,
	`voice` varchar(512) NOT NULL,
	`season` int(10) NOT NULL,
	`seria` int(10) NOT NULL,
	`updateDate` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
	`updateMoonwalk` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
	`category` tinyint(1) NOT NULL DEFAULT '0',
	`quality` varchar(30) NOT NULL,
	`typeVideo` tinyint(1) NOT NULL DEFAULT '0',
	`translatorId` int(5) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET={CHAR} COMMENT='DLE Moonwalk by LazyDev.pro';";

if ($action == 'installDb') {
	foreach ($dataBase as $dataQuery) {
		$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataQuery));
	}
	echo json_encode(['step' => 'create']);
	exit;
}

if ($action == 'deleteDb') {
	$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataBase[0]));
	$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataBase[2]));
	echo json_encode(['step' => 'delete']);
	exit;
}

$update = false;
if (file_exists(ENGINE_DIR . '/mod_punpun/dle_moonwalk/config/dle_moonwalk.php')) {
	$update = true;
}

if ($action == 'delete') {
	if (!is_writable(ENGINE_DIR . '/inc/addnews.php') || !is_writable(ENGINE_DIR . '/inc/editnews.php')) {
		echo json_encode(['error' => 'Файлы <b>/engine/inc/addnews.php</b> и <b>/engine/inc/editnews.php</b> не доступны для записи.<br>Пожалуйста выставьте права на запись или выполните удаление вручную.', 'head' => 'Ошибка', 'message' => 'При удалении произошла ошибка.', 'install' => 'itself']);
		exit;
	}
	
	if (!is_writable(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk')) {
		echo json_encode(['error' => 'Папка <b>/templates/' . $config['skin'] . '/dle_moonwalk</b> не доступна для записи.<br>Пожалуйста выставьте права на запись или выполните удаление вручную.', 'head' => 'Ошибка', 'message' => 'При удалении произошла ошибка.', 'install' => 'itself']);
		exit;
	}
	
	file_put_contents(ENGINE_DIR . '/inc/addnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/addnews.php')));
	file_put_contents(ENGINE_DIR . '/inc/editnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/editnews.php')));
	
	removeOldVersion(ENGINE_DIR . '/dle_moonwalk');
	if (dirEmpty(ENGINE_DIR . '/dle_moonwalk')) {
		removeOldVersion(ENGINE_DIR . '/dle_moonwalk');
	}
	removeOldVersion(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk');
	if (dirEmpty(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk')) {
		removeOldVersion(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk');
	}
	$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataBase[0]));
	$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataBase[2]));
	if (file_exists(ROOT_DIR . '/dle_moonwalk_cron.php')) {
		unlink(ROOT_DIR . '/dle_moonwalk_cron.php');
	}
	echo json_encode(['delete' => 'ok']);
	unlink(ROOT_DIR . '/dle_moonwalk_install.php');
	exit;
}

if ($action == 'install') {
	$stepType = $update ? 'обновление' : 'установку';
	if (!is_writable(ENGINE_DIR . '/inc/addnews.php') || !is_writable(ENGINE_DIR . '/inc/editnews.php')) {
		echo json_encode(['error' => 'Файлы <b>/engine/inc/addnews.php</b> и <b>/engine/inc/editnews.php</b> не доступны для записи.<br>Пожалуйста выставьте права на запись или выполните ' . $stepType . ' вручную.', 'head' => 'Ошибка', 'message' => 'При установке произошла ошибка.', 'install' => 'itself']);
		exit;
	}
	
	if (!is_writable(ROOT_DIR . '/templates/' . $config['skin'])) {
		echo json_encode(['error' => 'Папка <b>/templates/' . $config['skin'] . '</b> не доступна для записи.<br>Пожалуйста выставьте права на запись или выполните ' . $stepType . ' вручную.', 'head' => 'Ошибка', 'message' => 'При установке произошла ошибка.', 'install' => 'itself']);
		exit;
	}
	
	if ($update) {
		echo json_encode(['step' => 'update']);
		removeOldVersion(ENGINE_DIR . '/mod_punpun/dle_moonwalk');
		if (dirEmpty(ENGINE_DIR . '/mod_punpun')) {
			removeOldVersion(ENGINE_DIR . '/mod_punpun');
		}
		
		file_put_contents(ENGINE_DIR . '/inc/addnews.php', str_replace('/mod_punpun/dle_moonwalk', '/dle_moonwalk', file_get_contents(ENGINE_DIR . '/inc/addnews.php')));
		file_put_contents(ENGINE_DIR . '/inc/editnews.php', str_replace('/mod_punpun/dle_moonwalk', '/dle_moonwalk', file_get_contents(ENGINE_DIR . '/inc/editnews.php')));
	} else {
		echo json_encode(['step' => 'install']);
		$tree = [
			'addnews.search' => [
				'replacement' => "\r\n// DLE Moonwalk begin\r\ninclude ENGINE_DIR . '/dle_moonwalk/inc/dle_moonwalk.php';\r\n// DLE Moonwalk end\r\n",
				'versions' => [
					'10.2' => [
						'file' => 'addnews.php',
						'path' => 'engine/inc',
						'pattern' => '#(echo\s*<<<HTML[^{}]*{\$output})#i',
						'type' => 'before',
					],
				],
			],
			'editnews.search' => [
				'replacement' => "\r\n// DLE Moonwalk begin\r\ninclude ENGINE_DIR . '/dle_moonwalk/inc/dle_moonwalk.php';\r\n// DLE Moonwalk end\r\n",
				'versions' => [
					'10.2' => [
						'file' => 'editnews.php',
						'path' => 'engine/inc',
						'pattern' => '#(echo\s*<<<HTML[^{}]*{\$output})#i',
						'type' => 'before',
					],
				],
			]
		];
		$matches = [];

		foreach ($tree as $key => $step) {
			$matches[$key] = false;

			foreach ($step['versions'] as $version => $replace) {
				if ((float) $config['version_id'] >= (float) $version) {
					$matches[$key] = [
						'file' => $replace['file'],
						'pattern' => $replace['pattern'],
						'replacement' => $step['replacement'],
						'type' => $replace['type'],
						'version' => (float) $version,
					];

					if ($replace['path']) {
						$matches[$key]['path'] = $replace['path'];
					}
				}
			}
		}
		
		foreach ($matches as $match) {
			$file = ROOT_DIR . '/' . ($match['path'] ? "{$match['path']}/" : '') . $match['file'];
			$pattern = $match['pattern'];
			$replacement = $match['replacement'];
			$type = $match['type'];

			file_put_contents($file, preg_replace($pattern, ($type == 'before' ? $replacement : '') . '$1' . ($type == 'after' ? $replacement : ''), file_get_contents($file)));
		}
	}
	
	rename(ENGINE_DIR . '/dle_moonwalk/tpl', ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk');
	
	foreach ($dataBase as $dataQuery) {
		$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataQuery));
	}
	
	unlink(ROOT_DIR . '/dle_moonwalk_install.php');
	exit;
}
$delete = false;
if (file_exists(ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php')) {
	$delete = true;
}
echo <<<HTML
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>DLE Moonwalk - Установка</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="HandheldFriendly" content="true">
		<meta name="format-detection" content="telephone=no">
		<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width"> 
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="default">
		<link href="engine/skins/stylesheets/application.css" media="screen" rel="stylesheet" type="text/css" />
		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
			<style>
				.step-text {
					list-style: none;
					padding-left: 0;
					counter-reset: toc1;
				}
				.step-text>li {
					position: relative;
					padding-left: 64px;
					margin-bottom: 48px;
					font-weight: 300;
				}
				.step-text>li::before {
					content: counter(toc1, decimal-leading-zero);
					counter-increment: toc1;
					position: absolute;
					left: 0;
					top: 0;
					color: #929daf;
					font-size: 24px;
					line-height: 44px;
					text-align: center;
					width: 46px;
					height: 46px;
					background-color: #f9fafb;
					border-radius: 10rem;
				}
				.step-text h5 {
					font-size: 15px;
					padding-top: 5px;
				}
		</style>
	</head>
	<body class="no-theme">
		<div class="navbar navbar-inverse bg-primary-700" style="background-image: linear-gradient(-20deg, #3a6a8a 0%, #463f5f 100%)!important;">
			<div class="navbar-header">
				<div style="color: #fff;font-weight: 600;text-shadow: none;font-size: 22px;letter-spacing: .1em;float: left;padding: 15px 20px;line-height: 20px;height: 50px;">Мастер установки DLE Moonwalk</div>
			</div>
		</div>
		<div class="page-container">
			<div class="page-content">
				<div class="col-md-8 col-md-offset-2 mt-20">
					<div class="panel panel-default">
						<div class="panel-heading">Мастер установки DLE Moonwalk</div>
						<div class="panel-body">
							<div id="firstStep">
								Добро пожаловать в мастер установки DLE Moonwalk. Данный мастер поможет вам установить скрипт всего за несколько минут. После установки мы настоятельно рекомендуем Вам ознакомиться с <a href="https://moonwalk.lazydev.pro" target="_blank">документацией</a> по работе со скриптом.<br><br>
								<span class="text-danger">Внимание: при установке скрипта создается таблица в базе данных и правятся файлы движка!</span><br><br>
								Приятной Вам работы,<br><br>
								LazyDev
							</div>
							<div id="errorStep" style="display:none;">
								<div class="alert alert-danger" role="alert">
									<h4 class="alert-heading"></h4>
									<p></p>
								</div>
							</div>
							<div id="itSelfUnistall" style="display:none;">
								<ul class="step-text">
									<li>
										<h5>Шаг первый</h5>
										<div>
											Откройте файл <b>/engine/inc/addnews.php</b> найдите и удалите
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;display: inline-flex;">
													<span style="color: #369;font-weight: 400;">include</span>&nbsp;
													<span style="color: #000;font-weight: 400;">ENGINE_DIR . </span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">'/dle_moonwalk/inc/dle_moonwalk.php'</span>
													<span style="color: #000;font-weight: 400;">;</span>
												</div>
											</div>
										</div>
									</li>
									<li>
										<h5>Шаг второй</h5>
										<div>
											Откройте файл <b>/engine/inc/editnews.php</b> найдите и удалите
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;display: inline-flex;">
													<span style="color: #369;font-weight: 400;">include</span>&nbsp;
													<span style="color: #000;font-weight: 400;">ENGINE_DIR . </span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">'/dle_moonwalk/inc/dle_moonwalk.php'</span>
													<span style="color: #000;font-weight: 400;">;</span>
												</div>
											</div>
										</div>
									</li>
									<li>
										<h5>Шаг третий</h5>
										<div>Удалите папку <b>/engine/dle_moonwalk</b> и <b>dle_moonwalk</b> в папке <u>вашего шаблона</u>.</div>
									</li>
									<li>
										<h5>Шаг четвертый</h5>
										<div>Нужно удалить таблицу в базе данных - для этого нажмите на кнопку <button id="deleteDb" class="btn bg-danger btn-sm btn-raised" style="border-radius: 0!important;line-height: 1.5384616!important;">Удалить таблицу</button></div>
									</li>
									<li>
										<h5>Шаг пятый</h5>
										<div>Удалите файл <b>dle_moonwalk_install.php</b> и <b>dle_moonwalk_cron.php</b></div>
									</li>
								</ul>
							</div>
							<div id="itSelfManual" style="display:none;">
								<ul class="step-text">
									<li>
										<h5>Шаг первый</h5>
										<div>
											Откройте файл <b>/engine/inc/addnews.php</b> 
HTML;
											if ($update) {
echo <<<HTML
											найдите
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;display: inline-flex;">
													<span style="color: #369;font-weight: 400;">include</span>&nbsp;
													<span style="color: #000;font-weight: 400;">ENGINE_DIR . </span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">'/mod_punpun/dle_moonwalk/inc/dle_moonwalk.php'</span>
													<span style="color: #000;font-weight: 400;">;</span>
												</div>
											</div>
HTML;
											} else {
echo <<<HTML
											и перед
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;">
													<span style="color: #369;font-weight: 400;">echo</span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">&lt;&lt;&lt;HTML</span>
													<br>
													<span style="color: #929daf;font-weight: 400;">&lt;/div></span>
													<br>
													<span style="color: #929daf;font-weight: 400;">&lt;/div></span>
													<br>
													<span style="color: #000;font-weight: 400;">{\$output}</span>
												</div>
											</div>
HTML;
											}
											echo $update ? 'Замените на' : 'Вставьте';
echo <<<HTML
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;display: inline-flex;">
													<span style="color: #369;font-weight: 400;">include</span>&nbsp;
													<span style="color: #000;font-weight: 400;">ENGINE_DIR . </span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">'/dle_moonwalk/inc/dle_moonwalk.php'</span>
													<span style="color: #000;font-weight: 400;">;</span>
												</div>
											</div>
										</div>
									</li>
									<li>
										<h5>Шаг второй</h5>
										<div>
											Откройте файл <b>/engine/inc/editnews.php</b> 
HTML;
											if ($update) {
echo <<<HTML
											найдите
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;display: inline-flex;">
													<span style="color: #369;font-weight: 400;">include</span>&nbsp;
													<span style="color: #000;font-weight: 400;">ENGINE_DIR . </span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">'/mod_punpun/dle_moonwalk/inc/dle_moonwalk.php'</span>
													<span style="color: #000;font-weight: 400;">;</span>
												</div>
											</div>
HTML;
											} else {
echo <<<HTML
											и перед
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;">
													<span style="color: #369;font-weight: 400;">echo</span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">&lt;&lt;&lt;HTML</span>
													<br>
													<span style="color: #929daf;font-weight: 400;">&lt;/div></span>
													<br>
													<span style="color: #929daf;font-weight: 400;">&lt;/div></span>
													<br>
													<span style="color: #000;font-weight: 400;">{\$output}</span>
												</div>
											</div>
HTML;
											}
											echo $update ? 'Замените на' : 'Вставьте';
echo <<<HTML
											<div style="background-color: #fdfdfd; margin-bottom: 1em; border: 1px solid #d9d9d9; padding: 5px; position: relative; margin: .5em 0; overflow: visible; font-size: 1.275rem; font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; text-align: left; word-spacing: normal; word-break: normal; word-wrap: normal; line-height: 1.5;">
												<div style="max-height: inherit; height: inherit; padding: 0 5px; overflow: auto;display: inline-flex;">
													<span style="color: #369;font-weight: 400;">include</span>&nbsp;
													<span style="color: #000;font-weight: 400;">ENGINE_DIR . </span>&nbsp;
													<span style="color: #929daf;font-weight: 400;">'/dle_moonwalk/inc/dle_moonwalk.php'</span>
													<span style="color: #000;font-weight: 400;">;</span>
												</div>
											</div>
										</div>
									</li>
									<li>
										<h5>Шаг третий</h5>
										<p>Создайте папку <b>dle_moonwalk</b> в <u>папке вашего шаблона</u> и скопируйте туда все содержимое папки <b>/engine/dle_moonwalk/tpl</b>.</p>
									</li>
									<li>
										<h5>Шаг четвертый</h5>
										<div>Нужно создать таблицу в базе данных - для этого нажмите на кнопку <button id="installDb" class="btn bg-danger btn-sm btn-raised" style="border-radius: 0!important;line-height: 1.5384616!important;">Создать таблицу</button></div>
									</li>
HTML;
									if ($update) {
echo <<<HTML
										<li>
											<h5>Шаг пятый</h5>
											<p>Удалите папку <b>/engine/mod_punpun/dle_moonwalk</b></p>
										</li>
										<li>
											<h5>Шаг шестой</h5>
											<p>Удалите файл <b>dle_moonwalk_install.php</b> и настройте модуль в админ панели.</p>
										</li>
HTML;
									} else {
echo <<<HTML
										<li>
											<h5>Шаг пятый</h5>
											<p>Удалите файл <b>dle_moonwalk_install.php</b> и настройте модуль в админ панели.</p>
										</li>
HTML;
									}
echo <<<HTML
								</ul>
							</div>
							<div id="installStep" style="display:none;">
								<ul class="step-text">
									<li id="StepInstall" style="display:none;">
										<h5>Начало установки.</h5>
									</li>
									<li id="StepUpdate" style="display:none;">
										<h5>Начало обновления.</h5>
									</li>
									<li id="StepDelteOld" style="display:none;">
										<h5>Старая версия успешно удалена.</h5>
									</li>
									<li id="StepFiles" style="display:none;">
										<h5>Изменения в файлах движка успешно сделаны.</h5>
									</li>
									<li id="StepDataBase" style="display:none;">
										<h5>Таблица в базе данных успешно создана.</h5>
									</li>
								</ul>
							</div>
						</div>
						<div class="panel-footer">
							<a href="{$config['http_home_url']}{$config['admin_path']}?mod=dle_moonwalk" id="adminLink" class="btn bg-teal btn-sm btn-raised position-left" style="display: none;border-radius: 0!important;font-size:15px;">Перейти в админ панель модуля</a>
							<button id="installButton" class="btn bg-slate btn-sm btn-raised position-left" style="border-radius: 0!important;font-size:15px;">Начать установку</button>
							<button id="selfInstall" class="btn bg-info-800 btn-sm btn-raised position-left" style="display: none;border-radius: 0!important;font-size:15px;">
HTML;
echo $update ? 'Ручное обновление' : 'Ручная установка';
echo <<<HTML
							</button>
HTML;
if ($delete) {
echo <<<HTML
							<button id="deleteMod" class="btn bg-danger btn-sm btn-raised" style="border-radius: 0;font-size:15px;">Удалить модуль</button>
							<button id="selfUnistall" class="btn bg-info-800 btn-sm btn-raised" style="display: none;border-radius: 0;font-size:15px;">Ручное удаление</button>
HTML;
}
echo <<<HTML
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(function() {
				$('body').on('click', '#installButton', function() {
					$('#installButton').attr('disabled', 'disabled');
					$.post('dle_moonwalk_install.php', {action: 'install'}, function(data) {
						var data = $.parseJSON(data);
						if (data.error) {
							$('#errorStep > div > h4').text(data.head);
							$('#errorStep > div > p').html(data.error);
							$('#firstStep').hide();
							$('#errorStep').show();
							$('#installButton').attr('disabled', false);
							$('#installButton').text('Повторить установку');
							$('#selfInstall').show();
						} else {
							if (data.step == 'install') {
								$('#StepInstall').show();
							} else if (data.step == 'update') {
								$('#StepUpdate, #StepDelteOld').show();
							}
							$('#StepFiles, #StepDataBase').show();
							$('#firstStep, #errorStep, #installButton, #selfInstall').hide();
							$('#adminLink, #installStep').show();
						}
					});
				});
				$('body').on('click', '#installDb', function() {
					$('#installDb').attr('disabled', 'disabled');
					$.post('dle_moonwalk_install.php', {action: 'installDb'}, function(data) {
						var data = $.parseJSON(data);
						if (data.step == 'create') {
							$('#installDb').replaceWith('<span class="btn bg-success btn-sm btn-raised" style="border-radius: 0!important;line-height: 1.5384616!important;">Таблица успешно создана</span>');
						}
					});
				});
				$('body').on('click', '#deleteDb', function() {
					$('#deleteDb').attr('disabled', 'disabled');
					$.post('dle_moonwalk_install.php', {action: 'deleteDb'}, function(data) {
						var data = $.parseJSON(data);
						if (data.step == 'delete') {
							$('#deleteDb').replaceWith('<span class="btn bg-success btn-sm btn-raised" style="border-radius: 0!important;line-height: 1.5384616!important;">Таблица успешно удалена</span>');
						}
					});
				});
				$('body').on('click', '#deleteMod', function() {
					$('#deleteMod').attr('disabled', 'disabled');
					$.post('dle_moonwalk_install.php', {action: 'delete'}, function(data) {
						var data = $.parseJSON(data);
						if (data.error) {
							$('#errorStep > div > h4').text(data.head);
							$('#errorStep > div > p').html(data.error);
							$('#errorStep, #firstStep, #installStep, #itSelfManual, #installButton, #selfInstall').hide();
							$('#errorStep').show();
							$('#deleteMod').attr('disabled', false);
							$('#deleteMod').text('Повторить удаление');
							$('#selfUnistall').show();
						} else if (data.delete == 'ok') {
							window.location.href = '{$config['http_home_url']}';
						}
					});
				});
				$('body').on('click', '#selfUnistall', function() {
					$('#deleteMod').attr('disabled', 'disabled');
					$('#errorStep, #firstStep, #installStep, #itSelfManual, #installButton').hide();
					$('#itSelfUnistall').show();
				});
				$('body').on('click', '#selfInstall', function() {
					$('#installButton').attr('disabled', 'disabled');
					$('#errorStep, #firstStep, #installStep').hide();
					$('#itSelfManual').show();
				});
			});
		</script>
	</body>
</html>
HTML;
?>