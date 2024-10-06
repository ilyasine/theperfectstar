<?php 
    namespace WTS_EAE\Pro\Modules\VideoGallery;

    use WTS_EAE\Base\Module_Base;

    class Module extends Module_Base{
        public function get_widgets(){
            return [
                'VideoGallery',
            ];
        }
        public function get_name(){
            return 'eae-video-gallery';
        }
        public function get_title(){
            return esc_html__('Video Gallery','wts-eae');
        }
    }
?>