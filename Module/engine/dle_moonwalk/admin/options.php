<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   2.0.0
 * @link      https://lazydev.pro/
 */

if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

$all_xfield = xfieldsload();
$xfield = ['-' => '-'];
$allField = ['p.title' => $dle_moonwalk_admin_lang[5], 'p.short_story' => $dle_moonwalk_admin_lang[6], 'p.full_story' => $dle_moonwalk_admin_lang[7]];
for ($i = 0; $i < count($all_xfield); $i++) {
	$xfield[$all_xfield[$i][0]] = $all_xfield[$i][1];
	$allField[$all_xfield[$i][0]] = $all_xfield[$i][1];
}

$disable_category = CategoryNewsSelection(($dle_moonwalk_config['main']['disable_category'] ?: 0), 0, false);
$delete_category = CategoryNewsSelection(($dle_moonwalk_config['main']['delete_category'] ?: 0), 0, false);

$voice = [52 => '2x2', 16 => 'Agatha Studdio', 7 => 'Alexfilm', 89 => 'AlFair Studio', 40 => 'Alt Pro', 88 => 'AMC', 32 => 'Amedia', 36 => 'Ancord', 49 => 'AniDUB', 48 => 'AniLibria', 105 => 'Animedia', 114 => 'AveBrasil', 102 => 'AvePremier', 113 => 'AveTurk', 34 => 'AXN Sci-fi', 9 => 'BaibaKo', 17 => 'Coldfilm', 87 => 'CTC', 110 => 'D1', 73 => 'datynet', 72 => 'den904', 64 => 'Discovery', 42 => 'Diva Universal', 60 => 'DreamRecords', 22 => 'Filiza Studio', 28 => 'Flux-Team', 99 => 'FocusStudio', 18 => 'Fox', 41 => 'F-TRAIN', 81 => 'Gears Media', 61 => 'GladiolusTV', 109 => 'Good People', 30 => 'GREEN TEA', 8 => 'HamsterStudio ', 112 => 'HDrezka Studio', 80 => 'HTB', 57 => 'IdeaFilm', 116 => 'JAM', 12 => 'Jaskier', 24 => 'Jetvis Studio', 13 => 'Jimmy J.', 100 => 'JWA Project', 104 => 'KANSAI', 98 => 'Levelin', 79 => 'Lord32x', 2 => 'LostFilm', 26 => 'Lw13pro', 51 => 'MC Entertainment', 75 => 'napaBo3uk', 58 => 'Narkom Pro', 3 => 'Newstudio', 59 => 'Nice-Media', 4 => 'Novafilm', 78 => 'Novamedia', 96 => 'OMSKBIRD records', 97 => 'Onibaku', 10 => 'Ozz', 90 => 'Paramount Comedy', 74 => 'PashaUp', 63 => 'Prichudiki', 19 => 'ProjektorShow', 77 => 'R.A.I.M', 94 => 'SDI Media', 47 => 'SET Russia', 95 => 'SHIZA Project', 115 => 'SoftBox', 43 => 'Sony Sci-Fi', 85 => 'Sony Turbo', 101 => 'STEPonee', 108 => 'StudioBand', 93 => 'Sunshine Studio', 15 => 'To4ka', 86 => 'Tycoon', 45 => 'Universal Russia', 29 => 'Victory-Films', 14 => 'ViruseProject', 62 => 'VO-production', 37 => 'xaros', 71 => 'xixidok', 68 => 'Zamez', 53 => 'АРК ТВ', 70 => 'Гаврилов', 82 => 'Гоблин', 25 => 'Дасевич', 66 => 'двухголосый закадровый', 21 => 'Дубляж', 76 => 'Есарев', 69 => 'Живов', 92 => 'Кравец', 11 => 'Кубик в Кубе', 5 => 'Кураж-бамбей', 27 => 'Матвеев', 67 => 'многоголосый закадровый', 50 => 'Невафильм', 44 => 'Несмертельное оружие', 111 => 'Не требуется', 65 => 'одноголосый закадровый', 83 => 'Первый канал', 6 => 'Сербин', 56 => 'Студия Райдо', 20 => 'Субтитры', 107 => 'Субтитры PhysKids', 23 => 'Сыендук', 106 => 'ТВ-3', 103 => 'Украинский', 46 => 'Шадинский'];

echoheader('<b>' . $dle_moonwalk_admin_lang['name'] . '</b>', $dle_moonwalk_admin_lang['header_title'] . $dle_moonwalk_admin_lang['name']);

