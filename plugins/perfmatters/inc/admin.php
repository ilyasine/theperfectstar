<?php 
//accessibility mode styles
$tools = get_option('perfmatters_tools');
if(!empty($tools['accessibility_mode'])) {
	echo '<style>#perfmatters-admin .perfmatters-tooltip-subtext{display: none;}</style>';
}

//settings wrapper
echo '<div id="perfmatters-admin" class="wrap">';

	//hidden h2 for admin notice placement
	echo '<h2 style="display: none;"></h2>';

	//flex container
	echo '<div id="perfmatters-admin-container">';

		echo '<div id="perfmatters-admin-header">';

			//header
			echo '<div class="perfmatters-admin-block">';

				echo '<div id="perfmatters-logo-bar">';

					//logo
					echo '<svg id="perfmatters-logo" viewBox="0 0 428 73"><g transform="matrix(0.75,0,0,0.75,-209.078,-71.2279)"><g transform="matrix(1,0,0,1,250.5,68.3486)"> <path d="M190.582,73.966C192.75,71.773 193.834,68.782 193.834,64.994C193.834,61.206 192.75,58.241 190.582,56.098C188.414,53.954 185.859,52.883 182.919,52.883C179.978,52.883 177.423,53.967 175.255,56.135C173.087,58.303 172.003,61.281 172.003,65.069C172.003,68.857 173.087,71.835 175.255,74.003C177.423,76.172 179.978,77.256 182.919,77.256C185.859,77.256 188.414,76.159 190.582,73.966ZM172.003,50.789C174.944,46.104 179.255,43.761 184.937,43.761C190.619,43.761 195.304,45.743 198.993,49.705C202.681,53.668 204.525,58.777 204.525,65.032C204.525,71.287 202.681,76.408 198.993,80.396C195.304,84.383 190.657,86.377 185.049,86.377C179.442,86.377 175.093,83.86 172.003,78.826L172.003,105.516L161.461,105.516L161.461,44.36L172.003,44.36L172.003,50.789Z" style="fill:#282E34;"></path><path d="M229.469,86.377C223.438,86.377 218.528,84.421 214.74,80.508C210.952,76.595 209.058,71.424 209.058,64.994C209.058,58.565 210.965,53.418 214.778,49.556C218.591,45.693 223.525,43.761 229.581,43.761C235.637,43.761 240.609,45.643 244.496,49.406C248.384,53.169 250.328,58.191 250.328,64.471C250.328,65.917 250.228,67.262 250.029,68.508L219.824,68.508C220.074,71.2 221.07,73.368 222.815,75.013C224.559,76.658 226.777,77.48 229.469,77.48C233.107,77.48 235.799,75.985 237.543,72.994L248.907,72.994C247.711,76.932 245.394,80.147 241.954,82.639C238.515,85.131 234.353,86.377 229.469,86.377ZM239.412,61.705C239.263,58.963 238.254,56.77 236.385,55.126C234.515,53.481 232.26,52.658 229.618,52.658C226.977,52.658 224.771,53.481 223.002,55.126C221.232,56.77 220.173,58.963 219.824,61.705L239.412,61.705Z" style="fill:#282E34;"></path><path d="M267.268,44.36L267.268,51.686C270.06,46.403 274.097,43.761 279.38,43.761L279.38,54.527L276.763,54.527C273.623,54.527 271.256,55.313 269.661,56.883C268.066,58.453 267.268,61.157 267.268,64.994L267.268,85.779L256.727,85.779L256.727,44.36L267.268,44.36Z" style="fill:#282E34;"></path><path d="M303.826,44.36L303.826,52.957L297.246,52.957L297.246,85.779L286.779,85.779L286.779,52.957L282.294,52.957L282.294,44.36L286.779,44.36L286.779,42.042C286.779,37.058 288.1,33.419 290.742,31.126C293.384,28.834 297.545,27.687 303.228,27.687L303.228,36.509C301.034,36.509 299.489,36.933 298.592,37.78C297.695,38.628 297.246,40.048 297.246,42.042L297.246,44.36L303.826,44.36Z" style="fill:#282E34;"></path><path d="M319.919,44.36L319.919,50.565C322.561,46.029 326.848,43.761 332.779,43.761C335.919,43.761 338.723,44.484 341.19,45.93C343.657,47.375 345.563,49.444 346.909,52.135C348.355,49.543 350.336,47.5 352.853,46.004C355.37,44.509 358.199,43.761 361.339,43.761C366.273,43.761 370.248,45.307 373.264,48.397C376.279,51.487 377.787,55.823 377.787,61.406L377.787,85.779L367.32,85.779L367.32,62.901C367.32,59.661 366.497,57.182 364.853,55.462C363.208,53.742 360.965,52.883 358.124,52.883C355.283,52.883 353.027,53.742 351.358,55.462C349.688,57.182 348.853,59.661 348.853,62.901L348.853,85.779L338.386,85.779L338.386,62.901C338.386,59.661 337.564,57.182 335.919,55.462C334.274,53.742 332.031,52.883 329.19,52.883C326.349,52.883 324.094,53.742 322.424,55.462C320.754,57.182 319.919,59.661 319.919,62.901L319.919,85.779L309.378,85.779L309.378,44.36L319.919,44.36Z" style="fill:#282E34;"></path><path d="M413.769,74.003C415.962,71.835 417.059,68.857 417.059,65.069C417.059,61.281 415.962,58.303 413.769,56.135C411.576,53.967 409.009,52.883 406.068,52.883C403.128,52.883 400.573,53.954 398.405,56.098C396.237,58.241 395.153,61.206 395.153,64.994C395.153,68.782 396.249,71.773 398.443,73.966C400.636,76.159 403.19,77.256 406.106,77.256C409.022,77.256 411.576,76.172 413.769,74.003ZM390.032,80.358C386.318,76.346 384.462,71.225 384.462,64.994C384.462,58.764 386.306,53.668 389.994,49.705C393.683,45.743 398.368,43.761 404.05,43.761C409.732,43.761 414.068,46.104 417.059,50.789L417.059,44.36L427.526,44.36L427.526,85.779L417.059,85.779L417.059,78.826C413.919,83.86 409.557,86.377 403.975,86.377C398.393,86.377 393.745,84.371 390.032,80.358Z" style="fill:#282E34;"></path><path d="M452.665,76.957L457.674,76.957L457.674,85.779L451.02,85.779C446.783,85.779 443.518,84.807 441.226,82.863C438.933,80.919 437.787,77.704 437.787,73.218L437.787,52.957L433.375,52.957L433.375,44.36L437.787,44.36L437.787,34.117L448.328,34.117L448.328,44.36L457.599,44.36L457.599,52.957L448.328,52.957L448.328,73.218C448.328,74.564 448.652,75.524 449.3,76.097C449.948,76.67 451.07,76.957 452.665,76.957Z" style="fill:#282E34;"></path><path d="M480.822,76.957L485.831,76.957L485.831,85.779L479.177,85.779C474.941,85.779 471.676,84.807 469.383,82.863C467.09,80.919 465.944,77.704 465.944,73.218L465.944,52.957L461.533,52.957L461.533,44.36L465.944,44.36L465.944,34.117L476.486,34.117L476.486,44.36L485.756,44.36L485.756,52.957L476.486,52.957L476.486,73.218C476.486,74.564 476.81,75.524 477.458,76.097C478.106,76.67 479.227,76.957 480.822,76.957Z" style="fill:#282E34;"></path><path d="M510.25,86.377C504.219,86.377 499.309,84.421 495.521,80.508C491.733,76.595 489.839,71.424 489.839,64.994C489.839,58.565 491.746,53.418 495.559,49.556C499.372,45.693 504.306,43.761 510.362,43.761C516.418,43.761 521.39,45.643 525.277,49.406C529.165,53.169 531.109,58.191 531.109,64.471C531.109,65.917 531.009,67.262 530.81,68.508L500.605,68.508C500.855,71.2 501.851,73.368 503.596,75.013C505.34,76.658 507.558,77.48 510.25,77.48C513.888,77.48 516.58,75.985 518.324,72.994L529.689,72.994C528.492,76.932 526.175,80.147 522.736,82.639C519.296,85.131 515.135,86.377 510.25,86.377ZM520.194,61.705C520.044,58.963 519.035,56.77 517.166,55.126C515.297,53.481 513.041,52.658 510.399,52.658C507.758,52.658 505.552,53.481 503.783,55.126C502.013,56.77 500.954,58.963 500.605,61.705L520.194,61.705Z" style="fill:#282E34;"></path><path d="M548.05,44.36L548.05,51.686C550.841,46.403 554.878,43.761 560.161,43.761L560.161,54.527L557.545,54.527C554.405,54.527 552.037,55.313 550.442,56.883C548.847,58.453 548.05,61.157 548.05,64.994L548.05,85.779L537.508,85.779L537.508,44.36L548.05,44.36Z" style="fill:#282E34;"></path><path d="M563.97,56.471C563.97,52.883 565.49,49.867 568.531,47.425C571.571,44.983 575.608,43.761 580.642,43.761C585.676,43.761 589.714,44.97 592.754,47.388C595.795,49.805 597.414,53.082 597.614,57.219L586.848,57.219C586.549,53.73 584.406,51.986 580.418,51.986C578.424,51.986 576.879,52.384 575.783,53.182C574.686,53.979 574.138,55.076 574.138,56.471C574.138,57.867 574.96,58.963 576.605,59.761C578.25,60.558 580.244,61.181 582.586,61.63C584.929,62.079 587.259,62.639 589.577,63.312C591.894,63.985 593.876,65.181 595.52,66.901C597.165,68.62 597.988,70.901 597.988,73.742C597.988,77.48 596.405,80.52 593.24,82.863C590.075,85.206 586.038,86.377 581.128,86.377C576.219,86.377 572.194,85.218 569.054,82.9C565.914,80.583 564.194,77.256 563.895,72.919L574.661,72.919C575.06,76.408 577.278,78.153 581.315,78.153C583.259,78.153 584.829,77.717 586.025,76.845C587.222,75.972 587.82,74.826 587.82,73.405C587.82,71.985 586.997,70.863 585.353,70.041C583.708,69.219 581.714,68.583 579.371,68.135C577.029,67.686 574.699,67.138 572.381,66.49C570.063,65.842 568.082,64.683 566.437,63.013C564.792,61.343 563.97,59.163 563.97,56.471Z" style="fill:#282E34;"></path></g><g transform="matrix(0.785202,0,0,0.785202,271.099,91.0524)"><path d="M62.676,55.56C59.095,53.461 57.851,48.842 59.956,45.23C63.289,39.477 72.058,40.543 73.756,47.07C75.498,53.601 68.388,58.895 62.676,55.56M76.266,32.07C61.162,23.444 43.184,37.345 47.576,54.02C49.947,62.939 57.949,68.58 66.416,68.58C79.28,68.58 88.629,56.414 85.366,44C84.026,38.96 80.816,34.73 76.266,32.07M122.906,69.76L131.086,86.64C107.098,88.172 101.495,86.06 91.506,104.08C88.899,108.793 84.498,117.69 73.486,115.53C69.306,114.72 65.836,112.22 63.686,108.48L23.756,38.73C21.126,34.17 21.136,28.74 23.776,24.2C26.406,19.68 31.086,16.99 36.316,16.99C36.346,16.99 36.386,17 36.426,17L116.826,17.24C126.204,17.24 137.022,26.92 126.856,43.35C122.916,49.75 117.516,58.52 122.906,69.76M145.776,89.45L133.716,64.54C131.286,59.47 133.196,55.94 137.076,49.64C139.186,46.23 141.356,42.7 142.406,38.62C144.546,30.81 142.816,22.19 137.766,15.58C132.756,9.01 125.126,5.24 116.846,5.24C116.301,5.238 36.306,4.99 36.306,4.99C15.903,4.99 3.167,27.071 13.356,44.71L53.276,114.44C61.324,128.487 79.672,131.925 92.136,122.57C96.511,119.894 100.376,112.826 101.996,109.91C105.546,103.51 107.656,100.16 113.206,99.81L140.766,98.05C145.01,97.772 147.638,93.28 145.776,89.45M72.986,52.8C71.966,54.55 70.326,55.8 68.376,56.32C66.436,56.84 64.406,56.57 62.676,55.56C60.936,54.54 59.696,52.91 59.186,50.95C58.666,49 58.936,46.98 59.956,45.23C62.036,41.64 66.676,40.42 70.266,42.47C72.006,43.48 73.246,45.11 73.756,47.07C74.276,49.02 74.006,51.05 72.986,52.8M85.366,44C84.026,38.96 80.816,34.73 76.266,32.07C66.916,26.73 54.946,29.94 49.586,39.2C46.956,43.7 46.246,48.97 47.576,54.02C48.246,56.54 49.386,58.86 50.916,60.88C52.446,62.89 54.386,64.6 56.636,65.92C59.646,67.68 63.006,68.58 66.416,68.58C68.096,68.58 69.776,68.36 71.446,67.92C76.506,66.58 80.746,63.35 83.356,58.83C85.986,54.32 86.696,49.06 85.366,44M72.986,52.8C71.966,54.55 64.406,56.57 62.676,55.56C60.936,54.54 59.696,52.91 59.186,50.95C58.666,49 58.936,46.98 59.956,45.23C62.036,41.64 66.676,40.42 70.266,42.47C72.006,43.48 73.246,45.11 73.756,47.07C74.276,49.02 74.006,51.05 72.986,52.8M85.366,44C84.026,38.96 80.816,34.73 76.266,32.07C66.916,26.73 54.946,29.94 49.586,39.2C46.956,43.7 46.246,48.97 47.576,54.02C48.246,56.54 49.386,58.86 50.916,60.88C52.446,62.89 54.386,64.6 56.636,65.92C59.646,67.68 63.006,68.58 66.416,68.58C68.096,68.58 69.776,68.36 71.446,67.92C76.506,66.58 80.746,63.35 83.356,58.83C85.986,54.32 86.696,49.06 85.366,44" style="fill:#4A89DD;"></path></g></g></svg> ';

					//menu toggle
					echo '<a href="#" id="perfmatters-menu-toggle"><span class="dashicons dashicons-menu"></span></a>';
				echo '</div>';

				//menu
				echo '<div id="perfmatters-menu">';

					if(!is_network_admin()) {

						//options
						echo '<a href="#" rel="options-general" class="active"><span class="dashicons dashicons-dashboard"></span>' . __('General', 'perfmatters') . '</a>';
						echo '<a href="#assets" rel="options-assets"><span class="dashicons dashicons-editor-code"></span>' . __('Assets', 'perfmatters') . '</a>';
						echo '<a href="#preload" rel="options-preload"><span class="dashicons dashicons-clock"></span>' . __('Preloading', 'perfmatters') . '</a>';
						echo '<a href="#lazyload" rel="options-lazyload"><span class="dashicons dashicons-images-alt2"></span>' . __('Lazy Loading', 'perfmatters') . '</a>';
						echo '<a href="#fonts" rel="options-fonts"><span class="dashicons dashicons-editor-paste-text"></span>' . __('Fonts', 'perfmatters') . '</a>';
						echo '<a href="#cdn" rel="options-cdn"><span class="dashicons dashicons-admin-site-alt2"></span>' . __('CDN', 'perfmatters') . '</a>';
						echo '<a href="#analytics" rel="options-analytics"><span class="dashicons dashicons-chart-bar"></span>' . __('Analytics', 'perfmatters') . '</a>';

						//spacer
						echo '<hr style="border-top: 1px solid #f2f2f2; border-bottom: 0px; margin: 10px 0px;" />';

						//tools
						echo '<a href="#tools" rel="tools-plugin"><span class="dashicons dashicons-admin-tools"></span>' . __('Tools', 'perfmatters') . '</a>';
						echo '<a href="#database" rel="tools-database"><span class="dashicons dashicons-database"></span>' . __('Database', 'perfmatters') . '</a>';
					}
					else {

						//network
						echo '<a href="#" rel="network-network" class="active"><span class="dashicons dashicons-admin-settings"></span>' . __('Network', 'perfmatters') . '</a>';
					}

					//license
					if(!is_plugin_active_for_network('perfmatters/perfmatters.php') || is_network_admin()) {
						echo '<a href="#license" rel="license-license"><span class="dashicons dashicons-admin-network"></span>' . __('License', 'perfmatters') . '</a>';
					}

					//support
					echo '<a href="#support" rel="support-support"><span class="dashicons dashicons-editor-help"></span>' . __('Support', 'perfmatters') . '</a>';

				echo '</div>';
			echo '</div>';

			//cta
			if(!get_option('perfmatters_close_cta')) {
				echo '<a href="https://novashare.io/perfmatters-discount/?utm_campaign=plugin-cta&utm_source=perfmatters" target="_blank" id="perfmatters-cta" class="perfmatters-admin-block perfmatters-mobile-hide">';
					echo '<span class="dashicons dashicons-tag" style="margin-right: 10px;"></span>';
					echo '<span>' . __('Get 25% off our social sharing plugin.') . '</span>';
					echo '<span id="perfmatters-cta-close" class="dashicons dashicons-no-alt"></span>';
				echo '</a>';
			}

		echo '</div>';

		echo '<div style="flex-grow: 1;">';
			echo '<div class="perfmatters-admin-block">';

				//version number
				echo '<span id="pm-version" class="perfmatters-mobile-hide">' . __('Version', 'perfmatters') . ' ' . PERFMATTERS_VERSION . '</span>';

				if(!is_network_admin()) {

					//main settings form
					echo '<form method="post" id="perfmatters-options-form" enctype="multipart/form-data" data-pm-option="options">';

						//options
						echo '<div id="perfmatters-options"' . (empty($tools['show_advanced']) ? ' class="pm-hide-advanced"' : '') . '>';

							echo '<section id="options-general" class="section-content active">';
						    	perfmatters_settings_section('perfmatters_options', 'perfmatters_options', 'dashicons-dashboard');
						    	perfmatters_settings_section('perfmatters_options', 'login_url');
						    	perfmatters_settings_section('perfmatters_options', 'perfmatters_woocommerce');
						    echo '</section>';

						    echo '<section id="options-assets" class="section-content">';
						    	perfmatters_settings_section('perfmatters_options', 'assets', 'dashicons-editor-code');
						    	perfmatters_settings_section('perfmatters_options', 'assets_js');
						    	perfmatters_settings_section('perfmatters_options', 'assets_css');
						    	perfmatters_settings_section('perfmatters_options', 'assets_code');
						    echo '</section>';

						    echo '<section id="options-preload" class="section-content">';
						    	perfmatters_settings_section('perfmatters_options', 'preload', 'dashicons-clock');
						    echo '</section>';

						    echo '<section id="options-lazyload" class="section-content">';
						    	perfmatters_settings_section('perfmatters_options', 'lazyload', 'dashicons-images-alt2');
						    echo '</section>';

						    echo '<section id="options-fonts" class="section-content">';
						    	perfmatters_settings_section('perfmatters_options', 'perfmatters_fonts', 'dashicons-editor-paste-text');
						    echo '</section>';

						    echo '<section id="options-cdn" class="section-content">';
						    	perfmatters_settings_section('perfmatters_options', 'perfmatters_cdn', 'dashicons-admin-site-alt2');
						    echo '</section>';

						    echo '<section id="options-analytics" class="section-content">';
						    	perfmatters_settings_section('perfmatters_options', 'perfmatters_analytics', 'dashicons-chart-bar');
						    echo '</section>';

					    echo '</div>';

					    //tools
						echo '<div id="perfmatters-tools">';

							echo '<section id="tools-plugin" class="section-content">';
						    	perfmatters_settings_section('perfmatters_tools', 'plugin', 'dashicons-admin-tools');
						    echo '</section>';

						    echo '<section id="tools-database" class="section-content">';
						    	perfmatters_settings_section('perfmatters_tools', 'database', 'dashicons-database');
						    echo '</section>';

						echo '</div>';

							echo '<div id="perfmatters-save" style="margin-top: 20px;">';
							perfmatters_action_button('save_settings', __('Save Changes', 'perfmatters'));
					    echo '</div>';

					echo '</form>';
				}
				else {

					//network
					echo '<section id="network-network" class="section-content active">';		
						require_once('network.php');
					echo '</section>';
				}

				//license
				if(!is_plugin_active_for_network('perfmatters/perfmatters.php') || is_network_admin()) {
					echo '<section id="license-license" class="section-content">';					
						require_once('license.php');
					echo '</section>';
				}

				//support
				echo '<section id="support-support" class="section-content">';	
					require_once('support.php');
				echo '</section>';

				//display correct section based on URL anchor
				echo '<script>
					!(function (t) {
					    var a = t.trim(window.location.hash);
					    if (a) {
					    	t("#perfmatters-menu > a.active").removeClass("active");
					    	var selectedNav = t(\'#perfmatters-menu > a[href="\' + a + \'"]\');
					    	t("#perfmatters-options-form").attr("data-pm-option", selectedNav.attr("rel").split("-")[0]); 
					    	t(selectedNav).addClass("active");
					    	var activeSection = t("#perfmatters-options .section-content.active");
					    	activeSection.removeClass("active");
					    	t("#" + selectedNav.attr("rel")).addClass("active");
					    }
					})(jQuery);
				</script>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';