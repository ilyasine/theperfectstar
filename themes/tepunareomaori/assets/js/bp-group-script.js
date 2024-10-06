jQuery(document).ready(function($) {
    // Check if we are on a BuddyPress group page by looking for specific classes in the body element
    var isBuddyPressGroupPage = $('body').hasClass('bp-group') && $('body').hasClass('bp-single-group');

    // If we are on a BuddyPress group page, reload the page after a delay (e.g., 2000 milliseconds or 2 seconds)
    if (isBuddyPressGroupPage) {
        setTimeout(function() {
            location.reload();
        }, 2000); // 2000 milliseconds (2 seconds)
    }
});
