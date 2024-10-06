jQuery(document).ready(function ($) {
    
    $("#courses_archive").select2();
    $('#courses_archive').one('select2:open', function(e) {
        $('input.select2-search__field').prop('placeholder', profile_courses.my_archive);
    });
  
});


