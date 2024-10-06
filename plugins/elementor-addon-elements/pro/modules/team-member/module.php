<?php 
    namespace WTS_EAE\Pro\Modules\TeamMember;

    use WTS_EAE\Base\Module_Base;

    class Module extends Module_Base{
        public function get_widgets(){
            return [
                'TeamMember',
            ];
        }
        public function get_name(){
            return 'eae-team-member';
        }
        
        public function get_title(){
            return esc_html__('Team Member','wts-eae');
        }
    }
?>