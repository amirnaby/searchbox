<?php
// Create data.json if it doesn't exist
if (!file_exists('data.json')) {
    file_put_contents('data.json', json_encode([]));
}

$data = json_decode(file_get_contents('data.json'), true);
$query = $_GET['query'] ?? '';

$results = array_filter($data, function ($item) use ($query) {
    return stripos($item['name'], $query) !== false;
});

foreach ($results as $item) {
    echo "<div class='autocomplete-item' data-name='{$item['name']}' data-code='{$item['code']}'>{$item['name']}</div>";
}