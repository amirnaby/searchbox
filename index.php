<?php
session_start();
$data = json_decode(file_get_contents('data.json'), true);
?>

<?php include 'header.php'; ?>
<div class="container mt-5">
	<div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
			<h1 class="card-title mb-0">Search Names</h1>
		</div>
		<div class="card-body">
			<div class="input-group mb-3">
				<input type="text" id="searchBox" class="form-control" placeholder="Search names...">
			</div>
			<div id="results" class="autocomplete-dropdown"></div>
			<div id="selectedCode" class="mt-3">
				<strong>Selected Code:</strong> <span id="codeValue">-</span>
			</div>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>