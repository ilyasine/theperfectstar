<?php 
    namespace WTS_EAE\Pro\Modules\ElementorFormAction;

    class Module{

        private static $_instance = null;

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        public function __construct(){
            add_action( 'elementor_pro/forms/fields/register', [ $this , 'add_new_form_field']);
            add_action( 'elementor_pro/forms/actions/register', [ $this ,'add_new_sendy_form_action' ]);
        }

        function add_new_sendy_form_action( $form_actions_registrar ) {

            include_once( __DIR__ .  '/form-actions/form-action.php' );
        
            $form_actions_registrar->register( new Ping_Action_After_Submit() );

        }

        function add_new_form_field( $form_fields_registrar ) {

            require_once( __DIR__ . '/form-field/taxonomy.php' );
        
            $form_fields_registrar->register( new Taxonomy_Field() );
        
        }

    }

?>