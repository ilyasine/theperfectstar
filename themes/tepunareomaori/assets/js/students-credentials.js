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

                    doc.addImage(img, 'JPEG', 120, 10, 40, 20);
                    // pdf author
                    doc.setFontSize(10); // Set the font size to 10
                    doc.text(5, y = 5, imprinted_by);
                    doc.text(5, y = 10, author);

                    doc.setFontSize(16); // Set the font size to 10
                    doc.setLineWidth(2);
                    doc.setTextColor(0, 167, 157);
                    doc.setFont('Nunito-Regular');
                    doc.text(60, y = 50, title + titletime);


                    var stdhead = [[std_header_name, std_header_email, std_header_username, std_header_password, std_header_classrom, account_status_header_name]];
                    var stdbody = [];

                    students_data.forEach(student_data => {
                        std_name = student_data.std_name;
                        std_email = student_data.std_email;
                        std_username = student_data.std_username;
                        std_password = student_data.std_password;
                        std_classrom = student_data.std_classrom;
                        account_status = student_data.account_status;
                        status_color = student_data.status_color;
                        status_bg_color = student_data.status_bg_color;

                        stdbody.push([std_name, std_email, std_username, std_password, std_classrom, account_status]);

                    });

                    // Add Students Data table
                    doc.autoTable({
                        head: stdhead,
                        body: stdbody,
                        startY: 60,
                        pageBreak: 'auto',
                        rowPageBreak: 'avoid',
                        headStyles: { fillColor: [247, 148, 29], fontStyle: 'bold', cellPadding: 2 },
                        bodyStyles: { fontStyle: 'bold', cellPadding: 2 },
                        didParseCell: function (data) {
                            var accountStatus = data.cell.raw;
                            if (data.section === 'body' && (accountStatus === 'Active' || accountStatus === 'Actif')) {
                                data.cell.styles.fillColor = [0, 167, 157];
                                data.cell.styles.textColor = [255, 255, 255];
                            }
                        },
                        columnStyles: {
                            3: { fillColor: [161, 206, 204] }
                        },
                        styles: { halign: 'center' },
                        horizontalPageBreakRepeat: 0,
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
                'info',
                null,
                true
            ]
        );

    });
});

function copyToClipboard() {
    // the text you want to copy
    var username_th = document.querySelector('th.column-kwf-student-username').innerText;
    var password_th = document.querySelector('th.column-kwf-student-password').innerText;
    // Select the text you want to copy
    var username = document.querySelector('td.column-kwf-student-username').innerText;
    var password = document.querySelector('td.column-kwf-student-password input#password-cell').value;

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
            'info',
            null,
            true
        ]
    );
}


