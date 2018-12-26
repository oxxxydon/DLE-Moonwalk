<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   2.0.0
 * @link      https://lazydev.pro/
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

function removeVersion($dir)
{
	$files = array_diff(scandir($dir), ['.', '..']);
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? removeVersion("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function dirEmpty($dir)
{
	$handle = opendir($dir);
	while (false !== ($entry = readdir($handle))) {
		if ($entry != '.' && $entry != '..') {
			return FALSE;
		}
	}
	return TRUE;
}

$installDataBase = [];
$installDataBase[] = "INSERT INTO `{PREFIX}_admin_sections` (`name`, `title`, `descr`, `icon`, `allow_groups`) VALUES ('dle_moonwalk', 'DLE Moonwalk', 'Работа с Moonwalk', '', '1')";
$installDataBase[] = "CREATE TABLE IF NOT EXISTS `{PREFIX}_dle_moonwalk` (
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

$deleteDataBase = [];
$deleteDataBase[] = "DELETE FROM `{PREFIX}_admin_sections` WHERE `name`='dle_moonwalk'";
$deleteDataBase[] = "DROP TABLE IF EXISTS `{PREFIX}_dle_moonwalk`;";

$error = '';
@chmod(ENGINE_DIR . '/inc/addnews.php', 0777);
@chmod(ENGINE_DIR . '/inc/editnews.php', 0777);

if (!is_writable(ENGINE_DIR . '/inc/addnews.php') || !is_writable(ENGINE_DIR . '/inc/editnews.php')) {
@chmod(ENGINE_DIR . '/inc/addnews.php', 0755);
@chmod(ENGINE_DIR . '/inc/editnews.php', 0755);
    if (!is_writable(ENGINE_DIR . '/inc/addnews.php') || !is_writable(ENGINE_DIR . '/inc/editnews.php')) {
$error = <<<HTML
<div id="errorStep">
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Ошибка!</h4>
        <p>Файлы <b>/engine/inc/addnews.php</b> и <b>/engine/inc/editnews.php</b> не доступны для записи.<br>Пожалуйста выставьте права на запись - 0755.</p>
    </div>
</div>
HTML;
    }
}

if ($action == 'install') {
    if ($error) {
        echo json_encode(['error' => 'true', 'message' => 'Файлы /engine/inc/addnews.php и /engine/inc/editnews.php не доступны для записи.', 'title' => 'Ошибка установки']);
        exit;
    }
    
    if (!is_writable(ROOT_DIR . '/templates/' . $config['skin'])) {
		echo json_encode(['error' => 'true', 'message' => 'Папка /templates/' . $config['skin'] . ' не доступна для записи.', 'title' => 'Ошибка установки']);
		exit;
	}
    
    file_put_contents(ENGINE_DIR . '/inc/addnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/addnews.php')));
	file_put_contents(ENGINE_DIR . '/inc/editnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/editnews.php')));
    
    file_put_contents(ENGINE_DIR . '/inc/addnews.php', str_replace('$categories_list = CategoryNewsSelection( 0, 0 );', '$categories_list = CategoryNewsSelection( 0, 0 );' . PHP_EOL . 'include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', file_get_contents(ENGINE_DIR . '/inc/addnews.php')));
	file_put_contents(ENGINE_DIR . '/inc/editnews.php', str_replace('$categories_list = CategoryNewsSelection( $cat_list, 0 );', '$categories_list = CategoryNewsSelection( $cat_list, 0 );'  . PHP_EOL . 'include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', file_get_contents(ENGINE_DIR . '/inc/editnews.php')));
    
	$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $deleteDataBase[0]));
	foreach ($installDataBase as $dataQuery) {
		$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataQuery));
	}
    
    rename(ENGINE_DIR . '/dle_moonwalk/tpl', ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk');
    
	echo json_encode(['message' => 'Установка успешно завершена.', 'title' => 'Успех']);
    unlink(ROOT_DIR . '/dle_moonwalk_install.php');
	exit;
}

