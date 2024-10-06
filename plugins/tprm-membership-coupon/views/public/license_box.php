<?php 

$kmc_helper = TPRM_membership_helper::get_instance();
$english_flag = TPRM_MEM_CO_IMG . 'en-flag.svg'; 
$french_flag = TPRM_MEM_CO_IMG . 'fr-flag.svg';

$student_id = get_current_user_id();

/* if(is_active_student(get_current_user_id())){
	echo 'active student';
}else{
	echo 'not active student';
} */

?>

<div class="box-header">
<h2 class="srctitle" id="welcome-text"><?php _e('Welcome to your tepunareomaori account activation space.','TPRM_-membership-coupon');?></h2>
<div class="language-switcher">
    <span class="language-switch" data-language="en"><img src="<?= esc_attr($english_flag)?>" alt="English"></span>
    <span class="language-switch" data-language="fr"><img src="<?= esc_attr($french_flag)?>" alt="French"></span>
</div>
</div>

<div class="box-container">
        <div class="box TPRM_-access">
            <h3 class="TPRM_-access-desc" id="TPRM_-access-desc">          
            <?php _e('To fully enjoy this educational resource, we invite you to activate your account by clicking on the « Activate Now » button and proceed with the online payment.' , 'TPRM_-membership-coupon') ?>
            </h3>
            <?php
            $access_product_id = $kmc_helper->TPRM_multilang_product()[$kmc_helper->current_lang]['access'];
           ?> <span id="activate-btn"> <?php  echo do_shortcode('[add_to_cart_form id="' . $access_product_id . '" text="' . __("Activate now","TPRM_-membership-coupon") . '" ]');?>
           </span>
        </div>

        <div class="box TPRM_-license">
            <form method="POST" class="TPRM_FormLicense" action="">
                <input type="hidden" name="subId" value="<?php echo esc_attr($subscriptionID);?>" />
                <input type="hidden" name="prId" value="<?php echo esc_attr($access_product_id);?>" />
                <input type="hidden" name="sId" value="<?php echo esc_attr($student_id);?>" />
                <h3 class="TPRM_-license-desc" id="license-desc"><?php _e('If you have a license acquired on our platform or from a partner library, please activate it by entering the license code in the field below.','TPRM_-membership-coupon');?></h3>
                <div class="TPRM_Erros" id="TPRM_Erros"></div>
                <div class="formwrapTPRM_">
                    <div class="license-paste">
                        <input class="licenseinpt" type="text" placeholder="<?php esc_attr_e('License Code','TPRM_-membership-coupon');?>" id="license-placeholder">
                        <i class="bb-icon-l bb-icon-paste"></i>
                    </div>
                    <input class="licensebtn" type="submit" value="<?php esc_attr_e('Activate','TPRM_-membership-coupon');?>" id="activate-license-btn">
                </div>
                <div>
                    <a href="<?php echo home_url('/contact-it/') ?>" target="_blank" class="licenceperdu" id="lost-license-link"><?php _e('I have lost my license code.','TPRM_-membership-coupon');?></a>
                </div>
            </form>
        </div>
</div>


