<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   2.0.0
 * @link      https://lazydev.pro
 */
 
if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

include ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';
include ENGINE_DIR . '/dle_moonwalk/language/dle_moonwalk_admin_lang.lng';

if ($dle_moonwalk_config['main']['api_token'] != '') {
	$fieldChoose = ['title' => $dle_moonwalk_admin_lang[101]];
	$configField = '';
	
	if ($dle_moonwalk_config['main']['id_kinopoisk'] != '') {
		$fieldChoose['id_kinopoisk'] = $dle_moonwalk_admin_lang[102];
		$configField .= 'configField.id_kinopoisk = ' . "'" . $dle_moonwalk_config['main']['id_kinopoisk'] . "';";
	}
	
	if ($dle_moonwalk_config['main']['id_worldart'] != '') {
		$fieldChoose['id_worldart'] = $dle_moonwalk_admin_lang[103];
		$configField .= 'configField.id_worldart = ' . "'" . $dle_moonwalk_config['main']['id_worldart'] . "';";
	}
	
	foreach ($fieldChoose as $key => $val) {
		$selectField = '';
		if ($key == $dle_moonwalk_config['main']['field_search']) {
			$selectField = ' selected';
		}
		$optionChoose .= "<option value=\"{$key}\"{$selectField}>{$val}</option>";
	}

$showSearch = <<<HTML
<div class="form-group">
	<label class="control-label col-sm-2">{$dle_moonwalk_admin_lang['name']}:</label>
	<div class="col-sm-10">
		<div class="DleMoonwalk-search">
			<select name="optionChoose" class="uniform">
				{$optionChoose}
			</select>

			<button type="button" onclick="parseDleMoonwalk('{$dle_login_hash}')" class="btn bg-teal btn-sm btn-raised">{$dle_moonwalk_admin_lang[122]}</button>
			<button type="button" id="DleMoonwalk-search-clear" class="btn bg-danger btn-sm btn-raised" style="display:none;">{$dle_moonwalk_admin_lang[123]}</button>
		</div>
	</div>
</div>
<div class="form-group">
	<div class="col-sm-12">
		<div class="DleMoonwalk-search">
			<div id="DleMoonwalk-search-notfound" style="display: none;">
				{$dle_moonwalk_admin_lang[96]}
			</div>
			<div id="DleMoonwalk-search-results" style="display:none;">
				<div class="DleMoonwalk-search-table">
					<div class="DleMoonwalk-search-thead">
						<div class="DleMoonwalk-search-tr">
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_admin_lang[104]}</div>
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_admin_lang[105]}</div>
							<div class="DleMoonwalk-search-th">{$dle_moonwalk_admin_lang[106]}</div>
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_admin_lang[107]}</div>
							<div class="DleMoonwalk-search-th" style="width: 40px;">{$dle_moonwalk_admin_lang[108]}</div>
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
							<h4 class="DleMoonwalk-modal-title" id="previewPlayerModalLabel">{$dle_moonwalk_admin_lang[124]}</h4>
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
	</div>
</div>
HTML;

$showSearch = str_replace(PHP_EOL, '', $showSearch);
$showSearch = preg_replace('/\s+/', ' ', $showSearch);
$showSearch = addslashes($showSearch);

echo <<<HTML
<link type="text/css" href="/engine/dle_moonwalk/inc/css/dle_moonwalk.css" rel="stylesheet">
<script>
var configField = {}; {$configField}
</script>
<script type="text/javascript" src="/engine/dle_moonwalk/inc/js/modal.min.js"></script>
<script type="text/javascript" src="/engine/dle_moonwalk/inc/js/dle_moonwalk.js"></script>
<script>
$(function() {
    var s = $('.form-group')[0];
    $(s).after($("$showSearch"));
    $('.uniform').selectpicker();
});
</script>
HTML;
}
