<?php
function fliphtml5_shortcode($atts){
	extract(shortcode_atts(array(
	'id' => '',
	'width' => '720px',
	'height' => '480px'
	), $atts));
	
	$booklink = str_replace( '-', '/', $id);
	$url = 'https://online.fliphtml5.com/'.$booklink.'/';
	
	$html_code = '<iframe  style="width:'.$width.';height:'.$height.'"  src="'.$url.'"  seamless="seamless" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true"></iframe>';
  
  return $html_code;
}
?>