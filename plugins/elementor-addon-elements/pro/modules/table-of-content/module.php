<?php 
namespace WTS_EAE\Pro\Modules\TableOfContent;
use WTS_EAE\Base\Module_Base;

class Module extends Module_Base{

    public function get_widgets()
    {
        return [
            'TableOfContent',
        ];
    }

    public function get_name(){
        return 'eae-table-of-content';
    }

    public function get_title(){
        return __('Table Of Content','wts-eae');
    }
}

?>