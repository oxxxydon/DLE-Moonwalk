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

echo <<<HTML
<div class="card">
	<table class="table card-table">
		<tbody>
			<tr>
				<td>{$this->module_lang[2]}</td>
				<td class="text-right">
					<span class="badge badge-info">v{$this->module_config['version']}</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
HTML;
?>