<?php
/**
 * Template part for displaying page content in dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package TPRM_Theme
 */


$schools = get_tprm_schools();
$schools_count = count($schools);

?>

<div id="tabs" class='tprm-schools-navigation'>
	<ul class="component-navigation groups-nav">
		<li>
			<a href="#schools-tab">
				<div class="nav-item"><?php _e('Schools', 'tprm-theme'); ?></div>
				<span class="count"><?php echo esc_html($schools_count) ?></span>			
			</a>
		</li>
		<li>
			<a href="#stats-tab">
				<div class="nav-item"><?php _e('Stats', 'tprm-theme'); ?></div>		
			</a>
		</li>
	</ul>

	<div id="schools-tab">
		<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback('schools_feedback_message');?></div>
	</div>

	<div id="stats-tab">
		<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback('stats_feedback_message');?></div>
	</div>

</div>

<?php


