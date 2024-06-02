$(document).ready(function() {
    var url = $(location).attr('href');
    var parts = url.split("/");
    var last_part = parts[4];
    var active_tab = $('ul#sidebar_navigation li a#'+last_part);
    active_tab.addClass( "bg-gray-100" );

    var parent = active_tab.closest('ul');
    $(parent).removeClass("hidden");

});