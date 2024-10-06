<?php 

namespace WTS_EAE\Pro\Modules\AdvancedList;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base{
    
    public function get_widgets()
    {
        return [
            'AdvancedList',
        ];
    }

    public function get_name(){
        return 'eae-advanced-list';
    }

    public function get_title(){
        return esc_html__('Advanced List','wts-eae');
    }
}

?>