<?php
require 'header.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: ' . $baseUrl . 'login.php');
    exit;
}

// Load data from JSON file
$data = json_decode(file_get_contents('data.json'), true);

// Load last used ID
$lastIdFile = 'last_id.txt';
$lastId = file_exists($lastIdFile) ? (int)file_get_contents($lastIdFile) : 0;

// Handle add, update, and delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Add new record
        $name = strtolower($_POST['name']);
        $code = strtolower($_POST['code']);

        // Check for unique Name and Code (case-insensitive)
        $nameExists = array_filter($data, function($item) use ($name) {
            return strtolower($item['name']) === $name;
        });
        $codeExists = array_filter($data, function($item) use ($code) {
            return strtolower($item['code']) === $code;
        });

        if (!empty($nameExists)) {
            $_SESSION['error'] = 'Name already exists!';
        } elseif (!empty($codeExists)) {
            $_SESSION['error'] = 'Code already exists!';
        } else {
            // Auto-increment ID
            $lastId++;
            $data[] = ['id' => $lastId, 'name' => $_POST['name'], 'code' => $_POST['code']];
            file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($lastIdFile, $lastId);
            $_SESSION['success'] = 'Record added successfully!';
            $_SESSION['clear_form'] = true; // Flag to clear the form
        }
    } elseif (isset($_POST['update'])) {
        // Update record
        $id = $_POST['id'];
        $name = strtolower($_POST['name']);
        $code = strtolower($_POST['code']);

        // Check for unique Name and Code (excluding the current record)
        $nameExists = array_filter($data, function($item) use ($id, $name) {
            return strtolower($item['name']) === $name && $item['id'] != $id;
        });
        $codeExists = array_filter($data, function($item) use ($id, $code) {
            return strtolower($item['code']) === $code && $item['id'] != $id;
        });

        if (!empty($nameExists)) {
            $_SESSION['edit_error'] = 'Name already exists!';
        } elseif (!empty($codeExists)) {
            $_SESSION['edit_error'] = 'Code already exists!';
        } else {
            foreach ($data as &$item) {
                if ($item['id'] == $id) {
                    $item['name'] = $_POST['name'];
                    $item['code'] = $_POST['code'];
                    break;
                }
            }
            file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $_SESSION['success'] = 'Record updated successfully!';
        }
    } elseif (isset($_POST['delete'])) {
        // Delete record
        $id = $_POST['id'];
        $data = array_filter($data, function($item) use ($id) {
            return $item['id'] != $id;
        });
        $data = array_values($data); // Reindex array
        file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $_SESSION['success'] = 'Record deleted successfully!';
    }
}

// Clear session messages after displaying them
$error = $_SESSION['error'] ?? null;
$editError = $_SESSION['edit_error'] ?? null;
$success = $_SESSION['success'] ?? null;
$clearForm = $_SESSION['clear_form'] ?? false;
unset($_SESSION['error']);
unset($_SESSION['edit_error']);
unset($_SESSION['success']);
unset($_SESSION['clear_form']);
?>
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="card-title mb-0">Admin Panel</h1>
            <a href="<?= $baseUrl ?>index.php" class="btn btn-light btn-sm">View Site</a>
        </div>
        <div class="card-body">
            <!-- Add Record Form -->
            <form method="POST" class="mb-4" id="addForm">
                <div class="row">
                    <div class="col">
                        <input type="text" name="name" class="form-control" placeholder="Name" value="<?= $clearForm ? '' : ($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="col">
                        <input type="text" name="code" class="form-control" placeholder="Code" value="<?= $clearForm ? '' : ($_POST['code'] ?? '') ?>" required>
                    </div>
                    <div class="col">
                        <button type="submit" name="add" class="btn btn-success">Add</button>
                    </div>
                </div>
            </form>

            <!-- Table of Records -->
            <h2>Records</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['code']) ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                        data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name']) ?>" 
                                        data-code="<?= htmlspecialchars($item['code']) ?>">
                                    Edit
                                </button>
                                <!-- Delete Button -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Logout Button -->
            <a href="<?= $baseUrl ?>logout.php" class="btn btn-danger mt-3">Logout</a>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCode" class="form-label">Code</label>
                            <input type="text" name="code" id="editCode" class="form-control" required>
                        </div>
                        <?php if ($editError): ?>
                            <div class="alert alert-danger"><?= $editError ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Edit Modal Handler
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const code = button.getAttribute('data-code');
                editModal.querySelector('#editId').value = id;
                editModal.querySelector('#editName').value = name;
                editModal.querySelector('#editCode').value = code;
            });
        }
    </script>
<?php require 'footer.php'; ?>