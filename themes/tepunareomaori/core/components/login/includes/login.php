<?php 

add_filter('lost_password_html_link', 'classroom_code_section');

function classroom_code_section($classroom_code) {
    ob_start();
    $classroom_code_nonce = wp_create_nonce('classroom_code_nonce');
    ?>
    <div class="classroom-code-section">
        <div class="classroom-code-section-divider">
            <span><?php _e('Or', 'tprm-theme') ?></span>
        </div>

        <div class="submit-classroom-code">
            <input type="text" name="classroom_code" id="classroom_code" placeholder="<?php esc_attr_e('Classroom Code', 'tprm-theme') ?>">
            <button type="button" data-security="<?php esc_attr_e($classroom_code_nonce); ?>" class="button button-primary button-large" id="classroom_code_submit"><?php _e('GO', 'tprm-theme') ?></button>
        </div>      

        <div id="login_error" style="display: none;" class="notice classroom-code-notice notice-error"></div>
    </div>
    <?php
    $classroom_code = ob_get_clean();

     return $classroom_code;
}

add_filter('login_headertext', 'TPRM_login_headertext', 999, 1);

function TPRM_login_headertext($login_header_text) {
    $login_header_text = __('Welcome to ', 'tprm-theme');
    return $login_header_text;
}


//add_action('login_head', 'login_type', 151 );

function login_type(){
    $html = "<script>";
    $html .= "    jQuery('.login #login_error a').remove();";
    $html .= "    jQuery('.login form .lostmenot').remove();"; // remove lost password button

    // Inject the welcome message and tabs
    $html .= "    jQuery('.login-heading').after(`";
    $html .= "            <div class='login_type'>Text Password  |  Picture Password";
    $html .= "                <ul>";
    $html .= "                    <li><a href='#text-password'>Text Password</a></li>";
    $html .= "                    <li><a href='#picture-password'>Picture Password</a></li>";
    $html .= "                </ul>";
    $html .= "                <div id='text-password'>";
    $html .= "                    <div class='user-pass-wrap'>";
    $html .= "                        <label for='user_pass'>" . esc_html__( 'Password', 'tprm-theme' ) . "</label>";
    $html .= "                        <div class='wp-pwd'>";
    $html .= "                            <input type='password' name='pwd' id='user_pass' class='input password-input' value='' size='20' autocomplete='current-password' spellcheck='false' required='required' />";
    $html .= "                            <button type='button' class='button button-secondary wp-hide-pw hide-if-no-js' data-toggle='0' aria-label='" . esc_attr__( 'Show password', 'tprm-theme' ) . "'>";
    $html .= "                                <span class='dashicons dashicons-visibility' aria-hidden='true'></span>";
    $html .= "                            </button>";
    $html .= "                        </div>";
    $html .= "                    </div>";
    $html .= "                </div>";
    $html .= "                <div id='picture-password'>";
    $html .= "                    <p>Picture Password</p>";
    $html .= "                </div>";
    $html .= "         </div>";
    $html .= "    );";

    // Append support information
    $html .= "    jQuery('#login').append(`";
    $html .= "        <p class='support-div'>";
    $html .= "            " . esc_html__( 'If you are having trouble logging in, please contact our support team at support@tepunareomaori.com', 'tprm-theme' ) . "";
    $html .= "        </p>`";
    $html .= "    );";

    // Initialize jQuery tabs
    $html .= "    jQuery('.login_type').tabs();";
    $html .= "</script>";
}