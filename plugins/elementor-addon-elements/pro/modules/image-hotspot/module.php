<?php 

namespace WTS_EAE\Pro\Modules\ImageHotspot;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {
    
    public function get_widgets(){
        return [
            'ImageHotspot',
        ];
    }

    public function get_name(){
        return 'eae-image-hotspot';
    }

    public function get_title(){
        return __( 'Image Hotspot','wts-eae');
    }
}
?>
