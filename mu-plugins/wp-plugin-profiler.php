<?php
/** 
 * Plugin Name:             wt plugin profiler
 * Description:             low level plugin load manager and profiler
 * Version:                 1.00.01
 * Requires at least:       5.7
 * Requires PHP:            7.4
 * Author:                  Wes Tatters
 * Text Domain:             wt-plugin-profiler
 * Domain Path:             /languages
 */
/** 
 * Date:                   2021-11-05 16:00
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );


/** **************************************************************************************************** */

define("WTPP_VERSION", "1.00.01" );

/** **************************************************************************************************** */

// handle activation/deactivtion hooks
register_activation_hook( __FILE__, 'WTPP_register_activation' );
function WTPP_register_activation() { 
// handle any activation requirements
}

register_deactivation_hook( __FILE__, 'WTapp_register_deactivation' );
function WTPP_register_deactivation() { 
// handle any deactivation issues
}

register_uninstall_hook(__FILE__, 'WTPP_register_uninstall');
function WTPP_register_uninstall() { 
// handle any uninstall tasks
}

///////////////////////////////////////////////////////////////////////////////////////
// define globals that last the current request cycle

global $wtpp_type;
$wtpp_type = 0;

global $wp;
global $current_user;


///////////////////////////////////////////////////////////////////////////////////////
//should always be lower case but well to be sure to be sure

define("WTPP_FILE" ,  __FILE__ );

define("WTPP_CURRENT_URL",        strtolower( $_SERVER['REQUEST_URI'] ) );

define("WTPP_SERVER_NAME",        strtolower( $_SERVER['SERVER_NAME'] ) );

define("WTPP_PLUGINS_URL" ,       plugins_url( '/', __FILE__ ) );

define("WTPP_PLUGIN_DIR_URL" ,    plugin_dir_url( __FILE__ ) );

define("WTPP_PLUGIN_NAME" ,       plugin_basename( __FILE__ ) );

define("WTPP_PLUGIN_DIRPATH" ,    plugin_dir_path( __FILE__ ) );

define("WTPP_PLUGIN_DIR",         trailingslashit( plugin_dir_path( __FILE__ ) ));


//
///////////////////////////////////////////////////////////////////////////////////////


/** **************************************************************************************************** */
$kkpp = "running";