if ($action == 'update') {
    if ($error) {
        echo json_encode(['error' => 'true', 'message' => 'Файлы /engine/inc/addnews.php и /engine/inc/editnews.php не доступны для записи.', 'title' => 'Ошибка обновления']);
        exit;
    }
    
    file_put_contents(ENGINE_DIR . '/inc/addnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/addnews.php')));
	file_put_contents(ENGINE_DIR . '/inc/editnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/editnews.php')));
    
    file_put_contents(ENGINE_DIR . '/inc/addnews.php', str_replace('$categories_list = CategoryNewsSelection( 0, 0 );', '$categories_list = CategoryNewsSelection( 0, 0 );' . PHP_EOL . 'include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', file_get_contents(ENGINE_DIR . '/inc/addnews.php')));
	file_put_contents(ENGINE_DIR . '/inc/editnews.php', str_replace('$categories_list = CategoryNewsSelection( $cat_list, 0 );', '$categories_list = CategoryNewsSelection( $cat_list, 0 );'  . PHP_EOL . 'include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', file_get_contents(ENGINE_DIR . '/inc/editnews.php')));
    
	echo json_encode(['message' => 'Обновление успешно завершено.', 'title' => 'Успех']);
    unlink(ROOT_DIR . '/dle_moonwalk_install.php');
	exit;
}

