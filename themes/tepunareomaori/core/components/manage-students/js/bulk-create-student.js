jQuery(document).ready(function ($) {
    var current_fs, next_fs, previous_fs; // fieldsets
    var left, opacity, scale; // fieldset properties which we will animate
    var animating; // flag to prevent quick multi-click glitches
    var originalFileSubmitTitle = $('fieldset#file_submit').find('.fs-title').text();
    var originalFileSubmitSubtitle = $('fieldset#file_submit').find('.fs-subtitle').text();
    var school_id, selectedClassroom, selectedClassroomName, selectedClassroomslug, file;
    checkNextButtonState();

    //curriculum level select
    $("#classroom_select").select2();
    $('#classroom_select').one('select2:open', function(e) {
        $('input.select2-search__field').prop('placeholder', MST_data.choose_classroom_select);
    });

    $('fieldset#classroom_setup').find('#classroom_select').on('change', function() {
        checkNextButtonState();
    });

    function resetForm() {
        $('form#bulkmultistepsform').trigger("reset");
        $('fieldset#classroom_setup').find('#classroom_select').val('').trigger('change');
        $('fieldset#file_submit').find('.template-file-text').show();
        $('fieldset#file_submit').find('.file_upload_Container').show();
        $('fieldset#file_submit').find('#submit-students-file').show();
        $('fieldset#file_submit').find('input.previous').show();
    }

    function resetToFirstFieldsetBulkGlobal() {
        // Hide all fieldsets
        $('fieldset').hide();

        // Show the first fieldset and style it correctly
        $('#template_file').show();
        $('#template_file').css({
            'display': 'flex',
            'left': '0%',
            'opacity': 1,
            'transform': 'scale(1)',
            'position': 'absolute'
        });

        $('fieldset').css({
            'transform': 'scale(1)',
            'opacity': 1,
            'left': '0%'
        });
    }

    function resetToFirstFieldsetBulk() {
        if (animating) return false;
        animating = true;

        var current_fs = $('fieldset:visible');
        //var first_fs = $('fieldset').first();
        var first_fs = $('fieldset#template_file');

        // Reset progressbar 
        $("#progressbar li").removeClass("active");
        $("#progressbar li").first().addClass("active");

        // Reset styles for all fieldsets
        $('fieldset').css({
            'transform': 'scale(1)',
            'opacity': 1,
            'left': '0%'
        });

        // Show the first fieldset with animation 
        first_fs.show();
        first_fs.css('display', 'flex');
        current_fs.animate({ opacity: 0 }, {
            step: function (now, mx) {
                // As the opacity of current_fs reduces to 0 - stored in "now" 
                // 1. Scale first_fs from 80% to 100% 
                var scale = 0.8 + (1 - now) * 0.2;
                // 2. Take current_fs to the right(50%) - from 0% 
                var left = ((1 - now) * 50) + "%";
                // 3. Increase opacity of first_fs to 1 as it moves in 
                var opacity = 1 - now;
                current_fs.css({ 'left': left });
                first_fs.css({ 'transform': 'scale(' + scale + ')', 'opacity': opacity });
            },
            duration: 800,
            complete: function () {
                current_fs.hide();
                animating = false;
            },
            easing: 'easeInOutBack'
        });

    }

    var templateFile = $('fieldset#template_file');
    school_id = $(templateFile).find('#student_school').data('school_id');

    $('fieldset#classroom_setup').on('change', '#classroom_select', function () {
        var selectedOption = $(this).find('option:selected');
        selectedClassroom = selectedOption.val();
        selectedClassroomName = selectedOption.data('name');
        selectedClassroomslug = selectedOption.data('slug');
        $('fieldset#file_submit').find('.selected_classroom_text span').text(selectedClassroomName);
        checkNextButtonState();
    });
    

    function checkNextButtonState() {
        selectedClassroom = $('fieldset#classroom_setup').find('#classroom_select').val();
        var $nextButton = $('fieldset#classroom_setup').find('input.next');
        var fileInput = $('fieldset#file_submit').find('#excel_file');
        var $submitButton = $('fieldset#file_submit').find('#submit-students-file');
        if (fileInput[0]) {
            file = fileInput[0].files[0];
        }
       
    
        if (selectedClassroom == '') {
            $nextButton.addClass('disabled');
        } else{
            $nextButton.removeClass('disabled');
        }

        if (file) {         
            $submitButton.removeClass('disabled');
        } else{
            $submitButton.addClass('disabled');
        }
    }

    var fileInput = $('fieldset#file_submit').find('#excel_file');
    file = fileInput[0].files[0]; // Get the first file
    $(fileInput).on('change', function () {
        checkNextButtonState();
        var file = this.files[0];
        if (file) {
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>' + MST_data.file_uploaded + file.name + '</div>',
                    'success',
                    null,
                    true,
                ]
            );
        }     
    });


    $("#submit-students-file").click(function (e) {
        current_fs = $(this).closest('fieldset');
        var security = $(this).data('security'); 
    
        var fileInput = $('fieldset#file_submit').find('#excel_file');
        file = fileInput[0].files[0]; // Get the first file
    
        if (current_fs.attr('id') === 'file_submit') {
            e.preventDefault();
    
            if (!school_id) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MST_data.missing_school_id + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return;
            }
    
            if (!selectedClassroom || !selectedClassroomslug) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MST_data.no_classroom_selected_for_import + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return;
            }
    
            if (!file) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MST_data.no_file_selected_to_import + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return;
            }
    
            $('.kwf-preloader').fadeIn();
            $('.kwf-preloader #tepunareomaori-preloader').fadeIn();
            current_fs.find('.template-file-text').hide();
            current_fs.find('.file_upload_Container').hide();
            current_fs.find('.classrooms-notice').hide();
            current_fs.find('.template-file-text').hide();
            title = current_fs.find('.fieldset-header .fs-title');
            subtitle = current_fs.find('.fieldset-header .fs-subtitle');
            fieldset_body = current_fs.find('.fieldset-body');
            fieldset_footer = current_fs.find('.fieldset-footer');
            fieldset_notice = fieldset_body.find('.notice');
    
            // Create FormData object
            var formData = new FormData();
            formData.append('action', 'upload_import_file');
            formData.append('security', security);
            formData.append('school_id', school_id);
            formData.append('selectedClassroom', selectedClassroom);
            formData.append('selectedClassroomslug', selectedClassroomslug);
            formData.append('file', file);

            //Uploading and analyzing Excel File
            title.text(MST_data.submitting_file);
            subtitle.text(MST_data.uploading_analyzing_excel_file);
            fieldset_footer.css('visibility', 'hidden');

            $.ajax({
                url: ajaxurl,
                data: formData,
                type: 'post',
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (result) {

                    if (result.success) {
            
                        var totalRows = result.data.totalRows || 0;
                        if (totalRows) {
                            // Creating Students
                            title.text(MST_data.creating_students_header);
                            subtitle.text(MST_data.students_being_created_message);
                            $('.proccessed-data').fadeIn();
                            $('.progress-container').fadeIn();
                            $('.proccessed-data').find('#total_data').text(totalRows);
                            $('.proccessed-data').find('#imported-students-classroom').text(selectedClassroomName);
                        }
                        var excelfile = result.data.filePath;

                        //New Action for processing
                        processExcelBatch(2, 0, 0); // Start from row 2 ( as first row contains just the heading )

                        function processExcelBatch(startRow, processedRows, skippedRows) {
                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'process_excel_rows',
                                    security: security,
                                    selectedClassroom: selectedClassroom,
                                    startRow: startRow,                  
                                    school_id: school_id,
                                    excelfile: excelfile,
                                    processedRows: processedRows,
                                    skippedRows: skippedRows,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result) {
                                    console.log(' processExcelBatch ' , result);
                                    if (result.success) {
                                        // Update your progress indicators
                                        processedRows = result.data.processedRows || 0;
                                        skippedRows = result.data.skippedRows || 0;
                                        var nextStartRow = result.data.nextStartRow || 0;

                                        // Update processed data and total rows
                                        $('#process_data').text(processedRows);
                                                                                   
                                        // Update progress bar
                                        var progressPercentage = Math.min((processedRows / totalRows) * 100, 100); // Ensure it doesn't exceed 100%
                                        $('.progress-bar').css('width', progressPercentage + '%').text(Math.round(progressPercentage) + '%');
                
                                        /* console.log('totalRows : ' , totalRows);
                                        console.log('skippedRows : ' , skippedRows);
                                        console.log(' progressPercentage : ' , progressPercentage);
                                        console.log(' nextStartRow : ', nextStartRow); */

                                        $('.skipped_student_notice').find('#ignored_students_count').text(skippedRows);
                                        
                                        if (skippedRows > 0) {                           
                                            $('.skipped_student_notice').fadeIn();
                                        }
                
                                        if (nextStartRow <= totalRows + 1) { // Use totalRows from response
                                            // Process the next batch
                                            processExcelBatch(nextStartRow, processedRows, skippedRows);
                                        } else {
                                            $('.kwf-preloader #tepunareomaori-preloader').hide();
                                            console.log('Import completed.');
                                            $('.progress-bar').css('animation', 'none');
                                            title.text(MST_data.bulk_student_created_title);
                                            subtitle.text(MST_data.bulk_student_created_subtitle);
                                            var classroom_students_name = result.data.classroom_students_name;
                                            var classroom_students_tab = result.data.classroom_students_tab;
                                            if (skippedRows == 0) {
                                                fieldset_notice.html( processedRows + ' ' + MST_data.processed_students + ', ' + skippedRows  + ' ' + MST_data.skipped_students).addClass('success');
                                            } else {
                                                fieldset_notice.html('<span class="success"> ' + processedRows + ' ' + MST_data.processed_students + '</span>, <span class="error"> ' + skippedRows  + ' ' + MST_data.skipped_students + '</span>');
                                            }
                                            var newButton = '<a href="' + classroom_students_tab + '" class="button new_student_link" target="_blank" rel="noopener noreferrer">' + classroom_students_name + ' <span class="bb-icon-l bb-icon-external-link"></span></a>';
                                           
                                            fieldset_notice.after(newButton);
                                            fieldset_notice.fadeIn();
                                            fieldset_footer.css('visibility', 'visible');
                                            fieldset_footer.find('#submit-students-file').hide();
                                            $("#submit-new-students-file").fadeIn()
                
                                            // Attach event to the new "Create New student" button
                                            $("#submit-new-students-file").click(function () {
                                                // Reset form or redirect to student creation page
                                                resetForm();
                                                resetToFirstFieldsetBulk();
                                                fieldset_notice.hide();
                                                title.removeClass('error').text(originalFileSubmitTitle);
                                                subtitle.removeClass('error').text(originalFileSubmitSubtitle);                                             
                                                current_fs.find('.template-file-text').fadeIn();
                                                current_fs.find('.classrooms-notice').fadeIn();
                                                current_fs.find('.file_upload_Container').fadeIn();
                                                fieldset_footer.find('#submit-students-file').fadeIn();
                                                current_fs.find('input.previous').fadeIn();
                                                current_fs.find('input.back').fadeOut();
                                                current_fs.find('.new_student_link').remove();
                                                $('.proccessed-data').fadeOut();
                                                $('.progress-container').fadeOut();
                                                $('.skipped_student_notice').fadeOut();
                                                $(this).hide();
                                                //Reset Progress
                                                $('#process_data').text(0);
                                                $('.progress-bar').css('width', '0%').text('0%');
                                                $('.progress-bar').css('animation', 'f 2s linear infinite');
                                                checkNextButtonState();
                                            });
                
                                            $('.manage-students button.all-students').on('click', function () {
                                                // Trigger click event on the "Create New student" button
                                                resetForm();
                                                resetToFirstFieldsetBulkGlobal();
                                                fieldset_notice.hide();
                                                title.removeClass('error').text(originalFileSubmitTitle);
                                                subtitle.removeClass('error').text(originalFileSubmitSubitle);                                              
                                                current_fs.find('.classrooms-list').fadeIn();
                                                current_fs.find('.classrooms-notice').fadeIn();
                                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                                fieldset_footer.find('#submit-students-file').fadeIn();
                                                current_fs.find('input.previous').fadeIn();
                                                current_fs.find('input.back').fadeOut();
                                                current_fs.find('.new_student_link').remove();
                                                $("#submit-new-students-file").hide();
                                                $('.kwf-preloader #tepunareomaori-preloader').show();
                                                $('.skipped_student_notice').fadeOut();
                                                checkNextButtonState();
                
                                                setTimeout(() => {
                                                    window.location.reload();
                                                }, 2000);
                                            });
                
                                        }
                                    } else {
                                        console.error('An error occurred:', result.data);
                                        title.addClass('error').text(MST_data.error_creating_student_from_file);
                                        subtitle.addClass('error').text(result.data);
                                        fieldset_notice.text(result.data).removeClass('success').addClass('error');
                                        fieldset_footer.css('visibility', 'visible');
                                        fieldset_footer.find('.previous').on('click', function () {
                                            fieldset_notice.hide();
                                            title.removeClass('error').text(originalFileSubmitTitle);
                                            subtitle.removeClass('error').text(originalFileSubmitSubtitle);
                                            current_fs.find('.classrooms-list').fadeIn();
                                            current_fs.find('.classrooms-notice').fadeIn();
                                            current_fs.find('#confirm-classroom-selection').fadeIn();
                                            fieldset_footer.find('#submit-students-file').fadeIn();
                                            current_fs.find('input.previous').fadeOut();
                                            current_fs.find('input.back').fadeOut();
                                        });
                                        fieldset_footer.find('.back').on('click', function () {
                                            resetToFirstFieldsetBulk();
                                            fieldset_notice.hide();
                                            title.removeClass('error').text(originalFileSubmitTitle);
                                            subtitle.removeClass('error').text(originalFileSubmitSubtitle);
                                            current_fs.find('.classrooms-list').fadeIn();
                                            current_fs.find('.classrooms-notice').fadeIn();
                                            current_fs.find('#confirm-classroom-selection').fadeIn();
                                            fieldset_footer.find('#submit-students-file').fadeIn();
                                            current_fs.find('input.previous').fadeOut();
                                            current_fs.find('input.back').fadeOut();
                                        });
                                    }
                                }
                            });
                        }
        
                   
                    } else {
                        console.error('An error occurred:', result.data);
                        title.addClass('error').text(MST_data.error_creating_student_from_file);
                        subtitle.addClass('error').text(result.data);
                        fieldset_notice.text(result.data).removeClass('success').addClass('error');
                        fieldset_footer.find('.previous').on('click', function () {
                            fieldset_notice.hide();
                            title.removeClass('error').text(originalFileSubmitTitle);
                            subtitle.removeClass('error').text(originalFileSubmitSubtitle);
                            current_fs.find('.classrooms-list').fadeIn();
                            current_fs.find('.classrooms-notice').fadeIn();
                            current_fs.find('#confirm-classroom-selection').fadeIn();
                            fieldset_footer.find('#submit-students-file').fadeIn();
                            current_fs.find('input.previous').fadeOut();
                            current_fs.find('input.back').fadeOut();
                        });
                        fieldset_footer.find('.back').on('click', function () {
                            resetToFirstFieldsetBulk();
                            fieldset_notice.hide();
                            title.removeClass('error').text(originalFileSubmitTitle);
                            subtitle.removeClass('error').text(originalFileSubmitSubtitle);
                            current_fs.find('.classrooms-list').fadeIn();
                            current_fs.find('.classrooms-notice').fadeIn();
                            current_fs.find('#confirm-classroom-selection').fadeIn();
                            fieldset_footer.find('#submit-students-file').fadeIn();
                            current_fs.find('input.previous').fadeOut();
                            current_fs.find('input.back').fadeOut();
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('.kwf-preloader #tepunareomaori-preloader').hide();
                    title.addClass('error').text(MST_data.error_creating_student_from_file);
                    subtitle.addClass('error').text(xhr.responseText);
                    fieldset_notice.text(MST_data.error_submiting_students_file).removeClass('success').addClass('error');
                    fieldset_footer.find('.previous').on('click', function () {
                        fieldset_notice.hide();
                        title.removeClass('error').text(originalFileSubmitTitle);
                        subtitle.removeClass('error').text(originalFileSubmitSubtitle);
                        current_fs.find('.template-file-text').fadeIn();
                        current_fs.find('.classrooms-notice').fadeIn();
                        current_fs.find('.file_upload_Container').fadeIn();
                        fieldset_footer.find('#submit-students-file').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                        current_fs.find('.new_student_link').fadeOut();
                    });
                    fieldset_footer.find('.back').on('click', function () {
                        resetToFirstFieldsetBulk();
                        fieldset_notice.hide();
                        title.removeClass('error').text(originalFileSubmitTitle);
                        subtitle.removeClass('error').text(originalFileSubmitSubtitle);
                        current_fs.find('.template-file-text').fadeIn();
                        current_fs.find('.classrooms-notice').fadeIn();
                        current_fs.find('.file_upload_Container').fadeIn();
                        fieldset_footer.find('#submit-students-file').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                    });
                }
            });
    
        }
    });

});