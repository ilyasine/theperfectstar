jQuery(document).ready(function($) {
    // Change input type to text
    $('#pass1').attr('type', 'text');
});
jQuery(document).ready(function($) {
    // Initially change input type to text for pass1
    $('#pass1').attr('type', 'text');

    // Initially change input type to text for pass2
    $('#pass2').attr('type', 'text');

    // Continuously change input type to text when typing for pass1
    $('#pass1').on('input', function() {
        $(this).attr('type', 'text');
    });

    // Continuously change input type to text when typing for pass2
    $('#pass2').on('input', function() {
        $(this).attr('type', 'text');
    });
});