<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Create last_id.txt if it doesn't exist
if (!file_exists('last_id.txt')) {
    file_put_contents('last_id.txt', '0');
}

$data = json_decode(file_get_contents('data.json'), true);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);

        // Check for duplicates
        $duplicate = false;
        foreach ($data as $item) {
            if ($item['name'] === $name || $item['code'] === $code) {
                $duplicate = true;
                break;
            }
        }

        if ($duplicate) {
            $_SESSION['error'] = "Duplicate data found!";
        } else {
            $newId = intval(file_get_contents('last_id.txt')) + 1;
            $data[] = ['id' => $newId, 'name' => $name, 'code' => $code];
            file_put_contents('data.json', json_encode($data));
            file_put_contents('last_id.txt', $newId);
        }
    } elseif (isset($_POST['edit'])) {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);

        // Check for duplicates
        $duplicate = false;
        foreach ($data as $item) {
            if (($item['name'] === $name || $item['code'] === $code) && $item['id'] !== $id) {
                $duplicate = true;
                break;
            }
        }

        if ($duplicate) {
            $_SESSION['error'] = "Duplicate data found!";
        } else {
            foreach ($data as &$item) {
                if ($item['id'] === $id) {
                    $item['name'] = $name;
                    $item['code'] = $code;
                    break;
                }
            }
            file_put_contents('data.json', json_encode($data));
        }
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $data = array_filter($data, function ($item) use ($id) {
            return $item['id'] !== $id;
        });
        file_put_contents('data.json', json_encode(array_values($data)));
    }

    // Redirect to avoid form resubmission on page refresh
    header('Location: admin.php');
    exit();
}

// Clear error after displaying
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<?php include 'header.php'; ?>
<div class="container mt-5">
	<div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
			<h1 class="card-title mb-0">Admin Panel</h1>
		</div>
		<div class="card-body">
		<?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <button class="btn btn-primary mb-3 w-100" data-bs-toggle="modal" data-bs-target="#addModal">Add new record</button>

        <table class="table table-bordered">
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
                        <td><?= $item['name'] ?></td>
                        <td><?= $item['code'] ?></td>
                        <td class="flex">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                    data-id="<?= $item['id'] ?>" data-name="<?= $item['name'] ?>" data-code="<?= $item['code'] ?>">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $item['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
		</div>
	</div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" name="add">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCode" class="form-label">Code</label>
                            <input type="text" class="form-control" id="editCode" name="code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" name="edit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Edit modal data binding
        document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const code = button.getAttribute('data-code');

            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editCode').value = code;
        });

        // Delete confirmation
        function confirmDelete(id) {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            document.getElementById('confirmDelete').onclick = function () {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="id" value="${id}">
                                  <input type="hidden" name="delete" value="1">`;
                document.body.appendChild(form);
                form.submit();
            };
            deleteModal.show();
        }
    </script>
<?php include 'footer.php'; ?>