echo <<<HTML
<style>
.chosen-container-single .chosen-single:before{
	background: transparent!important;
}
.checkbox {
	display: inline-block;
	padding: 5px 5px 5px 56px;
	margin: 0;
	position: relative;
	cursor: pointer;
}
.checkbox input {
	position: absolute;
	opacity: 0;
	cursor: inherit;
}
.checkbox span {
	display: inline-block;
	font: normal 12px/16px Arial;
	padding: 4px 0;
}
.checkbox span:before,
.checkbox span:after {
	content: '';
	position: absolute;
	top: 50%;
	transition: .3s;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
}
.checkbox span:before {
	left: 0;
	height: 24px;
	margin-top: -12px;
	width: 46px;
	border-radius: 12px;
	background: #ddd;
	box-shadow: inset 0 1px 3px rgba(0,0,0,.4);
}
.checkbox span:after {
	left: 1px;
	height: 22px;
	width: 22px;
	margin-top: -11px;
	background: #fff;
	border-radius: 50%;
	box-shadow: 0 1px 2px rgba(0,0,0,.3);
}
.checkbox input:checked + span:before {
	background-color: #4caf50;
}
.checkbox input:checked + span:after {
	left: 23px;
}
.checkbox input:focus + span:before {
	box-shadow: 0 0 0 3px rgba(50,150,255,.2);
}
.checkbox input:disabled + span {
	opacity: .35;
}
.checkbox input:disabled + span:before{
	background: #ddd;
}
.chosen-container .chosen-results li.result-selected {
	color: #fff;
	background: #2c82c9;
}
.chosen-container-single .chosen-single {
    text-transform: none!important;
}
.chosen-container-active.chosen-with-drop .chosen-single {
    box-shadow: none;
}
</style>
<script>
function ChangeOption(obj, selectedOption) {
	$('#navbar-filter li').removeClass('active');
	$(obj).parent().addClass('active');
	document.getElementById('block_1').style.display = 'none';
	document.getElementById('block_2').style.display = 'none';
	document.getElementById('block_3').style.display = 'none';
	document.getElementById('block_4').style.display = 'none';
	document.getElementById('block_5').style.display = 'none';
    document.getElementById('block_6').style.display = 'none';
    document.getElementById('block_7').style.display = 'none';
	document.getElementById(selectedOption).style.display = '';

	return false;
}

function ShowHide(d) {
	if ($(d).text() === '{$dle_moonwalk_admin_lang[118]}') {
		$('#content_help').show();
		$(d).text('{$dle_moonwalk_admin_lang[119]}');
		$(d).css('border-color', '#e53935');
	} else {
		$('#content_help').hide();
		$(d).text('{$dle_moonwalk_admin_lang[118]}');
		$(d).css('border-color', '#009688');
	}
}
</script>
<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav">
			<li class="active"><a onclick="ChangeOption(this, 'block_1');" class="tip" title="{$dle_moonwalk_admin_lang[0]}"><i class="fa fa-cog"></i> {$dle_moonwalk_admin_lang[0]}</a></li>
			<li><a onclick="ChangeOption(this, 'block_2');" class="tip" title="{$dle_moonwalk_admin_lang[1]}"><i class="fa fa-file-text-o"></i> {$dle_moonwalk_admin_lang[1]}</a></li>
			<li><a onclick="ChangeOption(this, 'block_3');" class="tip" title="{$dle_moonwalk_admin_lang[2]}"><i class="fa fa-television"></i> {$dle_moonwalk_admin_lang[2]}</a></li>
			<li><a onclick="ChangeOption(this, 'block_4');" class="tip" title="{$dle_moonwalk_admin_lang[3]}"><i class="fa fa-film"></i> {$dle_moonwalk_admin_lang[3]}</a></li>
			<li><a onclick="ChangeOption(this, 'block_5');" class="tip" title="{$dle_moonwalk_admin_lang[4]}"><i class="fa fa-tasks"></i> {$dle_moonwalk_admin_lang[4]}</a></li>
            <li><a onclick="ChangeOption(this, 'block_6');" class="tip" title="{$dle_moonwalk_admin_lang[149]}"><i class="fa fa-picture-o"></i> {$dle_moonwalk_admin_lang[149]}</a></li>
            <li><a onclick="ChangeOption(this, 'block_7');" class="tip" title="{$dle_moonwalk_admin_lang[170]}"><i class="fa fa-th-list"></i> {$dle_moonwalk_admin_lang[170]}</a></li>
		</ul>
	</div>
