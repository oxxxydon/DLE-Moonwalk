<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev (https://lazydev.pro)
 * @version   1.1.1
 * @link      https://lazydev.pro
 */

if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

$all_xfield = xfieldsload();
$count_all_xfield = count($all_xfield);

$xfield = ['-' => '-', 'title' => $this->module_lang[144], 'short_story' => $this->module_lang[145], 'full_story' => $this->module_lang[146]];
$allField = ['p.title' => $this->module_lang[144], 'p.short_story' => $this->module_lang[145], 'p.full_story' => $this->module_lang[146]];

for ($i = 0; $i < $count_all_xfield; $i++) {
	$xfield[$all_xfield[$i][0]] = $all_xfield[$i][1];
	$allField[$all_xfield[$i][0]] = $all_xfield[$i][1];
}

$disable_category = CategoryNewsSelection((empty($this->module_config['main']['disable_category']) ? 0 : $this->module_config['main']['disable_category']));

$voice = [52 => '2x2', 16 => 'Agatha Studdio', 7 => 'Alexfilm', 89 => 'AlFair Studio', 40 => 'Alt Pro', 88 => 'AMC', 32 => 'Amedia', 36 => 'Ancord', 49 => 'AniDUB', 48 => 'AniLibria', 105 => 'Animedia', 114 => 'AveBrasil', 102 => 'AvePremier', 113 => 'AveTurk', 34 => 'AXN Sci-fi', 9 => 'BaibaKo', 17 => 'Coldfilm', 87 => 'CTC', 110 => 'D1', 73 => 'datynet', 72 => 'den904', 64 => 'Discovery', 42 => 'Diva Universal', 60 => 'DreamRecords', 22 => 'Filiza Studio', 28 => 'Flux-Team', 99 => 'FocusStudio', 18 => 'Fox', 41 => 'F-TRAIN', 81 => 'Gears Media', 61 => 'GladiolusTV', 109 => 'Good People', 30 => 'GREEN TEA', 8 => 'HamsterStudio ', 112 => 'HDrezka Studio', 80 => 'HTB', 57 => 'IdeaFilm', 116 => 'JAM', 12 => 'Jaskier', 24 => 'Jetvis Studio', 13 => 'Jimmy J.', 100 => 'JWA Project', 104 => 'KANSAI', 98 => 'Levelin', 79 => 'Lord32x', 2 => 'LostFilm', 26 => 'Lw13pro', 51 => 'MC Entertainment', 75 => 'napaBo3uk', 58 => 'Narkom Pro', 3 => 'Newstudio', 59 => 'Nice-Media', 4 => 'Novafilm', 78 => 'Novamedia', 96 => 'OMSKBIRD records', 97 => 'Onibaku', 10 => 'Ozz', 90 => 'Paramount Comedy', 74 => 'PashaUp', 63 => 'Prichudiki', 19 => 'ProjektorShow', 77 => 'R.A.I.M', 94 => 'SDI Media', 47 => 'SET Russia', 95 => 'SHIZA Project', 115 => 'SoftBox', 43 => 'Sony Sci-Fi', 85 => 'Sony Turbo', 101 => 'STEPonee', 108 => 'StudioBand', 93 => 'Sunshine Studio', 15 => 'To4ka', 86 => 'Tycoon', 45 => 'Universal Russia', 29 => 'Victory-Films', 14 => 'ViruseProject', 62 => 'VO-production', 37 => 'xaros', 71 => 'xixidok', 68 => 'Zamez', 53 => 'АРК ТВ', 70 => 'Гаврилов', 82 => 'Гоблин', 25 => 'Дасевич', 66 => 'двухголосый закадровый', 21 => 'Дубляж', 76 => 'Есарев', 69 => 'Живов', 92 => 'Кравец', 11 => 'Кубик в Кубе', 5 => 'Кураж-бамбей', 27 => 'Матвеев', 67 => 'многоголосый закадровый', 50 => 'Невафильм', 44 => 'Несмертельное оружие', 111 => 'Не требуется', 65 => 'одноголосый закадровый', 83 => 'Первый канал', 6 => 'Сербин', 56 => 'Студия Райдо', 20 => 'Субтитры', 107 => 'Субтитры PhysKids', 23 => 'Сыендук', 106 => 'ТВ-3', 103 => 'Украинский', 46 => 'Шадинский'];

echo <<<HTML
<style>
input:disabled + .custom-switch-indicator { 
	background: #D64541; 
}
.table td:nth-child(1) {
	width: 70%;
}
.table label {
	float: right;
}
</style>
	<div class="card">
		<div class="form-group" style="padding: 1.5rem 1.5rem;margin-bottom:0rem;">
			<div class="selectgroup w-100">
				<label class="selectgroup-item">
					<input type="radio" name="block" value="4" class="selectgroup-input" checked>
					<span class="selectgroup-button">{$this->module_lang[98]}</span>
				</label>
				<label class="selectgroup-item">
					<input type="radio" name="block" value="7" class="selectgroup-input">
					<span class="selectgroup-button">{$this->module_lang[33]}</span>
				</label>
				<label class="selectgroup-item">
					<input type="radio" name="block" value="1" class="selectgroup-input">
					<span class="selectgroup-button">{$this->module_lang[95]}</span>
				</label>
				<label class="selectgroup-item">
					<input type="radio" name="block" value="2" class="selectgroup-input">
					<span class="selectgroup-button">{$this->module_lang[96]}</span>
				</label>
				<label class="selectgroup-item">
					<input type="radio" name="block" value="5" class="selectgroup-input">
					<span class="selectgroup-button">{$this->module_lang[35]}</span>
				</label>
			</div>
		</div>
	</div>
	
	<div class="card" id="typeBlock_1" style="display:none;">
		<div class="form-group" style="padding: 1.5rem 1.5rem;margin-bottom:0rem;">
			<div class="selectgroup w-100">
				<label class="selectgroup-item">
					<input type="radio" name="option_block" value="1" class="selectgroup-input" checked>
					<span class="selectgroup-button">{$this->module_lang[32]}</span>
				</label>
				<label class="selectgroup-item">
					<input type="radio" name="option_block" value="2" class="selectgroup-input">
					<span class="selectgroup-button">{$this->module_lang[33]}</span>
				</label>
				<label class="selectgroup-item">
					<input type="radio" name="option_block" value="5" class="selectgroup-input">
					<span class="selectgroup-button">{$this->module_lang[36]}</span>
				</label>
			</div>
		</div>
	</div>
	<form method="post">
		<div id="typeDataBlock_4">
			<div class="card">
				<table class="table table-striped">
HTML;
$this->showTr(
	$this->module_lang[28],
	$this->module_lang[29],
	'input',
	['main[api_token]', 'text', $this->module_config['main']['api_token']]
);
$this->showTr(
	$this->module_lang[178],
	$this->module_lang[179],
	'checkbox',
	['main[ssl]', $this->module_config['main']['ssl'], ($this->module_config['main']['domain'] != '' ? true : false)]
);
$this->showTr(
	$this->module_lang[180],
	$this->module_lang[181],
	'input',
	['main[domain]', 'text', $this->module_config['main']['domain'], false, ($this->module_config['main']['ssl'] ? true : false)]
);
$this->showTr(
	$this->module_lang[17],
	$this->module_lang[18],
	'select',
	['main[id_kinopoisk]', $xfield, true, $this->module_config['main']['id_kinopoisk'], false, false]
);
$this->showTr(
	$this->module_lang[19],
	$this->module_lang[20],
	'select',
	['main[id_worldart]', $xfield, true, $this->module_config['main']['id_worldart'], false, false]
);
$this->showTr(
	$this->module_lang[10],
	$this->module_lang[11],
	false,
	"<select name=\"main[disable_category][]\" class=\"form-control custom-select\" data-placeholder=\"{$this->module_lang[7]}\" multiple>" . $disable_category . "</select>"
);
$this->showTr(
	$this->module_lang[12],
	$this->module_lang[13],
	'select_sort',
	['main[voice][]', $voice, true, $this->module_config['main']['voice'], true, false, $this->module_lang[14]]
);
echo <<<HTML
				</table>
			</div>
		</div>
		<div id="typeDataBlock_5" style="display:none;">
			<div class="card">
				<table class="table table-striped">
HTML;
$this->showTr(
	$this->module_lang[73],
	$this->module_lang[74],
	'input',
	['block[block_date]', 'number', $this->module_config['block']['block_date'] ? $this->module_config['block']['block_date'] : 4, false, false, 1, 10]
);
$this->showTr(
	$this->module_lang[75],
	$this->module_lang[76],
	'input',
	['block[block_news]', 'number', $this->module_config['block']['block_news'] ? $this->module_config['block']['block_news'] : 20, false, false, 1, 100]
);
$this->showTr(
	$this->module_lang[77],
	$this->module_lang[78],
	'checkbox',
	['block[all_data]', $this->module_config['block']['all_data'], ($this->module_config['block']['one_voice'] ? true : false)]
);
$this->showTr(
	$this->module_lang[79],
	$this->module_lang[80],
	'checkbox',
	['block[one_voice]', $this->module_config['block']['one_voice'], ($this->module_config['block']['all_data'] ? true : false)]
);
$this->showTr(
	$this->module_lang[65],
	$this->module_lang[66],
	'checkbox',
	['block[moonwalk_block_date]', $this->module_config['block']['moonwalk_block_date'], false]
);
echo <<<HTML
				</table>
			</div>
		</div>
		<div id="typeDataBlock_1" style="display:none;">
			<div id="option_block_1">
				<div class="card">
					<table class="table table-striped">
HTML;
$this->showTr(
	$this->module_lang[182],
	$this->module_lang[183],
	'checkbox',
	['serial[update_all_voice]', $this->module_config['serial']['update_all_voice'], false]
);
$this->showTr(
	$this->module_lang[15],
	$this->module_lang[16],
	'checkbox',
	['serial[season_by_season]', $this->module_config['serial']['season_by_season'], false]
);
$this->showTr(
	$this->module_lang[21],
	$this->module_lang[22],
	'select',
	['serial[season_number]', $xfield, true, $this->module_config['serial']['season_number'], false, ($this->module_config['serial']['season_by_season'] && $this->module_config['serial']['season_by_season'] != '-' ? false : true), $this->module_lang[23]]
);
echo <<<HTML
					</table>
				</div>
			</div>
			<div id="option_block_2" style="display:none;">
				<div class="card">
					<table class="table table-striped">
HTML;
$this->showTr(
	$this->module_lang[37],
	$this->module_lang[38],
	'select',
	['serial[season_xfield]', $xfield, true, $this->module_config['serial']['season_xfield'], false, false, $this->module_lang[23]]
);
$this->showTr(
	$this->module_lang[39],
	$this->module_lang[40],
	'select',
	['serial[seria_xfield]', $xfield, true, $this->module_config['serial']['seria_xfield'], false, false, $this->module_lang[23]]
);
$this->showTr(
	$this->module_lang[41],
	$this->module_lang[42],
	'select',
	['serial[season_format]', [1 => $this->module_lang[43], 2 => $this->module_lang[44], 3 => $this->module_lang[45]], true, $this->module_config['season_format'], false, ($this->module_config['serial']['season_xfield'] && $this->module_config['serial']['season_xfield'] != '-' ? false : true)]
);
$this->showTr(
	$this->module_lang[46],
	$this->module_lang[47],
	'select',
	['serial[seria_format]', [1 => $this->module_lang[48], 2 => $this->module_lang[49], 3 => $this->module_lang[50]], true, $this->module_config['seria_format'], false, ($this->module_config['serial']['seria_xfield'] && $this->module_config['serial']['seria_xfield'] != '-' ? false : true)]
);
echo <<<HTML
					</table>
				</div>
			</div>
			<div id="option_block_5" style="display:none;">
				<div class="card">
					<table class="table table-striped">
HTML;
$this->showTr(
	$this->module_lang[81],
	$this->module_lang[82],
	'checkbox',
	['serial[change_meta]', $this->module_config['serial']['change_meta'], false]
);
$this->showTr(
	$this->module_lang[83],
	$this->module_lang[84],
	'textarea',
	['serial[meta_title]', $this->module_config['serial']['meta_title'], false, ($this->module_config['serial']['change_meta'] ? false : true)]
);
echo <<<HTML
					</table>
				</div>
			</div>
		</div>
		<div id="typeDataBlock_2" style="display:none;">
			<div class="card">
				<div class="card-header" style="border-top: 2px solid rgba(0, 40, 100, 0.12);">
					<h3 class="card-title">{$this->module_lang[1]}</h3>
				</div>
				<table class="table">
HTML;
$this->showTr(
	$this->module_lang[184],
	$this->module_lang[185],
	'select',
	['movie[quality_field]', $xfield, true, $this->module_config['movie']['quality_field'], false, false, $this->module_lang[23]]
);
$this->showTr(
	$this->module_lang[186],
	$this->module_lang[187],
	'select',
	['movie[video_field]', $xfield, true, $this->module_config['movie']['video_field'], false, false, $this->module_lang[23]]
);
echo <<<HTML
				</table>
			</div>
			<div class="card">
				<div class="card-header" style="border-top: 2px solid rgba(0, 40, 100, 0.12);">
					<h3 class="card-title">{$this->module_lang[36]}</h3>
				</div>
				<table class="table">
HTML;
$this->showTr(
	$this->module_lang[83],
	$this->module_lang[142],
	'checkbox',
	['movie[change_meta]', $this->module_config['movie']['change_meta'], false]
);
$this->showTr(
	$this->module_lang[189],
	$this->module_lang[143],
	'textarea',
	['movie[meta_title]', $this->module_config['movie']['meta_title'], false, ($this->module_config['movie']['change_meta'] ? false : true)]
);
echo <<<HTML
				</table>
			</div>
		</div>
		<div id="typeDataBlock_7" style="display:none;">
			<div class="card card-collapsed" id="blockTag">
				<div class="card-header">
					<h3 class="card-title">{$this->module_lang[149]}</h3>
					<div class="card-options">
						<a href="#" onclick="showHideTags(); return false;" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
					</div>
				</div>
				<div class="card-body">
					<p><code class="highlighter-rouge">[tag-title_ru]{title_ru}[/tag-title_ru]</code> - {$this->module_lang[150]}</p>
					<p><code class="highlighter-rouge">[tag-title_en]{title_en}[/tag-title_en]</code> - {$this->module_lang[151]}</p>
					<p><code class="highlighter-rouge">[tag-video]{video}[/tag-video]</code> - {$this->module_lang[174]}</p>
					<p><code class="highlighter-rouge">[tag-trailer]{trailer}[/tag-trailer]</code> - {$this->module_lang[175]}</p>
					<p><code class="highlighter-rouge">[tag-description]{description}[/tag-description]</code> - {$this->module_lang[152]}</p>
					<p><code class="highlighter-rouge">[tag-quality]{quality}[/tag-quality]</code> - {$this->module_lang[153]}</p>
					<p><code class="highlighter-rouge">[tag-year]{year}[/tag-year]</code> - {$this->module_lang[155]}</p>
					<p><code class="highlighter-rouge">[tag-translator]{translator}[/tag-translator]</code> - {$this->module_lang[154]}</p>
					<p><code class="highlighter-rouge">[tag-countries]{countries}[/tag-countries]</code> - {$this->module_lang[156]}</p>
					<p><code class="highlighter-rouge">[tag-genres]{genres}[/tag-genres]</code> - {$this->module_lang[158]}</p>
					<p><code class="highlighter-rouge">[tag-duration]{duration}[/tag-duration]</code> - {$this->module_lang[161]}</p>
					<p><code class="highlighter-rouge">[tag-season]{season}[/tag-season]</code> - {$this->module_lang[166]}</p>
					<p><code class="highlighter-rouge">[tag-season-format-1]{season-format-1}[/tag-season-format-1]</code> - {$this->module_lang[167]}</p>
					<p><code class="highlighter-rouge">[tag-season-format-2]{season-format-2}[/tag-season-format-2]</code> - {$this->module_lang[168]}</p>
					<p><code class="highlighter-rouge">[tag-season-format-3]{season-format-3}[/tag-season-format-3]</code> - {$this->module_lang[169]}</p>
					<p><code class="highlighter-rouge">[tag-seria]{seria}[/tag-seria]</code> - {$this->module_lang[170]}</p>
					<p><code class="highlighter-rouge">[tag-seria-format-1]{seria-format-1}[/tag-seria-format-1]</code> - {$this->module_lang[171]}</p>
					<p><code class="highlighter-rouge">[tag-seria-format-2]{seria-format-2}[/tag-seria-format-2]</code> - {$this->module_lang[172]}</p>
					<p><code class="highlighter-rouge">[tag-seria-format-3]{seria-format-3}[/tag-seria-format-3]</code> - {$this->module_lang[173]}</p>
					<p><code class="highlighter-rouge">[tag-directors]{directors}[/tag-directors]</code> - {$this->module_lang[157]}</p>
					<p><code class="highlighter-rouge">[tag-actors]{actors}[/tag-actors]</code> - {$this->module_lang[159]}</p>
					<p><code class="highlighter-rouge">[tag-age]{age}[/tag-age]</code> - {$this->module_lang[160]}</p>
					<p><code class="highlighter-rouge">[tag-kinopoisk_id]{kinopoisk_id}[/tag-kinopoisk_id]</code> - {$this->module_lang[176]}</p>
					<p><code class="highlighter-rouge">[tag-world_art_id]{world_art_id}[/tag-world_art_id]</code> - {$this->module_lang[177]}</p>
					<p><code class="highlighter-rouge">[tag-kinopoisk_rating]{kinopoisk_rating}[/tag-kinopoisk_rating]</code> - {$this->module_lang[162]}</p>
					<p><code class="highlighter-rouge">[tag-kinopoisk_votes]{kinopoisk_votes}[/tag-kinopoisk_votes]</code> - {$this->module_lang[162]}</p>
					<p><code class="highlighter-rouge">[tag-imdb_rating]{imdb_rating}[/tag-imdb_rating]</code> - {$this->module_lang[163]}</p>
					<p><code class="highlighter-rouge">[tag-imdb_votes]{imdb_votes}[/tag-imdb_votes]</code> - {$this->module_lang[164]}</p>
				</div>
			</div>
			<div class="card">
				<table class="table table-striped">
HTML;

$xfOptions = [
	'title_ru' => $this->module_lang[102],
	'title_en' => $this->module_lang[103],
	'description' => $this->module_lang[104],
	'quality' => $this->module_lang[105],
	'translator' => $this->module_lang[106],
	'year' => $this->module_lang[109],
	'countries' => $this->module_lang[110],
	'directors' => $this->module_lang[112],
	'genres' => $this->module_lang[113],
	'actors' => $this->module_lang[114],
	'age' => $this->module_lang[115],
	'duration' => $this->module_lang[117],
	'kinopoisk_rating' => $this->module_lang[118],
	'kinopoisk_votes' => $this->module_lang[119],
	'imdb_rating' => $this->module_lang[120],
	'imdb_votes' => $this->module_lang[121],
];

foreach ($allField as $key => $value) {
	if ($key == 'p.short_story' || $key == 'p.full_story') {
		$this->showTrInline($value, '', 'textarea', ['data[' . $key . ']', $this->module_config['data'][$key]]);
	} else {
		$this->showTrInline($value, '', 'input', ['data[' . $key . ']', 'text', $this->module_config['data'][$key]]);
	}
}
echo <<<HTML
				</table>
			</div>
		</div>
		<button type="submit" name="submit" class="btn btn-lg btn-success">{$this->module_lang['save']}</button>
	</form>
	<script>
		function showHideTags() {
			if ($("#blockTag").hasClass('card-collapsed')) {
				$("#blockTag").removeClass('card-collapsed');
			} else {
				$("#blockTag").addClass('card-collapsed');
			}
		}
		$(function() {
			$("select[multiple]").chosen({allow_single_deselect:true, no_results_text: 'Ничего не найдено', width: "300px"});
			
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
					$("[name='serial[season_number]']").attr('disabled', false);
				} else {
					$("[name='serial[season_number]']").attr('disabled', 'disabled');
				}
			});
			
			$("[name='serial[season_xfield]']").on('change', function() {
				var data = $(this).val();
				if (data == '-') {
					$("[name='serial[season_format]']").attr('disabled', 'disabled');
				} else {
					$("[name='serial[season_format]']").attr('disabled', false);
				}
			});
			
			$("[name='serial[seria_xfield]']").on('change', function() {
				var data = $(this).val();
				if (data == '-') {
					$("[name='serial[seria_format]']").attr('disabled', 'disabled');
				} else {
					$("[name='serial[seria_format]']").attr('disabled', false);
				}
			});
			
			$("body").on("click", "[name=option_block]", function() {
				var id = $(this).val();
				$("[id*=option_block_]").hide();
				$("#option_block_"+id).show();
			});
			
			$("body").on("click", "[name=option_block_film]", function() {
				var id = $(this).val();
				$("[id*=option_block_film_]").hide();
				$("#option_block_film_"+id).show();
			});
			
			$("body").on("click", "[name=block]", function() {
				var id = $(this).val();
				$("[id*=typeBlock_]").hide();
				$("#typeBlock_"+id).show();
				$("[id*=typeDataBlock_]").hide();
				$("#typeDataBlock_"+id).show();
			});
			
			function ajax_save_option() {
				var data_form = $('form').serialize();
				$.post("/engine/dle_moonwalk/admin/ajax/ajax.php", {data_form: data_form, action: 'options', user_hash: '{$this->dle_login_hash}'}, function(data) {
					data = jQuery.parseJSON(data);
					$.toast({
						heading: data.head,
						text: data.text,
						showHideTransition: 'slide',
						position: 'top-right',
						icon: data.icon,
						stack: false
					});
					return false;
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
HTML;
?>