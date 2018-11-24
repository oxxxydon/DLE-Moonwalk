<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   1.1.3
 * @link      https://lazydev.pro
 */

$year = date('Y');
$skin_footer = <<<HTML
					<div class="footer text-muted text-size-small">
						<a href="https://lazydev.pro/" target="_blank">LazyDev.pro</a> &copy; {$year} | <a href="https://opensource.org/licenses/MIT" target="_blank">MIT License</a>.
					</div>
				</div>
			</div>
		</div>
	</div>
    <div id="style_switcher" title="{$lang['settings_panel_1']}" style="display: none;">
        <div>
            <h5>{$lang['settings_panel_9']}</h5>
            <ul class="switcher_app_themes" id="theme_switcher">
                <li class="app_style_default" data-app-theme="">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_a" data-app-theme="dle_theme_a">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_b" data-app-theme="dle_theme_b">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_c" data-app-theme="dle_theme_c">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_d" data-app-theme="dle_theme_d">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_e" data-app-theme="dle_theme_e">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_f" data-app-theme="dle_theme_f">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_g" data-app-theme="dle_theme_g">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_h" data-app-theme="dle_theme_h">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_i" data-app-theme="dle_theme_i">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_dark" data-app-theme="dle_theme_dark">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
            </ul>
        </div>
        <div>
            <h5>{$lang['settings_panel_2']}</h5>
			<div class="checkbox">
				<label><input type="checkbox" name="style_sidebar_mini" id="style_sidebar_mini" class="icheck">{$lang['settings_panel_3']}</label>
			</div>
        </div>
        <div>
            <h5>{$lang['settings_panel_4']}</h5>
			<div class="checkbox">
				<label><input type="checkbox" name="style_layout_boxed" id="style_layout_boxed" class="icheck">{$lang['settings_panel_5']}</label>
			</div>
        </div>
        <div>
            <h5>{$lang['settings_panel_6']}</h5>
			<label class="radio-inline"><input class="icheck" type="radio" name="style_input" value="0">{$lang['settings_panel_7']}</label>
			<label class="radio-inline"><input class="icheck" type="radio" name="style_input" value="1">{$lang['settings_panel_8']}</label>
        </div>
    </div>
</body>
</html>
HTML;
?>