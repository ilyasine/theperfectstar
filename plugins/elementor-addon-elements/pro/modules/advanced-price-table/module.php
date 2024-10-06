<?php 
namespace WTS_EAE\Pro\Modules\AdvancedPriceTable;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base{

    public function get_widgets(){
        return [
            'AdvancedPriceTable',
        ];
    }

    public function get_name(){
        return 'eae-advanced-price-table';
    }

    public function get_title(){
        return __('Advanced Price Table' , 'wts-eae');
    }
}
?>