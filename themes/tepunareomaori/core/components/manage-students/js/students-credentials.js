jQuery(document).ready(function ($) {
    $('.print_students_credentials #print_excel').on('click', function () {
        var security = $(this).data('security');
        var generatedFilename; // Variable to store the dynamically generated filename
        var generatedFilePath; // Variable to store the Excel file path

        NProgress.start();
        NProgress.set(0.4);

        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);
        clearInterval(interval);

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'std_cred_generate_excel_file',
                security: security,
                payload: 'students_credentials_excel',
            },
            type: 'post',
            dataType: 'json', // Specify that the response is JSON
            success: function (result, textstatus) {

                // Check if the response contains a download link
                if (result.download_link) {
                    // Use a regular expression to extract the filename from the download link
                    var match = result.download_link.match(/\/([^/]+\.xlsx)$/);
                    if (match) {
                        generatedFilename = match[1]; // Extracted filename
                    } else {
                        console.log('Failed to extract the filename from the download link.');
                        return;
                    }

                    // Store the file path from the response
                    generatedFilePath = result.file_path;

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
                            '<div>' + TPRM_stdcred.success_print + '</div>',
                            'success',
                            null,
                            true
                        ]
                    );

                    NProgress.done();

                    // Set a timeout to delete the file after a specified time (e.g., 10 seconds)
                    setTimeout(function () {
                        // Make an AJAX request to delete the file on the server
                        $.ajax({
                            url: ajaxurl,
                            data: {
                                action: 'delete_excel_file',
                                filename: generatedFilename, // Use the extracted filename
                                file_path: generatedFilePath, // Pass the file path
                            },
                            type: 'post',
                            success: function (deleteResult, deleteTextstatus) {
                                //console.log(deleteResult);
                                //console.log('File deleted from the server.');
                            },
                            error: function (deleteResult) {
                                /* console.log(deleteResult);
                                console.log('Failed to delete the file from the server.'); */
                            },
                        });
                    }, 10000); // Adjust the timeout value (in milliseconds) as needed
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
});