if (!class_exists("WTPP_Plugin_Profiler")) :

    class WTPP_Plugin_Profiler {

        public $wtpp_do_profiling = false;

        public $wtpp_show_plugins = false;
        public $wtpp_show_debug = false;

        public $plugin_name;

        public $start_timer;
        public $end_timer;
        public $difference_timer;

        public $wtpp_type;

        public $wtpp_allplugins;


        ///////////////////////////////////////////////////////////////////////////////////////

        function __construct() {    

            // set this for now - though we currently dont really use it
            $this->plugin_name = WTPP_PLUGIN_NAME;

            $this->wtpp_type  = 0 ;



            // include plugin modules only when relevant
            if ( defined( 'DOING_CRON' ) || $this->check_uri( WTPP_CURRENT_URL,'wp-cron.php') ) 
            {

                $this->wtpp_type = 1;   // doing CRON.
                // dont do any hacking if we are in the CRON 
                // any plugin could be needing access to anything at that stage
                return;

            }
            elseif ( wp_is_json_request() )
            {
                // what do we need for json calls
                
                $this->wtpp_type = 2;   // doing JSON / REST.



            }
            elseif ( wp_doing_ajax() || $this->check_uri(WTPP_CURRENT_URL,'admin-ajax.php') )
            {

                $this->wtpp_type = 3;   // doing AJAX resuest.



            }
            elseif ( is_admin() ) {
                $this->wtpp_type = 4;   // doing admin area request.


            }
            else
            {

                $this->wtpp_type = 5;
 
            }

           
            //Register hook to capture end of mp load point
            add_action ('muplugins_loaded' , array( &$this, 'wtpp_muplugins_loaded' )  );

            
            //Register a filter to intercept the active_plugins list
            add_filter( 'option_active_plugins', array( &$this, 'wtpp_option_active_plugins' )  );


            // do_action( 'plugins_loaded' );
            add_action ('plugins_loaded' , array( &$this, 'wtpp_plugins_loaded' )  );


            //do_action( 'wp_footer' );
            add_action ( 'wp_footer', array( &$this, 'wtpp_wp_footer' ) , 9999 );

        }


        ///////////////////////////////////////////////////////////////////////////////////////
        function wtpp_muplugins_loaded()
        {
            $this->start_timer = $this->wtpp_milliseconds();
        }


        ///////////////////////////////////////////////////////////////////////////////////////
        function wtpp_plugins_loaded()
        {
            $this->end_timer = $this->wtpp_milliseconds();
            $this->difference_timer = $this->end_timer - $this->start_timer;
            $atime = $this->difference_timer;

        }

        function wtpp_milliseconds() {
            $mt = explode(' ', microtime());
            return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
        }

      
        ///////////////////////////////////////////////////////////////////////////////////////
        function wtpp_option_active_plugins( $active_plugins)
        {

            if ( !$this->wtpp_do_profiling )
            {
                    return $active_plugins;
            }



            $this->wtpp_allplugins = $active_plugins;

            $really_active_plugins = array();


            // iterate the entire active plugin loop and work out which ones should be killed and when

            foreach ( $active_plugins as $plugin )
            {

                $isok = true;
                $exists = false;

                // if ( WTPP_CURRENT_URL === '/welcome/' ) 
                // {

                //      if ( array_key_exists( $plugin ,  $this->goodplugins ) )
                //     {
                //         if ( $this->goodplugins[$plugin] === "0" )
                //         {
                //             $isok = false;
                //         }

                //     }

                // }


                if ( WTPP_CURRENT_URL === '/wp-admin/admin-ajax.php' ) 
                {

                    // "molongui-authorship-pro/molongui-authorship-pro.php" => "1" ,
                    // "molongui-authorship/molongui-authorship.php" => "1" ,

                    if ( isset( $_POST['action'] ) )
                    {

                        if (  $_POST['action'] == 'activity_filter' )
                        {

                            if ( $plugin === 'molongui-authorship-pro/molongui-authorship-pro.php')
                            {

                                // if ( function_exists('wt_log_me' ) && defined('WP_DEBUG') && WP_DEBUG === true )  {
                                //     wt_log_me( 'KILL: ' . WT_UNID . '::' . $plugin );
                                // }

                               $isok = false;
                            }
                            else if ( $plugin === 'molongui-authorship/molongui-authorship.php')
                            {

                                // if ( function_exists('wt_log_me' ) && defined('WP_DEBUG') && WP_DEBUG === true )  {
                                //     wt_log_me( 'KILL: ' . WT_UNID . '::' . $plugin );
                                // }

                                $isok = false;
                            }

         
                        }

                    }

                }

                if ($isok)
                {
                    $really_active_plugins[] = $plugin;
                }
                     

            }

            
            return $really_active_plugins;
        
        }
        

        function wtpp_wp_footer( )
        {

            return ;

            if ( ! $this->wtpp_show_debug )
            {
                return;
            }

            // hide for any sort of ajax or rest queries
            if ( $this->wtpp_type < 5 )
            {
                return;
            }

?>
<div>
    Plugin Load Time: <?php echo $this->difference_timer ?>
    Raw Url: <?php echo WTPP_CURRENT_URL ?>
    type: <?php echo $this->wtpp_type ?>

<?php 
     $this->wtpp_plugin_list( $this->wtpp_show_plugins );
?>
</div>
<?php
    
        }       

        function wtpp_plugin_list ( $show )
        {
            if (! $show )
            {
                return;
            }

            echo '<br />';
            echo '============================================================================<br />';
    
            foreach ( $this->wtpp_allplugins as $plugin )
            {
                echo '"' . $plugin . '" =&gt; "1" , ' . '<br />';
            }
    
            echo '============================================================================<br />';

        }


        ///////////////////////////////////////////////////////////////////////////////////////
        function check_uri($wherearewe, $url) {
            if ( strpos($wherearewe, $url) === 0)
            {
                return TRUE;
            }
            return FALSE;
        }

  ///////////////////////////////////////////////////////////////////////////////////////
        // define goodness array for now

        public $wtgoodplugins = array(
            
            );
            

        public $wtkillplugins = array(
        
        );


////////////////////////////////////////////////////////////////////////////////////////////////////////

    }

endif;
 
///////////////////////////////////////////////////////////////////////////////////////
// define a global to reference our master plugin
global $WTPP_plugin_profiler;

///////////////////////////////////////////////////////////////////////////////////////
// instantiate only once per request 
if (class_exists("WTPP_Plugin_Profiler") && !$WTPP_plugin_profiler) {

    $WTPP_plugin_profiler = new WTPP_Plugin_Profiler();   

}   

// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////





// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
//  westa low level logging handler
function wt_shutdown()
{



  if ( function_exists('wt_log_me' ) && defined('WP_DEBUG') && WP_DEBUG === true && defined( 'WT_SHUTDOWN') )  {

    $time_start = WT_START;
    $time_end = microtime(true); 
    $diff = $time_end -  $time_start;

    // Break the difference into seconds and microseconds
    $sec = intval($diff);
    $micro = $diff - $sec;

    // Format the result as you want it
    // $final will contain something like "00:00:02.452"
    $final = strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.3f', $micro));

    wt_log_me('ENDED: ' . WT_UNID . ' :: ' .$time_start . ' || ' . $time_end  . ' [ ' . $final . ' ] ' );


    if ( defined('SAVEQUERIES' ) )
    {
      global $wpdb;  
      wt_log_me( 'database: ' );
      wt_log_me( $wpdb->queries );
    }

  }


  
}
add_action ( 'shutdown' , 'wt_shutdown' , 99999 );

// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////////////////////////////////////////






// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// plugin list = MUST BE COMPILED TO USE THIS SITE

$wtallplugins = array(

    // "worker/init.php" => "1" ,
    // "gravityperks/gravityperks.php" => "1" ,
    // "gravityforms/gravityforms.php" => "1" ,
    // "activecampaign-subscription-forms/activecampaign.php" => "1" ,
    // "admin-menu-editor/menu-editor.php" => "1" ,
    // "advanced-custom-fields-pro/acf.php" => "1" ,
    // "advanced-custom-fields/acf.php" => "1" ,
    // "akismet/akismet.php" => "1" ,
    // "amazon-s3-and-cloudfront-pro/amazon-s3-and-cloudfront-pro.php" => "1" ,
   

);


?>