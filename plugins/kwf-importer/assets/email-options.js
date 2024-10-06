jQuery(document).ready(function ($) {
	'use strict';

	$('#TPRM_importer_email_option_remove_upload_button').click(function () {
		var data = {
			'action': 'TPRM_importer_mail_options_remove_attachment',
			'security': email_options.security,
		};

		$.post(ajaxurl, data, function (response) {
			location.reload();
		});
	});

	$('#TPRM_importer_email_template_remove_upload_button').click(function () {
		$('#email_template_attachment_file').val('');
		$('#email_template_attachment_id').val('');
	});

	$('#send_test_email').click(function () {
		var data = {
			'action': 'TPRM_importer_send_test_email',
			'security': email_options.security,
		};

		$.post(ajaxurl, data, function (response) {
			alert(email_options.success_message);
		});
	})
});;
