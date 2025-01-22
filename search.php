<?php
$data = json_decode(file_get_contents('data.json'), true);
$query = $_GET['query'] ?? '';

$results = array_filter($data, function ($item) use ($query) {
    return stripos($item['name'], $query) !== false;
});

foreach ($results as $item) {
    echo "<div class='autocomplete-item' data-name='{$item['name']}' data-code='{$item['code']}'>{$item['name']} - {$item['code']}</div>";
}