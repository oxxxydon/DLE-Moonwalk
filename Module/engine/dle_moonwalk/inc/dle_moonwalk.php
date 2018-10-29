<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   1.1.2
 * @link      https://lazydev.pro
 */
 
if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

include ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';
include ENGINE_DIR . '/dle_moonwalk/language/dle_moonwalk.lng';

if ($dle_moonwalk_config['main']['api_token'] != '') {
	$fieldChoose = ['title' => $dle_moonwalk_lang[122]];
	$configField = '';
	
	if ($dle_moonwalk_config['main']['id_kinopoisk'] != '') {
		$fieldChoose['id_kinopoisk'] = $dle_moonwalk_lang[123];
		$configField .= 'configField.id_kinopoisk = ' . "'" . $dle_moonwalk_config['main']['id_kinopoisk'] . "';";
	}
	
	if ($dle_moonwalk_config['main']['id_worldart'] != '') {
		$fieldChoose['id_worldart'] = $dle_moonwalk_lang[124];
		$configField .= 'configField.id_worldart = ' . "'" . $dle_moonwalk_config['main']['id_worldart'] . "';";
	}
	
	if ($dle_moonwalk_config['main']['id_pornolab'] != '') {
		$fieldChoose['id_pornolab'] = $dle_moonwalk_lang[125];
		$configField .= 'configField.id_pornolab = ' . "'" . $dle_moonwalk_config['main']['id_pornolab'] . "';";
	}
	
	foreach ($fieldChoose as $key => $val) {
		if ($key == 'id_pornolab') {
$optionChoose .= <<<HTML
<option value="{$key}" style="display:none;">{$val}</option>
HTML;
		} else {
$optionChoose .= <<<HTML
<option value="{$key}">{$val}</option>
HTML;
		}
	}
	
	$optionChooseDb = '';
	if ($fieldChoose['id_pornolab']) {
$optionChooseDb = <<<HTML
<option value="moonwalk">{$dle_moonwalk_lang[128]}</option>
<option value="pornodb">{$dle_moonwalk_lang[129]}</option>
HTML;
	}
	
echo <<<HTML
	</div>
</div>
<link type="text/css" href="/engine/dle_moonwalk/inc/css/dle_moonwalk.css" rel="stylesheet">
<script>
var configField = {}; {$configField}
</script>
<script type="text/javascript" src="/engine/dle_moonwalk/inc/js/modal.min.js"></script>
<script type="text/javascript" src="/engine/dle_moonwalk/inc/js/dle_moonwalk.js"></script>
<div class="form-group">
	<label class="control-label col-lg-2">{$dle_moonwalk_lang['name']}: </label>
	<div class="col-lg-10">
		<div id="DleMoonwalk-search">
			<select name="optionChoose" class="selectDleMoonwalk">
				{$optionChoose}
			</select>
HTML;
if ($optionChooseDb != '') {
echo <<<HTML
			<select name="optionChooseDb" class="selectDleMoonwalk selectDleMoonwalkDb">
				{$optionChooseDb}
			</select>
HTML;
}
echo <<<HTML
			<button type="button" onclick="parseDleMoonwalk('{$dle_login_hash}')" class="buttonDleMoonwalk bg-teal marginDleMoonwalk">Поиск в базе</button>
			<button type="button" class="DleMoonwalk-search-clear">Очистить</button>
			<div id="DleMoonwalk-search-notfound" style="display: none;">
				{$dle_moonwalk_lang[139]}
			</div>
			<div id="DleMoonwalk-search-results" style="display:none;">
				<div class="DleMoonwalk-search-table">
					<div class="DleMoonwalk-search-thead">
						<div class="DleMoonwalk-search-tr">
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_lang[134]}</div>
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_lang[135]}</div>
							<div class="DleMoonwalk-search-th">{$dle_moonwalk_lang[136]}</div>
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_lang[137]}</div>
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_lang[138]}</div>
							<div class="DleMoonwalk-search-th" style="width: 142px;text-align:center;"><i class="fa fa-cogs"></i></div>
						</div>
					</div>
					<div class="DleMoonwalk-search-tbody"></div>
				</div>
			</div>
			<div class="DleMoonwalk-modal fade" id="previewPlayerModal" tabindex="-1" role="dialog" aria-labelledby="previewPlayerModalLabel" aria-hidden="true">
				<div class="DleMoonwalk-modal-dialog">
					<div class="DleMoonwalk-modal-content">
						<div class="DleMoonwalk-modal-header">
							<button type="button" class="DleMoonwalk-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
							<h4 class="DleMoonwalk-modal-title" id="previewPlayerModalLabel">Предпросмотр видео</h4>
						</div>
						<div class="DleMoonwalk-modal-body">
							<div class="DleMoonwalk-preview-player">
								<iframe id="DleMoonwalk-preview" src="" frameborder="0" allowfullscreen></iframe>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
HTML;
}