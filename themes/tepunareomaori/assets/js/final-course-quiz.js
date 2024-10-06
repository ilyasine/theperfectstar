jQuery(document).ready(function ($) {
    //$('.lms-quiz-status-icon').eq(0).remove();
    // Disable grid view
    window.BBLMS.switchLdGridList = function () { };
 
    $('#course-dir-list #courses-list #see-final-quiz').magnificPopup({
        type: 'inline',
        fixedContentPos: true,
        fixedBgPos: true,
        closeBtnInside: true,
        closeOnBgClick: false,
        closeOnContentClick: false,
        removalDelay: 300,
        mainClass: 'mfp-fade',
    });


    $('.see-final-quiz-head #print_pdf').on('click', function (e) {

        var button = $(this);
        var security = button.data('security');
        var final_quiz = button.data('final_quiz');
        var course_name = button.data('course_name');      
        var generatedFilename; // Variable to store the dynamically generated filename
        var generatedFilePath; // Variable to store the PDF file path

        e.preventDefault();

        button.addClass('disabled');

        NProgress.start();
        NProgress.set(0.4);

        var interval = setInterval(function () {
            NProgress.inc();
            }, 1000);   
        clearInterval(interval);

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'print_final_quiz',
                security: security,
                payload: 'print_final_quiz',
                final_quiz: final_quiz,
                course_name: course_name,
            },
            type: 'post',
            dataType: 'json', // Specify that the response is JSON
            success: function (result, textstatus) {

                if (result.download_link) {
                    // Use a regular expression to extract the filename from the download link
                    var match = result.download_link.match(/\/([^/]+\.pdf)$/);
                    if (match) {
                        generatedFilename = match[1]; // Extracted filename
                    } else {
                        console.log('Failed to extract the filename from the download link.');
                        return;
                    }

                    // Store the file path from the response
                    generatedFilePath = result.pdf_file_path;

                  // Create a temporary hidden anchor element for the download
                    var tempLink = document.createElement('a');
                    tempLink.style.display = 'none';
                    tempLink.href = result.download_link;
                    tempLink.setAttribute('download', ''); // Set the "download" attribute                  
                    document.body.appendChild(tempLink);
                    tempLink.click(); // Simulate a click on the anchor element to trigger the download
                    document.body.removeChild(tempLink); // Remove the temporary anchor element


                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + result.quiz_printed_message + '</div>',
                            'success',
                            null,
                            true
                        ]
                    );

                    NProgress.done();

                    button.removeClass('disabled');

                    // Set a timeout to delete the file after a specified time (e.g., 10 seconds)
                    setTimeout(function () {
                        console.log('Attempting to delete file js:', generatedFilePath);
                    
                        // Make an AJAX request to delete the file on the server
                        $.ajax({
                            url: ajaxurl,
                            data: {
                                action: 'delete_pdf_file',
                                filename: generatedFilename, // Use the extracted filename
                                pdf_file_path: generatedFilePath, // Pass the file path
                            },
                            type: 'post',
                            success: function (deleteResult, deleteTextstatus) {
                                //console.log('File deletion response:', deleteResult);
                                //console.log('File deleted from the server.');
                            },
                            error: function (deleteResult) {
                                //console.log('File deletion error:', deleteResult);
                                //console.log('Failed to delete the file from the server.');
                            },
                        });
                    }, 10000); // wait 10 seconds before deleting the file
                } else {
                    console.log('No download link found in the response.');
                }        

            },
            error: function (result) {
                console.log(result);
                console.log('fail');
            },
        });
    
    });
  

    $('#course-dir-list #courses-list button#enable-final-quiz-btn').on('click', function (e) {

        var security = $(this).data('security');
        var final_quiz = $(this).data('final_quiz');
        var button = $(this);
        //var statusDiv = button.siblings('#final-quiz-status');
        var statusDiv = $(this).closest('#enable-final-quiz-container').find('#final-quiz-status');
        var ld_group_id = $(this).data('group_id');
        var teacher_id = $(this).data('teacher_id');
        var course_name = $(this).data('course_name');
        //var feedback = $(this).data('balloon');
        e.preventDefault();

        NProgress.start();
        NProgress.set(0.4);

        var interval = setInterval(function () {
            NProgress.inc();
            }, 1000);   
        clearInterval(interval);

        //final-quiz
        //console.log(final_quiz);
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'enable_final_quiz',
                security: security,
                payload: 'enable_final_quiz',
                final_quiz: final_quiz,
                ld_group_id: ld_group_id,
                teacher_id: teacher_id,
                course_name: course_name,
            },
            type: 'post',
            dataType: 'json', // Specify that the response is JSON
            success: function (result, textstatus) {

                NProgress.done();

                if (result.quiz_status == 'quiz_enabled') {
                    // Enabled
                    statusDiv.html(result.quiz_status_enabled);
                    button.html(result.quiz_button_disable);
                    button.attr('data-balloon', result.data_ballon_disable);
                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + result.quiz_status_enabled + '</div>',
                            'success',
                            null,
                            true
                        ]
                    );


                } else if (result.quiz_status == 'quiz_disabled') {
                    // Disabled
                    statusDiv.html(result.quiz_status_disabled);
                    button.html(result.quiz_button_enable);
                    button.attr('data-balloon', result.data_ballon_enable);
                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + result.quiz_status_disabled + '</div>',
                            'info',
                            null,
                            true
                        ]
                    );

                }                


            },
            error: function (result) {
               /*  console.log(result);
                console.log('fail'); */
            },
        });
    });
});