jQuery(document).ready(function ($) {
    $('.print_students_credentials #print_pdf').on('click', function () {
        var security = $(this).data('security');

        NProgress.start();
        NProgress.set(0.4);

        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);
        clearInterval(interval);

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'std_cred_generate_pdf_file',
                security: security,
                payload: 'students_credentials_pdf',
            },
            type: 'post',

            // dataType: 'json', // Specify that the response is JSON
            success: function (result, textstatus) {
                // Check if the response contains a download link
                if (result) {
                    var obj = JSON.parse(result);
                    console.log('obj:', obj);
                    //document data
                    var ecole_name = obj.ecole_name;
                    var ecole_name_formatted = obj.ecole_name_formatted;
                    var title = obj.title;
                    var imprinted_by = obj.imprinted_by;
                    var author = obj.author;
                    var description = obj.description;
                    var img = obj.img;

                    //header data
                    var students_header_data = obj.students_header_data;
                    var std_header_name = students_header_data.std_name;
                    var std_header_email = students_header_data.std_email;
                    var std_header_username = students_header_data.std_username;
                    var std_header_password = students_header_data.std_password;
                    var std_header_classrom = students_header_data.std_classrom;
                    var account_status_header_name = students_header_data.account_status_name;

                    //body data
                    var students_data = obj.students_data;

                    var doc = new jspdf.jsPDF('l');
                    var currentdate = new Date();
                    var filetime = currentdate.getDate() + "-"
                        + (currentdate.getMonth() + 1) + "-"
                        + currentdate.getFullYear() + "-"
                        + currentdate.getHours() + "-"
                        + currentdate.getMinutes() + "-"
                        + currentdate.getSeconds();

                    var titletime = currentdate.getDate() + "/"
                        + (currentdate.getMonth() + 1) + "/"
                        + currentdate.getFullYear() + " @ "
                        + currentdate.getHours() + ":"
                        + currentdate.getMinutes() + ":"
                        + currentdate.getSeconds();

                    var y = 10;

                    doc.addImage(img, 'JPEG', 122, 5, 50, 40);
                    // pdf author
                    doc.setFontSize(10); // Set the font size to 10
                    doc.text(5, y = 5, imprinted_by);
                    doc.text(5, y = 10, author);

                    doc.setFontSize(16); // Set the font size to 10
                    doc.setLineWidth(2);
                    doc.setTextColor(0, 167, 157);
                    doc.setFont('Nunito-Regular');
                    doc.text(60, y = 50, title + titletime);


                    var baseUrl = `${window.location.protocol}//${window.location.hostname}`;
                    var stdhead = [[std_header_name, std_header_email, std_header_username, std_header_password, std_header_classrom, account_status_header_name]];
                    var stdbody = [];


                    // console.log('Students data:', students_data); 
                    students_data.forEach(student_data => {
                        std_name = student_data.std_name;
                        std_email = student_data.std_email;
                        std_username = student_data.std_username;
                        std_password = student_data.std_password;
                        std_classrom = student_data.std_classrom;
                        account_status = student_data.account_status;

                        var validExtensions = ['.png', '.jpeg', '.jpg'];

                        if (std_password && validExtensions.some(ext => std_password.endsWith(ext))) {

                            std_password = {
                                image: baseUrl + "/wp-content/uploads/picture-passwords/" + std_password
                            };
                            // console.log('Detected image in password:', std_password);
                        }
                        stdbody.push([std_name, std_email, std_username, std_password, std_classrom, account_status]);
                    });

                    doc.autoTable({
                        head: stdhead,
                        body: stdbody,
                        startY: 60,
                        pageBreak: 'auto',
                        rowPageBreak: 'avoid',
                        headStyles: { fillColor: [247, 148, 29], fontStyle: 'bold', cellPadding: 2 },
                        bodyStyles: { fontStyle: 'bold', cellPadding: 4 },
                        columnStyles: {
                            3: { fillColor: [161, 206, 204] }
                        },
                        styles: { halign: 'center' },
                        horizontalPageBreakRepeat: 0,
                        didParseCell: function (data) {
                            if (data.section === 'body') {
                                if (typeof data.cell.raw === 'object') {
                                    data.cell.text = [];
                                }
                                if (data.column.index === 5) { // Account Status column
                                    var accountStatus = data.cell.raw;
                                    if (accountStatus === 'Active' || accountStatus === 'Actif') {
                                        data.cell.styles.fillColor = [0, 167, 157];
                                        data.cell.styles.textColor = [255, 255, 255];
                                    }
                                }
                            }
                        },
                        didDrawCell: function (data) {

                            if (data.section === 'body') {
                                if (data.column.index === 3 && data.cell.raw && data.cell.raw.image) {
                                    data.cell.text = "";
                                    var img = data.cell.raw.image;
                                    var imgProps = { width: data.cell.width - 9, height: data.cell.height };
                                    var imgX = data.cell.x + 4.6;
                                    var imgY = data.cell.y;

                                    doc.addImage(img, 'JPEG', imgX, imgY, imgProps.width, imgProps.height);
                                }

                            }


                        }
                    });



                    //add KWF copyright to bottom
                    var pageHeight = doc.internal.pageSize.height; // Get the height of the page
                    var pageWidth = doc.internal.pageSize.width; // Get the height of the page
                    var textOffset = 25; // Define an offset value for positioning the text from the bottom of the page
                    doc.setFontSize(10); // Set the font size to 10
                    doc.setTextColor(0, 0, 0); // black color


                    doc.text(pageWidth / 2, pageHeight - textOffset, description, { align: 'center', maxWidth: 100 });

                    doc.save(ecole_name_formatted + '_' + filetime + '.pdf');

                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + TPRM_stdcred.success_print + '</div>',
                            'success',
                            null,
                            true
                        ]
                    );

                    NProgress.done();

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
});

