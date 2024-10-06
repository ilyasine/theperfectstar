jQuery(document).ready(function ($) {

    // we create a copy of the WP inline edit post function
    const wp_inline_edit = inlineEditPost.edit;

    // and then we overwrite the function with our own code
    inlineEditPost.edit = function (post_id) {

        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        wp_inline_edit.apply(this, arguments);

        // now we take care of our business

        // get the post ID from the argument
        if (typeof (post_id) == 'object') { // if it is object, get the ID number
            post_id = parseInt(this.getId(post_id));
        }

        console.log(post_id);

       /*  if (post_id > 0) {
            // define the edit row
            const edit_row = $('#edit-' + post_id);
            const post_row = $('#post-' + post_id);

            // get the data
            const related_posts = $('.wz_tutorials_related_posts', post_row).text();
            const exclude_this_post = 1 == $('.wz_tutorials_exclude_this_post', post_row).val() ? true : false;

            // populate the data
            $(':input[name="wz_tutorials_related_posts"]', edit_row).val(related_posts);
            $(':input[name="wz_tutorials_exclude_this_post"]', edit_row).prop('checked', exclude_this_post);
        } */

        if (post_id > 0) {
            // Trigger AJAX request passing the post ID
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_quick_edit_custom_box',
                    post_id: post_id,
                    _wpnonce: manage_courses_data.nonce
                },
                success: function(response) {
                    // Update the inline edit row with the custom box HTML received from the server
                    $('#edit-' + post_id).find('input[name="course_position"]').val(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    };


    $('#bulk_edit').on('click', function (event) {
        const bulk_row = $('#bulk-edit');

        // Get the selected post ids that are being edited.
        const course_ids = [];

        // Get the data.
        const course_position = $('input[name="course_position"]', bulk_row).val();

        // Get post IDs from the bulk_edit ID. .ntdelbutton is the class that holds the post ID.
        bulk_row.find('#bulk-titles-list .ntdelbutton').each(function () {
            course_ids.push($(this).attr('id').replace(/^(_)/i, ''));
        });
        console.log(course_ids);
        // Convert all course_ids to integer.
        course_ids.map(function (value, index, array) {
            array[index] = parseInt(value);
        });

        // Save the data.
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            async: false,
            cache: false,
            data: {
                action: 'course_position_save_bulk_edit',
                course_ids: course_ids,
                course_position: course_position,
                course_position_bulk_edit_nonce: manage_courses_data.nonce
            }
        });
    });

    // JavaScript to focus on the school year input field if URL parameter is present
	document.addEventListener('DOMContentLoaded', function() {
		const urlParams = new URLSearchParams(window.location.search);
		const schoolYearParam = urlParams.get('school_year');
		if (schoolYearParam !== null) {
			const schoolYearInput = document.querySelector('#school_year');
			if (schoolYearInput !== null) {
				schoolYearInput.focus();
			}
		}
	});

});

