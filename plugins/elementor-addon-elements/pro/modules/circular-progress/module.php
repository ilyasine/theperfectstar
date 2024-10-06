<?php 

namespace WTS_EAE\Pro\Modules\CircularProgress;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {
    
    public function get_widgets(){
        return [
            'CircularProgress',
        ];
    }

    public function get_name(){
        return 'eae-circular-progress';
    }

    public function get_title(){
        return __( 'Circular Progress','wts-eae');
    }
}
?>
