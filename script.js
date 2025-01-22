$(function() {
    // Autocomplete for search box
    $("#search-box").autocomplete({
        source: "index.php",
        minLength: 1, // Show suggestions after typing 1 character
        select: function(event, ui) {
            $("#search-box").val(ui.item.value); // Fill the search box with the selected name
            $("#selected-code").text(ui.item.code); // Display the selected code
        }
    });

    // Search button click handler
    $("#search-button").click(function() {
        const query = $("#search-box").val();
        if (query) {
            $.get("index.php", { term: query }, function(data) {
                const results = JSON.parse(data);
                if (results.length > 0) {
                    $("#selected-code").text(results[0].code); // Display the first result's code
                } else {
                    $("#selected-code").text("-");
                    alert("No results found!");
                }
            });
        }
    });
});