if ($action == 'delete') {
    if ($error) {
        echo json_encode(['error' => 'true', 'message' => 'Файлы /engine/inc/addnews.php и /engine/inc/editnews.php не доступны для записи.', 'title' => 'Ошибка удаления']);
        exit;
    }
    
    if (!is_writable(ROOT_DIR . '/templates/' . $config['skin'])) {
		echo json_encode(['error' => 'true', 'message' => 'Папка /templates/' . $config['skin'] . ' не доступна для записи.', 'title' => 'Ошибка удаления']);
		exit;
	}
    
    file_put_contents(ENGINE_DIR . '/inc/addnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/addnews.php')));
	file_put_contents(ENGINE_DIR . '/inc/editnews.php', str_replace('include ENGINE_DIR . \'/dle_moonwalk/inc/dle_moonwalk.php\';', '', file_get_contents(ENGINE_DIR . '/inc/editnews.php')));
    
    removeVersion(ENGINE_DIR . '/dle_moonwalk');
	if (dirEmpty(ENGINE_DIR . '/dle_moonwalk')) {
		removeVersion(ENGINE_DIR . '/dle_moonwalk');
	}
    
	removeVersion(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk');
	if (dirEmpty(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk')) {
		removeVersion(ROOT_DIR . '/templates/' . $config['skin'] . '/dle_moonwalk');
	}
    
    if (file_exists(ROOT_DIR . '/dle_moonwalk_cron.php')) {
		unlink(ROOT_DIR . '/dle_moonwalk_cron.php');
	}
    if (file_exists(ENGINE_DIR . '/inc/dle_moonwalk.php')) {
		unlink(ENGINE_DIR . '/inc/dle_moonwalk.php');
	}
    foreach ($deleteDataBase as $dataQuery) {
		$db->query(str_replace(['{PREFIX}', '{CHAR}'], [PREFIX, $charset], $dataQuery));
	}
    
	echo json_encode(['message' => 'Удаление успешно завершено.', 'title' => 'Успех']);
    unlink(ROOT_DIR . '/dle_moonwalk_install.php');
	exit;
}

$installHtml = [];
if (!$error) {
$installHtml[] =<<<HTML
Добро пожаловать в мастер установки DLE Moonwalk. Данный мастер поможет вам установить скрипт всего за несколько минут. После установки мы настоятельно рекомендуем Вам ознакомиться с <a href="https://moonwalk.lazydev.pro" target="_blank">документацией</a> по работе со скриптом.<br><br>
<span class="text-danger">Внимание: при установке скрипта создается таблица в базе данных и правятся файлы движка!</span><br><br>
Приятной Вам работы,<br><br>
LazyDev
HTML;

$installHtml[] =<<<HTML
<ul class="step-text">
    <li id="StepInstall">
        <h5>Начало установки.</h5>
    </li>
    <li id="StepFiles">
        <h5>Изменения в файлах движка успешно сделаны.</h5>
    </li>
    <li id="StepDataBase">
        <h5>Таблица в базе данных успешно создана.</h5>
    </li>
</ul>
HTML;
}

$updateButton = $deleteButton = '';
if (file_exists(ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php')) {
    $deleteButton = '<button id="deleteButton" class="btn bg-danger btn-sm btn-raised" style="border-radius: 0;font-size:15px;">Удалить модуль</button>';
}
if (file_exists(ENGINE_DIR . '/dle_moonwalk/ajax/dle_moonwalk.php')) {
    $updateButton = '<button id="updateButton" class="btn bg-info-800 btn-sm btn-raised position-left" style="border-radius: 0!important;font-size:15px;">Обновить модуль</button>';
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
        <link href="engine/skins/fonts/fontawesome/styles.min.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="engine/skins/stylesheets/application.css" media="screen" rel="stylesheet" type="text/css" />
		<script src="engine/skins/javascripts/application.js"></script>
        <script>
            <!--
                var dle_act_lang   = ["Да", "Нет", "Ввод", "Отмена", "Сохранить", "Удалить", "Загрузка. Пожалуйста, подождите..."];
                var cal_language   = {en:{months:[],dayOfWeek:[]}};
                var filedefaulttext= '';
                var filebtntext    = '';
            //-->
        </script>
        <style>
            .step-text {list-style: none;padding-left: 0;counter-reset: toc1;}.step-text>li {position: relative;padding-left: 64px;margin-bottom: 48px;font-weight: 300;}.step-text>li::before {content: counter(toc1, decimal-leading-zero);counter-increment: toc1;position: absolute;left: 0;top: 0;color: #929daf;font-size: 24px;line-height: 44px;text-align: center;width: 46px;height: 46px;background-color: #f9fafb;border-radius: 10rem;}.step-text h5 {font-size: 15px;padding-top: 5px;}
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
                            {$error}
                            {$installHtml[0]}
                        </div>
                        <div class="panel-footer">
                            <a href="{$config['http_home_url']}{$config['admin_path']}?mod=dle_moonwalk" id="adminLink" class="btn bg-teal btn-sm btn-raised position-left" style="display: none;border-radius: 0!important;font-size:15px;">Перейти в админ панель модуля</a>
                            <button id="installButton" class="btn bg-slate btn-sm btn-raised position-left" style="border-radius: 0!important;font-size:15px;">Установить модуль</button>
                            {$updateButton}
                            {$deleteButton}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        $(function() {
            function doPost(action) {
                $.post('dle_moonwalk_install.php', {action: action}, function(data) {
                    var data = $.parseJSON(data);
                    if (data.error) {
                        DLEalert(data.message, data.title);
                    } else {
                        Growl.info({
                            title: data.title,
                            text: data.message
                        });
                        if (action == 'delete') {
                            window.location.href = '{$config['http_home_url']}';
                        }
                    }
                });
            }
            function showAdmin() {
                if ($('#installButton')) {
                    $('#installButton').hide();
                }
                if ($('#updateButton')) {
                    $('#updateButton').hide();
                }
                if ($('#deleteButton')) {
                    $('#deleteButton').hide();
                }
                $('#adminLink').show();
            }
            $('body').on('click', '#installButton', function(e) {
                e.preventDefault();
                DLEconfirm('Подтвердите установку модуля.', 'Подтверждение установки', function() {
                    doPost('install');
                    showAdmin();
                });
                return false;
            });
            $('body').on('click', '#updateButton', function(e) {
                e.preventDefault();
                DLEconfirm('Подтвердите обновление модуля.', 'Подтверждение обновления', function() {
                    doPost('update');
                    showAdmin();
                });
                return false;
            });
            $('body').on('click', '#deleteButton', function(e) {
                e.preventDefault();
                DLEconfirm('Подтвердите удаление модуля.', 'Подтверждение удаления', function() {
                    doPost('delete');
                });
                return false;
            });
        });
        </script>
	</body>
</html>
HTML;
?>