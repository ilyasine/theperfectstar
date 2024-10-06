<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_Help{
	public static function message(){
		?>
<div class="postbox">
    <h3 class="hndle"><span>&nbsp;<?php _e( 'Do you like it?', 'kwf-importer' ); ?></span></h3>

    <div class="inside" style="display: block;">
        <img src="<?php echo esc_url( plugins_url( 'assets/icon_coffee.png', dirname( __FILE__ ) ) ); ?>" alt="<?php _e( 'buy me a coffee', 'kwf-importer' ); ?>" style=" margin: 5px; float:left;">
        <p><?php _e( 'Hi! we are', 'kwf-importer'); ?> <a href="https://twitter.com/fjcarazo" target="_blank" title="Javier Carazo">Javier Carazo</a> <?php _e( 'and all the team of', 'kwf-importer' ); ?> <a href="http://codection.com">Codection</a>, <?php _e("developers of this plugin.", 'kwf-importer' ); ?></p>
        <p><?php _e( 'We have been spending many hours to develop this plugin and answering questions in the forum to give you the best support. <br>If you like and use this plugin, you can <strong>buy us a cup of coffee</strong>.', 'kwf-importer' ); ?></p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="QPYVWKJG4HDGG">
			<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e('PayPal – The safer, easier way to pay online.', 'kwf-importer' ); ?>">
			<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
		</form>
        <div style="clear:both;"></div>
    </div>

    <h3 class="hndle"><span>&nbsp;<?php _e( 'Or if you prefer, you can also help us becoming a Patreon:', 'kwf-importer' ); ?></span></h3>

    <div class="inside TPRM_importer" style="display: block;">
    	<a class="patreon" color="primary" type="button" name="become-a-patron" data-tag="become-patron-button" href="https://www.patreon.com/carazo" role="button">
    		<div class="oosjif-1 jFPfxp"><span>Become a patron</span></div>
    	</a>
    </div>
</div>

<div class="postbox">
    <h3 class="hndle"><span>&nbsp;<?php _e( 'Need proffessional help with WordPress or WooCommerce?', 'kwf-importer' ); ?></span></h3>

    <div class="inside" style="display: block;">
        <p><?php _e( 'Hi! we are', 'kwf-importer' ); ?> <a href="https://twitter.com/fjcarazo" target="_blank" title="Javier Carazo">Javier Carazo</a> <?php _e( 'and the team of', 'kwf-importer' ) ?> <a href="http://codection.com">Codection</a>, <?php _e( 'developers of this plugin.', 'kwf-importer' ); ?></p>
        <p><?php _e( 'We work daily with WordPress and WooCommerce if you need professional help, hire us. You can send us a message at', 'kwf-importer' ); ?> <a href="mailto:contacto@codection.com">contacto@codection.com</a>.</p>
        <div style="clear:both;"></div>
    </div>
</div>
		<?php
	}
}