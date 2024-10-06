<?php 
    namespace WTS_EAE\Pro\Modules\GoogleReviews;

    use WTS_EAE\Base\Module_Base;

    class Module extends Module_Base{

        public function get_widgets(){
            return [
                'GoogleReviews',
            ];
        }

        public function get_name(){
            return 'eae-google-reviews';
        }

        public function get_title(){
            return __('Google Reviews','wts-eae');
        }
    }
?>