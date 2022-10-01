<?php
use Controllers\EmployeeController;

    $subtitle = $_SESSION[EmployeeController::EMPLOYEE_NAME] ?? 'New Employee';
    include 'views/layouts/header.php';
?>

<?php if (isset($_SESSION[EmployeeController::EMPLOYEE_SUCCESS])) { ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div class="toast show align-items-center text-white bg-success opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $_SESSION[EmployeeController::EMPLOYEE_SUCCESS] ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (isset($_SESSION[EmployeeController::EMPLOYEE_ERROR])) { ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show align-items-center text-white bg-danger opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $_SESSION[EmployeeController::EMPLOYEE_ERROR] ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php } ?>

<nav class="navbar bg-light px-3 mb-2" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/employee">Employee</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= isset($oldValues) && isset($oldValues['name']) ? $oldValues['name'] : 'New'?></li>
    </ol>
</nav>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form
                    action="<?= isset($id) ? "/employee/$id/edit" : "/employee/add"; ?>"
                    method="post"
                    class="d-flex flex-column"
            >
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="form-control<?= isset($errors) && isset($errors['name']) ? ' is-invalid' : ''; ?>"
                        value="<?= isset($oldValues) && isset($oldValues['name']) ? $oldValues['name'] : ''; ?>"
                    >
                    <?php if (isset($errors) && isset($errors['name'])) { ?>
                        <div class="invalid-feedback">
                            <?= $errors['name']; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="mb-3">
                    <label for="desc" class="form-label">Job Description</label>
                    <textarea
                        id="desc"
                        name="jobDescription"
                        class="form-control<?= isset($errors) && isset($errors['jobDescription']) ? ' is-invalid' : ''; ?>"
                    ><?= htmlspecialchars(isset($oldValues) && isset($oldValues['jobDescription']) ? $oldValues['jobDescription'] : ''); ?></textarea>
                    <?php if (isset($errors) && isset($errors['jobDescription'])) { ?>
                        <div class="invalid-feedback">
                            <?= $errors['jobDescription']; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="mb-3">
                    <label for="startDate" class="form-label">Start date</label>
                    <input
                        id="dueDate"
                        type="datetime-local"
                        name="startDate"
                        class="form-control<?= isset($errors) && isset($errors['startDate']) ? ' is-invalid' : ''?>"
                        value="<?= isset($oldValues) && isset($oldValues['startDate']) ? $oldValues['startDate'] : ''; ?>"
                    >
                    <?php if (isset($errors) && isset($errors['startDate'])) { ?>
                        <div class="invalid-feedback">
                            <?= $errors['startDate']; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select
                        id="status"
                        name="status"
                        class="form-select<?= isset($errors) && isset($errors['status']) ? ' is-invalid' : ''?>"
                    >
                        <option value="FULL-TIME" <?= isset($oldValues) && isset($oldValues['status']) && $oldValues['status'] === 'FULL-TIME' ? 'selected' : ''; ?>>Full-Time</option>
                        <option value="PART-TIME" <?= isset($oldValues) && isset($oldValues['status']) && $oldValues['status'] === 'PART-TIME' ? 'selected' : ''; ?>>Part-Time</option>
                    </select>
                    <?php if (isset($errors) && isset($errors['status'])) { ?>
                        <div class="invalid-feedback">
                            <?= $errors['status']; ?>
                        </div>
                    <?php } ?>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>

<?php
    include 'views/layouts/footer.php'
?>
