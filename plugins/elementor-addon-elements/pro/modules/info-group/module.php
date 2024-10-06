<?php 

namespace WTS_EAE\Pro\Modules\InfoGroup;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base{
    
    public function get_widgets(){
        return [
            'InfoGroup',
        ];
    }  

    public function get_name(){
        return 'eae-info-group';
    } 

    public function get_title(){
        return __('Info Group','wts-eae');
    } 
}

?>
