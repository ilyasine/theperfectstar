<?php
// teacher-email.php

// Variables passed to the template
$TeacherFirstName = isset($TeacherFirstName) ? $TeacherFirstName : '';
$TeacherLastName = isset($TeacherLastName) ? $TeacherLastName : '';
$school_name = isset($school_name) ? $school_name : '';
$teacher_username = isset($teacher_username) ? $teacher_username : '';
$TeacherPassword = isset($TeacherPassword) ? $TeacherPassword : '';
$login_url = isset($login_url) ? $login_url : wp_login_url();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('Welcome to tepunareomaori.com', 'tprm-theme'); ?></title>
</head>
<body>
    <table id="bodyTable" border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
        <tbody>
            <tr>
                <td id="bodyCell" align="center" valign="top">
                    <table class="templateContainer" border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr style="background-color: #2e9e9e; color: #fff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; border-radius: 17px; overflow: hidden;">
                                <td id="header_wrapper" style="padding: 36px 48px; display: block;">
                                    <h1 style="font-family: &quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #33b9b1; color: #fff; background-color: inherit;" bgcolor="inherit"><?php _e('Welcome to tepunareomaori.com', 'tprm-theme'); ?></h1>
                                </td>
                            </tr>                       
                            <tr>
                                <td id="templateBody" valign="top">
                                    <p><?php echo sprintf(__('Dear %s %s,', 'tprm-theme'), $TeacherFirstName, $TeacherLastName); ?></p>
                                    <p><?php echo sprintf(__('Welcome to your Computer Science journey with tepunareomaori.com offered by <span style="font-family: cursive; font-weight: bold;margin: 0 0 10px;">%s</span>.', 'tprm-theme'), $school_name); ?></p>
                                    <p><?php echo __('We are pleased to inform you that your account on tepunareomaori.com has been successfully created.', 'tprm-theme'); ?></p>
                                    <p><?php echo __('Please find below the link and log in information to your profile:', 'tprm-theme'); ?></p>
                                    <ul>
                                        <li><?php echo sprintf(__('Username: <span style="font-family: cursive; font-weight: bold;margin: 0 0 10px;">%s</span>', 'tprm-theme'), $teacher_username); ?></li>
                                        <li><?php echo sprintf(__('Password: <span style="font-family: cursive; font-weight: bold;margin: 0 0 10px;">%s</span>', 'tprm-theme'), $TeacherPassword); ?></li>
                                        <li><?php echo sprintf(__('Link: <a href="%s" style="font-family: cursive; color: #2e9e9e;text-decoration: none;margin: 0 0 10px;">Click here to log in</a>', 'tprm-theme'), $login_url); ?></li>
                                    </ul>
                                    <p><?php echo sprintf(__('We sincerely thank you for your trust and cooperation. If you have any additional questions or concerns, please do not hesitate to contact us or reach out to your School Leader at <span style="font-family: &quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-weight: bold;margin: 0 0 10px;">%s</span>.', 'tprm-theme'), $school_name); ?></p>
                                    <p><?php echo __('Sincerely,', 'tprm-theme'); ?></p>
                                    <p><strong style="color: #1c5cb2; font-size: 20px;">tepunareomaori.com</strong></p>
                                </td>
                            </tr>
                            <!-- Footer Section -->
                            <tr>
                                <td id="templateFooter" valign="top">
                                    <div class="woo_email_footer">
                                        <p class="TPRM_footer_border" style="border-top: solid 0.5px #dee0e1; font-size: 1px; width: 100%; margin: 40px auto 15px auto !important;"></p>         
                                        <p class="TPRM_copyright_footer" style="font-family: system-ui,Segoe UI,sans-serif; font-size: 11px; line-height: 1.6; text-align: center; color: #939598;">
                                            <?php echo __('This email was sent to you by ', 'tprm-membership-coupon'); ?>
                                            <a href="<?php echo esc_url(home_url()); ?>" target="_blank" style="color: #2e9e9e !important" >tepunareomaori</a>
                                        </p>
                                        <div class="footer-bg" style="background-image: url('<?php echo esc_attr(TPRM_IMG_PATH) . 'koding-cool.png'; ?>'); background-position: center; background-repeat: no-repeat; background-size: contain; height: 30px;"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