</div>

<form action="" method="post" class="systemsettings">
	<div id="block_1" class="panel panel-flat">
		<div class="panel-body" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[0]}</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showLazyRow($dle_moonwalk_admin_lang[8], $dle_moonwalk_admin_lang[9], showLazyInput(['main[api_token]', 'text', $dle_moonwalk_config['main']['api_token']]));
showLazyRow($dle_moonwalk_admin_lang[10], $dle_moonwalk_admin_lang[11], makeLazyCheckBox('main[ssl]', $dle_moonwalk_config['main']['ssl'], ($dle_moonwalk_config['main']['domain'] != '' ? true : false)));
showLazyRow($dle_moonwalk_admin_lang[12], $dle_moonwalk_admin_lang[13], showLazyInput(['main[domain]', 'text', $dle_moonwalk_config['main']['domain'], ($dle_moonwalk_config['main']['ssl'] ? true : false)]));
showLazyRow($dle_moonwalk_admin_lang[15], $dle_moonwalk_admin_lang[16], lazySelect(['main[id_kinopoisk]', $xfield, true, $dle_moonwalk_config['main']['id_kinopoisk'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[17], $dle_moonwalk_admin_lang[18], lazySelect(['main[id_worldart]', $xfield, true, $dle_moonwalk_config['main']['id_worldart'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[138], $dle_moonwalk_admin_lang[139], makeLazyCheckBox('main[geo_block]', $dle_moonwalk_config['main']['geo_block']));
showLazyRow($dle_moonwalk_admin_lang[20], $dle_moonwalk_admin_lang[21], showLazySelect('main[disable_category][]', $disable_category, $dle_moonwalk_admin_lang[19]));
showLazyRow($dle_moonwalk_admin_lang[147], $dle_moonwalk_admin_lang[148], showLazySelect('main[delete_category][]', $delete_category, $dle_moonwalk_admin_lang[19]));
showLazyRow($dle_moonwalk_admin_lang[22], $dle_moonwalk_admin_lang[23], lazySelect(['main[voice][]', $voice, true, $dle_moonwalk_config['main']['voice'], true, false, $dle_moonwalk_admin_lang[24]]));
showLazyRow($dle_moonwalk_admin_lang[128], $dle_moonwalk_admin_lang[129], makeLazyCheckBox('main[update_date]', $dle_moonwalk_config['main']['update_date']));
showLazyRow($dle_moonwalk_admin_lang[132], $dle_moonwalk_admin_lang[95], makeLazyCheckBox('main[update_news_moonwalk]', $dle_moonwalk_config['main']['update_news_moonwalk']));
showLazyRow($dle_moonwalk_admin_lang[130], $dle_moonwalk_admin_lang[131], lazySelectDisable(['main[field_search]', ['title' => $dle_moonwalk_admin_lang[101], 'id_kinopoisk' => $dle_moonwalk_admin_lang[102], 'id_worldart' => $dle_moonwalk_admin_lang[103]], true, $dle_moonwalk_config['main']['field_search'], false, false], [1, $dle_moonwalk_config['main']['id_kinopoisk'] ?: false, $dle_moonwalk_config['main']['id_worldart'] ?: false]));
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="block_2" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[1]}</div>
		<div class="alert alert-component text-size-small" style="margin-bottom:0px!important;box-shadow:none!important;">
			{$dle_moonwalk_admin_lang[112]}
			<br><br>
			<button style="border-radius: 0;background: #fff;border: 1px solid #009688;color: #000;width: 100%;" onclick="ShowHide(this); return false;" class="btn bg-teal btn-raised btn-sm">{$dle_moonwalk_admin_lang[113]}</button>
			
			<div id="content_help" style="display: none;">
				<table class="table table-normal table-hover">
					<thead>
						<tr>
							<td>{$dle_moonwalk_admin_lang[114]}</td>
							<td>{$dle_moonwalk_admin_lang[115]}</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><b>[tag-title_ru]{title_ru}[/tag-title_ru]</b></td>
							<td>{$dle_moonwalk_admin_lang[25]}</td>
						</tr>
						<tr>
							<td><b>[tag-title_en]{title_en}[/tag-title_en]</b></td>
							<td>{$dle_moonwalk_admin_lang[26]}</td>
						</tr>
						<tr>
							<td><b>[tag-not-title_ru]{$dle_moonwalk_admin_lang[140]}[/tag-not-title_ru]</b></td>
							<td>{$dle_moonwalk_admin_lang[143]}</td>
						</tr>
						<tr>
							<td><b>[tag-not-title_en]{$dle_moonwalk_admin_lang[140]}[/tag-not-title_en]</b></td>
							<td>{$dle_moonwalk_admin_lang[144]}</td>
						</tr>
						<tr>
							<td><b>[tag-video]{video}[/tag-video]</b></td>
							<td>{$dle_moonwalk_admin_lang[27]}</td>
						</tr>
						<tr>
							<td><b>[tag-trailer]{trailer}[/tag-trailer]</b></td>
							<td>{$dle_moonwalk_admin_lang[28]}</td>
						</tr>
                        <tr>
							<td><b>[tag-poster]{poster}[/tag-poster]</b></td>
							<td>{$dle_moonwalk_admin_lang[164]}</td>
						</tr>
						<tr>
							<td><b>[tag-description]{description}[/tag-description]</b></td>
							<td>{$dle_moonwalk_admin_lang[29]}</td>
						</tr>
						<tr>
							<td><b>[tag-quality]{quality}[/tag-quality]</b></td>
							<td>{$dle_moonwalk_admin_lang[30]}</td>
						</tr>
						<tr>
							<td><b>[tag-year]{year}[/tag-year]</b></td>
							<td>{$dle_moonwalk_admin_lang[31]}</td>
						</tr>
						<tr>
							<td><b>[tag-translator]{translator}[/tag-translator]</b></td>
							<td>{$dle_moonwalk_admin_lang[32]}</td>
						</tr>
                        <tr>
							<td><b>[tag-translator-all]{translator-all}[/tag-translator-all]</b></td>
							<td>{$dle_moonwalk_admin_lang[163]}</td>
						</tr>
						<tr>
							<td><b>[tag-countries]{countries}[/tag-countries]</b></td>
							<td>{$dle_moonwalk_admin_lang[33]}</td>
						</tr>
						<tr>
							<td><b>[tag-genres]{genres}[/tag-genres]</b></td>
							<td>{$dle_moonwalk_admin_lang[34]}</td>
						</tr>
						<tr>
							<td><b>[tag-duration]{duration}[/tag-duration]</b></td>
							<td>{$dle_moonwalk_admin_lang[35]}</td>
						</tr>
						<tr>
							<td><b>[tag-season]{season}[/tag-season]</b></td>
							<td>{$dle_moonwalk_admin_lang[36]}</td>
						</tr>
						<tr>
							<td><b>[tag-season-format-1]{season-format-1}[/tag-season-format-1]</b></td>
							<td>{$dle_moonwalk_admin_lang[37]}</td>
						</tr>
						<tr>
							<td><b>[tag-season-format-2]{season-format-2}[/tag-season-format-2]</b></td>
							<td>{$dle_moonwalk_admin_lang[38]}</td>
						</tr>
						<tr>
							<td><b>[tag-season-format-3]{season-format-3}[/tag-season-format-3]</b></td>
							<td>{$dle_moonwalk_admin_lang[39]}</td>
						</tr>
						<tr>
							<td><b>[tag-season-format-4]{season-format-4}[/tag-season-format-4]</b></td>
							<td>{$dle_moonwalk_admin_lang[116]}</td>
						</tr>
						<tr>
							<td><b>[tag-seria]{seria}[/tag-seria]</b></td>
							<td>{$dle_moonwalk_admin_lang[40]}</td>
						</tr>
						<tr>
							<td><b>[tag-seria-format-1]{seria-format-1}[/tag-seria-format-1]</b></td>
							<td>{$dle_moonwalk_admin_lang[41]}</td>
						</tr>
						<tr>
							<td><b>[tag-seria-format-2]{seria-format-2}[/tag-seria-format-2]</b></td>
							<td>{$dle_moonwalk_admin_lang[42]}</td>
						</tr>
						<tr>
							<td><b>[tag-seria-format-3]{seria-format-3}[/tag-seria-format-3]</b></td>
							<td>{$dle_moonwalk_admin_lang[43]}</td>
						</tr>
						<tr>
							<td><b>[tag-seria-format-4]{seria-format-4}[/tag-seria-format-4]</b></td>
							<td>{$dle_moonwalk_admin_lang[117]}</td>
						</tr>
						<tr>
							<td><b>[tag-directors]{directors}[/tag-directors]</b></td>
							<td>{$dle_moonwalk_admin_lang[44]}</td>
						</tr>
						<tr>
							<td><b>[tag-actors]{actors}[/tag-actors]</b></td>
							<td>{$dle_moonwalk_admin_lang[45]}</td>
						</tr>
						<tr>
							<td><b>[tag-age]{age}[/tag-age]</b></td>
							<td>{$dle_moonwalk_admin_lang[46]}</td>
						</tr>
						<tr>
							<td><b>[tag-kinopoisk_id]{kinopoisk_id}[/tag-kinopoisk_id]</b></td>
							<td>{$dle_moonwalk_admin_lang[47]}</td>
						</tr>
						<tr>
							<td><b>[tag-world_art_id]{world_art_id}[/tag-world_art_id]</b></td>
							<td>{$dle_moonwalk_admin_lang[48]}</td>
						</tr>
						<tr>
							<td><b>[tag-kinopoisk_rating]{kinopoisk_rating}[/tag-kinopoisk_rating]</b></td>
							<td>{$dle_moonwalk_admin_lang[49]}</td>
						</tr>
                        <tr>
							<td><b>[tag-kinopoisk_rating]{kinopoisk_rating-X}[/tag-kinopoisk_rating]</b></td>
							<td>{$dle_moonwalk_admin_lang[165]}</td>
						</tr>
						<tr>
							<td><b>[tag-kinopoisk_votes]{kinopoisk_votes}[/tag-kinopoisk_votes]</b></td>
							<td>{$dle_moonwalk_admin_lang[50]}</td>
						</tr>
						<tr>
							<td><b>[tag-imdb_rating]{imdb_rating}[/tag-imdb_rating]</b></td>
							<td>{$dle_moonwalk_admin_lang[51]}</td>
						</tr>
                        <tr>
							<td><b>[tag-kinopoisk_rating]{imdb_rating-X}[/tag-kinopoisk_rating]</b></td>
							<td>{$dle_moonwalk_admin_lang[166]}</td>
						</tr>
						<tr>
							<td><b>[tag-imdb_votes]{imdb_votes}[/tag-imdb_votes]</b></td>
							<td>{$dle_moonwalk_admin_lang[52]}</td>
						</tr>
						<tr>
							<td><b>[tag-block-ru]{$dle_moonwalk_admin_lang[140]}[/tag-block-ru]</b></td>
							<td>{$dle_moonwalk_admin_lang[141]}</td>
						</tr>
						<tr>
							<td><b>[tag-not-block-ru]{$dle_moonwalk_admin_lang[140]}[/tag-not-block-ru]</b></td>
							<td>{$dle_moonwalk_admin_lang[142]}</td>
						</tr>
						<tr>
							<td><b>[tag-block-ua]{$dle_moonwalk_admin_lang[140]}[/tag-block-ua]</b></td>
							<td>{$dle_moonwalk_admin_lang[145]}</td>
						</tr>
						<tr>
							<td><b>[tag-not-block-ua]{$dle_moonwalk_admin_lang[140]}[/tag-not-block-ua]</b></td>
							<td>{$dle_moonwalk_admin_lang[146]}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
foreach ($allField as $key => $value) {
	if ($key == 'p.short_story' || $key == 'p.full_story') {
		showTrInline($value, '', 'textarea', ['data[' . $key . ']', $dle_moonwalk_config['data'][$key]]);
	} else {
		showTrInline($value, '', 'input', ['data[' . $key . ']', 'text', $dle_moonwalk_config['data'][$key]]);
	}
}
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="block_3" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[78]}</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showLazyRow($dle_moonwalk_admin_lang[53], $dle_moonwalk_admin_lang[54], makeLazyCheckBox('serial[update_all_voice]', $dle_moonwalk_config['serial']['update_all_voice']));
showLazyRow($dle_moonwalk_admin_lang[55], $dle_moonwalk_admin_lang[56], makeLazyCheckBox('serial[season_by_season]', $dle_moonwalk_config['serial']['season_by_season']));
showLazyRow($dle_moonwalk_admin_lang[57], $dle_moonwalk_admin_lang[58], lazySelect(['serial[season_number]', $xfield, true, $dle_moonwalk_config['serial']['season_number'], false, ($dle_moonwalk_config['serial']['season_by_season'] ? false : true)]));
echo <<<HTML
<tr>
	<td colspan="2" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[1]}</td>
</tr>
HTML;
showLazyRow($dle_moonwalk_admin_lang[59], $dle_moonwalk_admin_lang[60], lazySelect(['serial[season_xfield]', $xfield, true, $dle_moonwalk_config['serial']['season_xfield'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[61], $dle_moonwalk_admin_lang[62], lazySelect(['serial[seria_xfield]', $xfield, true, $dle_moonwalk_config['serial']['seria_xfield'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[133], $dle_moonwalk_admin_lang[134], lazySelect(['serial[voice_xfield]', $xfield, true, $dle_moonwalk_config['serial']['voice_xfield'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[63], $dle_moonwalk_admin_lang[64], lazySelect(['serial[season_format]', [1 => $dle_moonwalk_admin_lang[65], 2 => $dle_moonwalk_admin_lang[66], 3 => $dle_moonwalk_admin_lang[67], 4 => $dle_moonwalk_admin_lang[116]], true, $dle_moonwalk_config['serial']['season_format'], false, ($dle_moonwalk_config['serial']['season_xfield'] ? false : true)]));
showLazyRow($dle_moonwalk_admin_lang[68], $dle_moonwalk_admin_lang[69], lazySelect(['serial[seria_format]', [1 => $dle_moonwalk_admin_lang[70], 2 => $dle_moonwalk_admin_lang[71], 3 => $dle_moonwalk_admin_lang[72], 4 => $dle_moonwalk_admin_lang[117]], true, $dle_moonwalk_config['serial']['seria_format'], false, ($dle_moonwalk_config['serial']['seria_xfield'] ? false : true)]));
echo <<<HTML
<tr>
	<td colspan="2" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[77]}</td>
</tr>
HTML;
showLazyRow($dle_moonwalk_admin_lang[73], $dle_moonwalk_admin_lang[74], makeLazyCheckBox('serial[change_meta]', $dle_moonwalk_config['serial']['change_meta']));
showLazyRow($dle_moonwalk_admin_lang[75], $dle_moonwalk_admin_lang[76], textareaLazyForm(['serial[meta_title]', $dle_moonwalk_config['serial']['meta_title'], false, ($dle_moonwalk_config['serial']['change_meta'] ? false : true)]));
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="block_4" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[3]}</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showLazyRow($dle_moonwalk_admin_lang[79], $dle_moonwalk_admin_lang[80], lazySelect(['movie[quality_field]', $xfield, true, $dle_moonwalk_config['movie']['quality_field'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[81], $dle_moonwalk_admin_lang[82], lazySelect(['movie[video_field]', $xfield, true, $dle_moonwalk_config['movie']['video_field'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[133], $dle_moonwalk_admin_lang[135], lazySelect(['movie[voice_field]', $xfield, true, $dle_moonwalk_config['movie']['voice_field'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[136], $dle_moonwalk_admin_lang[137], makeLazyCheckBox('movie[actual_voice]', $dle_moonwalk_config['movie']['actual_voice'], ($dle_moonwalk_config['movie']['voice_field'] != '' ? false : true)));
echo <<<HTML
<tr>
	<td colspan="2" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[77]}</td>
</tr>
HTML;
showLazyRow($dle_moonwalk_admin_lang[75], $dle_moonwalk_admin_lang[84], makeLazyCheckBox('movie[change_meta]', $dle_moonwalk_config['movie']['change_meta']));
showLazyRow($dle_moonwalk_admin_lang[84], $dle_moonwalk_admin_lang[85], textareaLazyForm(['movie[meta_title]', $dle_moonwalk_config['movie']['meta_title'], false, ($dle_moonwalk_config['movie']['change_meta'] ? false : true)]));
echo <<<HTML
<tr>
	<td colspan="2" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[169]}</td>
</tr>
HTML;
showLazyRow($dle_moonwalk_admin_lang[167], $dle_moonwalk_admin_lang[168], showLazyInput(['movie[cache_time]', 'number', $dle_moonwalk_config['movie']['cache_time'] ?: 3, false, false, 1, 30]));
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="block_5" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[4]}</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showLazyRow($dle_moonwalk_admin_lang[86], $dle_moonwalk_admin_lang[87], showLazyInput(['block[block_date]', 'number', $dle_moonwalk_config['block']['block_date'] ?: 4, false, false, 1, 10]));
showLazyRow($dle_moonwalk_admin_lang[88], $dle_moonwalk_admin_lang[89], showLazyInput(['block[block_news]', 'number', $dle_moonwalk_config['block']['block_news'] ?: 20, false, false, 1, 100]));
showLazyRow($dle_moonwalk_admin_lang[90], $dle_moonwalk_admin_lang[91], makeLazyCheckBox('block[all_data]', $dle_moonwalk_config['block']['all_data'], ($dle_moonwalk_config['block']['one_voice'] ? true : false)));
showLazyRow($dle_moonwalk_admin_lang[92], $dle_moonwalk_admin_lang[93], makeLazyCheckBox('block[one_voice]', $dle_moonwalk_config['block']['one_voice'], ($dle_moonwalk_config['block']['all_data'] ? true : false)));
showLazyRow($dle_moonwalk_admin_lang[94], $dle_moonwalk_admin_lang[95], makeLazyCheckBox('block[moonwalk_block_date]', $dle_moonwalk_config['block']['moonwalk_block_date']));
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="block_6" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[149]}</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showLazyRow($dle_moonwalk_admin_lang[150], $dle_moonwalk_admin_lang[151], makeLazyCheckBox('poster[upload]', $dle_moonwalk_config['poster']['upload']));
showLazyRow($dle_moonwalk_admin_lang[152], $dle_moonwalk_admin_lang[153], showLazyInput(['poster[size_poster]', 'text', $dle_moonwalk_config['poster']['size_poster']]));
showLazyRow($dle_moonwalk_admin_lang[154], $dle_moonwalk_admin_lang[155], showLazyInput(['poster[size_tumb]', 'text', $dle_moonwalk_config['poster']['size_tumb']]));
showLazyRow($dle_moonwalk_admin_lang[156], $dle_moonwalk_admin_lang[157], lazySelect(['poster[type_size_tumb]', [0 => $dle_moonwalk_admin_lang[158], 1 => $dle_moonwalk_admin_lang[159], 2 => $dle_moonwalk_admin_lang[160]], true, $dle_moonwalk_config['poster']['type_size_tumb'], false, false]));
showLazyRow($dle_moonwalk_admin_lang[161], $dle_moonwalk_admin_lang[162], showLazyInput(['poster[quality]', 'number', $dle_moonwalk_config['poster']['quality'] ?: 80, false, false, 1, 100]));
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="block_7" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:15px; font-weight:bold;">{$dle_moonwalk_admin_lang[170]}</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
$categoryParse = ['аниме', 'биография', 'боевик', 'вестерн', 'военный', 'детектив', 'детский', 'документальный', 'мелодрама', 'драма', 'история', 'комедия', 'криминал', 'мультфильм', 'приключения', 'реальное ТВ', 'тв шоу', 'семейный', 'спорт', 'триллер', 'ужасы', 'фантастика', 'фэнтези'];
$arrayCat = [];
$cat_info = [0 => ['name' => '-', 'id' => 0]] + $cat_info;
array_walk($categoryParse, function($value, $key) use ($cat_info, &$arrayCat, $dle_moonwalk_config) {
    $str = '';
    $valueTranslit = totranslit($value);
    
    foreach ($cat_info as $catKey => $catArr) {
        $str .= "<option value='{$catArr['id']}'";
        if ($catArr['id'] == $dle_moonwalk_config['category'][$valueTranslit]) {
            $str .= ' selected';
        }
        $str .= '>' . $catArr['name'] . '</option>';
    }
    $arrayCat[$value] = $str;
});
foreach ($arrayCat as $name => $value) {
    $nameTranslit = totranslit($name);
    showLazyRow(mb_convert_case($name, MB_CASE_TITLE, 'UTF-8'), $dle_moonwalk_admin_lang[171] . mb_convert_case($name, MB_CASE_TITLE, 'UTF-8'), showLazySelect('category['.$nameTranslit.']', $value, $dle_moonwalk_admin_lang[170], false));
}
echo <<<HTML
			</table>
		</div>
	</div>
    <button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</form>
<script>
	$(function() {
		$('select').chosen({allow_single_deselect: true, no_results_text: '{$dle_moonwalk_admin_lang[14]}', width: '300px'});
		
		$("[name='serial[change_meta]']").on('change', function() {
			if ($(this).prop('checked')) {
				$("[name='serial[meta_title]']").attr('disabled', false);
			} else {
				$("[name='serial[meta_title]']").attr('disabled', 'disabled');
			}
		});
		
		$("[name='movie[change_meta]']").on('change', function() {
			if ($(this).prop('checked')) {
				$("[name='movie[meta_title]']").attr('disabled', false);
			} else {
				$("[name='movie[meta_title]']").attr('disabled', 'disabled');
			}
		});
		
		$("[name='block[one_voice]']").on('change', function() {
			if ($(this).prop('checked')) {
				$("[name='block[all_data]']").attr('disabled', 'disabled');
			} else {
				$("[name='block[all_data]']").attr('disabled', false);
			}
		});
		
		$("[name='block[all_data]']").on('change', function() {
			if ($(this).prop('checked')) {
				$("[name='block[one_voice]']").attr('disabled', 'disabled');
			} else {
				$("[name='block[one_voice]']").attr('disabled', false);
			}
		});
		
		$("[name='main[ssl]']").on('change', function() {
			if ($(this).prop('checked')) {
				$("[name='main[domain]']").attr('disabled', 'disabled');
			} else {
				$("[name='main[domain]']").attr('disabled', false);
			}
		});
		
		$("[name='main[domain]']").on('change', function() {
			if ($(this).val() != '') {
				$("[name='main[ssl]']").attr('disabled', 'disabled');
			} else {
				$("[name='main[ssl]']").attr('disabled', false);
			}
		});
		
		$("[name='serial[season_by_season]']").on('change', function() {
			if ($(this).prop('checked')) {
				$("[name='serial[season_number]']").attr('disabled', false).trigger('chosen:updated');
			} else {
				$("[name='serial[season_number]']").attr('disabled', 'disabled').trigger('chosen:updated');
			}
		});
		
		$("[name='serial[season_xfield]']").on('change', function() {
			var data = $(this).val();
			if (data == '-') {
				$("[name='serial[season_format]']").attr('disabled', 'disabled').trigger('chosen:updated');
			} else {
				$("[name='serial[season_format]']").attr('disabled', false).trigger('chosen:updated');
			}
		});
		
		$("[name='serial[seria_xfield]']").on('change', function() {
			var data = $(this).val();
			if (data == '-') {
				$("[name='serial[seria_format]']").attr('disabled', 'disabled').trigger('chosen:updated');
			} else {
				$("[name='serial[seria_format]']").attr('disabled', false).trigger('chosen:updated');
			}
		});
		
		$("[name='main[id_kinopoisk]']").on('change', function() {
			var data = $(this).val();
			if (data == '-') {
				$("[name='main[field_search]'] option[value=id_kinopoisk]").attr('disabled', 'disabled').trigger('chosen:updated');
			} else {
				$("[name='main[field_search]'] option[value=id_kinopoisk]").attr('disabled', false).trigger('chosen:updated');
			}
		});
		
		$("[name='main[id_worldart]']").on('change', function() {
			var data = $(this).val();
			if (data == '-') {
				$("[name='main[field_search]'] option[value=id_worldart]").attr('disabled', 'disabled').trigger('chosen:updated');
			} else {
				$("[name='main[field_search]'] option[value=id_worldart]").attr('disabled', false).trigger('chosen:updated');
			}
		});
		
		$("[name='movie[voice_field]']").on('change', function() {
			var data = $(this).val();
			if (data == '-') {
				$("[name='movie[actual_voice]']").attr('disabled', 'disabled');
			} else {
				$("[name='movie[actual_voice]']").attr('disabled', false);
			}
		});
		
		function ajax_save_option() {
			var data_form = $('form').serialize();
			$.post('/engine/dle_moonwalk/admin/ajax/ajax.php', {data_form: data_form, action: 'options', user_hash: '{$dle_login_hash}'}, function(data) {
				data = jQuery.parseJSON(data);
				if (data.error) {
					Growl.error({
						title: '{$dle_moonwalk_admin_lang[120]}',
						text: data.error
					});
				} else {
					Growl.info({
						title: '{$dle_moonwalk_admin_lang[121]}',
						text: data.text
					});
				}
			});
			return false;
		}
		
		$('body').on('submit', 'form', function(e) {
			e.preventDefault();
			ajax_save_option();
			return false;
		});
	});
</script>
<div class="panel" style="margin-top: 20px;">
	<div class="panel-content">
		<div class="panel-body">
			© 2018 <a href="https://lazydev.pro/" target="_blank">LazyDev.pro</a> &copy; {$year} | <a href="https://opensource.org/licenses/MIT" target="_blank">MIT License</a>.
		</div>
	</div>
</div>
HTML;
echofooter();
?>
