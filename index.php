<?php
require 'header.php';

// Load data from JSON file
$data = json_decode(file_get_contents('data.json'), true);

// Handle search request
if (isset($_GET['term'])) {
    $term = strtolower($_GET['term']);
    $results = array_filter($data, function($item) use ($term) {
        return strpos(strtolower($item['name']), $term) !== false;
    });
    // Return name and code for autocomplete
    echo json_encode(array_map(function($item) {
        return [
            'label' => $item['name'] . ' (' . $item['code'] . ')', // Display name and code
            'value' => $item['name'], // Value to fill in the search box
            'code' => $item['code']   // Code to display when selected
        ];
    }, array_values($results)));
    exit;
}
?>
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Search Names</h1>
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <input type="text" id="search-box" class="form-control" placeholder="Search names...">
                <button id="search-button" class="btn btn-primary">Search</button>
            </div>
            <p class="mt-3">Selected Code: <span id="selected-code" class="fw-bold">-</span></p>
        </div>
    </div>
<?php require 'footer.php'; ?>