jQuery(document).ready(function ($) {
    $('.copy-button').click(function () {
        var targetElement = $(this).siblings('.copy-target');
        var copyfeedback = $(this).data('feedback');
        var textToCopy;

        if (targetElement.is('input')) {
            textToCopy = targetElement.val();
        } else {
            textToCopy = targetElement.text().trim();
        }

        // Create a textarea element to hold the text to be copied
        var textarea = $('<textarea>').val(textToCopy).css({
            position: 'fixed',
            opacity: 0
        }).appendTo('body');

        // Select the text and copy it to the clipboard
        textarea[0].select();
        document.execCommand('copy');

        // Remove the temporary textarea
        textarea.remove();

        // feedback to the user
        jQuery(document).trigger(
            'bb_trigger_toast_message',
            [
                '',
                '<div>' + copyfeedback + '</div>',
                'success',
                null,
                true
            ]
        );

    });

    $('.bulk-assign button#change-btn').on('click', function () {
        var current_classroom = $('#classroom-dropdown').data('current-classroom');
        var target_classroom = $('#classroom-dropdown').val();
        var selectedStudents = [];

        // Iterate through each checked checkbox within the .student_avatar elements
        $('#members-list .student_avatar .tprm-checkbox input[type="checkbox"]:checked').each(function () {
            // Find the closest li element and retrieve its data-bp-item-id attribute
            var studentId = $(this).closest('li').data('bp-item-id');
            // Push the student ID into the selectedStudents array
            selectedStudents.push(studentId);
        });

        // Log the array of selected student IDs
        console.log(selectedStudents);
        console.log(current_classroom);
        console.log(target_classroom);

    });


    /* Select Students */
    // Handle "Check All" in the header
    $('#check-all-students').on('change', function () {
        var isChecked = $(this).is(':checked');

        // Check or uncheck all student checkboxes without triggering the change event
        $('.student_avatar .tprm-checkbox input[type="checkbox"]').prop('checked', isChecked);

        // Optionally, add or remove a class based on the checkbox state
        $('.student_avatar').toggleClass('checked', isChecked);
    });

    // Handle individual checkbox changes
    $('.student_avatar .tprm-checkbox input[type="checkbox"]').on('change', function () {
        var allChecked = $('.student_avatar .tprm-checkbox input[type="checkbox"]').length === $('.student_avatar .tprm-checkbox input[type="checkbox"]:checked').length;

        // Sync header checkbox with all checkboxes
        $('#check-all-students').prop('checked', allChecked);

        // Add or remove a class based on the checkbox state
        $(this).closest('.student_avatar').toggleClass('checked', $(this).is(':checked'));
    });
});

function copyToClipboard() {
    // the text you want to copy
    var username_th = document.querySelector('th.column-tprm-student-username').innerText;
    var password_th = document.querySelector('th.column-tprm-student-password').innerText;
    // Select the text you want to copy
    var username = document.querySelector('td.column-tprm-student-username').innerText;
    var password = document.querySelector('td.column-tprm-student-password input#password-cell').value;

    // Combine username and password
    var credentials = username_th + ' : ' + username + '\n\n' + password_th + ' : ' + password;

    // Create a textarea element to temporarily hold the text
    var textarea = document.createElement('textarea');
    textarea.value = credentials;

    // Append the textarea to the document
    document.body.appendChild(textarea);

    // Select and copy the text in the textarea
    textarea.select();
    document.execCommand('copy');

    // Remove the textarea from the document
    document.body.removeChild(textarea);

    // feedback to the user
    jQuery(document).trigger(
        'bb_trigger_toast_message',
        [
            '',
            '<div>' + TPRM_stdcred.credentials_copied + '</div>',
            'success',
            null,
            true
        ]
    );
}


