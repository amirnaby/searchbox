$(document).ready(function () {
    // Autocomplete functionality
    $("#searchBox").on("input", function () {
        const query = $(this).val();
        if (query.length > 0) {
            $.ajax({
                url: "search.php",
                method: "GET",
                data: { query: query },
                success: function (response) {
                    $("#results").html(response);
                },
            });
        } else {
            $("#results").html("");
            $("#codeValue").text("-"); // Reset selected code
        }
    });

    // Handle click on autocomplete suggestions
    $(document).on("click", ".autocomplete-item", function () {
        const name = $(this).data("name"); // Get the name
        const code = $(this).data("code"); // Get the code

        // Fill the search box with the selected name
        $("#searchBox").val(name);

        // Display the selected code below the search box
        $("#codeValue").text(code);

        // Clear the drop-down results
        $("#results").html("");
    });
});