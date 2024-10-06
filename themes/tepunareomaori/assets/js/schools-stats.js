jQuery(document).ready(function($) {
    // Initialize jQuery UI Tabs
    $("#tabs").tabs({
        create: function (event, ui) {
            // Get the index of the active tab from localStorage
            var activeTab = localStorage.getItem("activeTab");
            // If there is a value, activate the tab
            if (activeTab) {
                $("#tabs").tabs("option", "active", activeTab);
            }
            // Load the content of the active tab
            loadTabContent(ui.panel.attr("id"));
        },
        beforeActivate: function (event, ui) {
            // Set the index of the active tab to localStorage
            localStorage.setItem("activeTab", ui.newTab.index());
        },
        activate: function (event, ui) {
            // Load the content of the activated tab
            loadTabContent(ui.newPanel.attr("id"));
        }
    });
    
    // A function to load the content of a tab based on its id
    function loadTabContent(tabId) {
        if (tabId === "schools-tab") {
            loadSchoolsTabContent();
        } else if (tabId === "stats-tab") {
            loadStatsTabContent();
        }
    }
    
    // Function to load schools tab content
    function loadSchoolsTabContent() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'schools_tab_content'
            },
            success: function(response) {
                $('#schools-tab').html(response);
                $('#schools-tab').addClass('loaded');                  
            },
            error: function(e) {
                console.log(e);
              },
        });
    }

    // Function to load stats tab content
    function loadStatsTabContent() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'stats_tab_content'
            },
            success: function(response) {
                $('#stats-tab').html(response);
                $('#stats-tab').addClass('loaded');
            },
            error: function(e) {
                console.log(e);
              },
        });
    }
});