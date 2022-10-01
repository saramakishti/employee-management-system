<?php
    $subtitle = 'Login';

    include 'layouts/header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Login to Employee Management System</h5>
            <form
                    action="/auth/login"
                    method="post"
                    class="d-flex flex-column"
            >
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input
                            id="username"
                            type="text"
                            name="username"
                            class="form-control<?= isset($errors) && isset($errors['username']) ? ' is-invalid' : ''; ?>"
                            placeholder="Enter username"
                            value="<?= isset($oldValues) && isset($oldValues['username']) ? $oldValues['username'] : ''; ?>"
                    >
                    <?php if (isset($errors) && isset($errors['username'])) { ?>
                        <div class="invalid-feedback">
                            <?= $errors['username']; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter password"
                            class="form-control<?= isset($errors) && isset($errors['password']) ? ' is-invalid' : ''?>"
                            value="<?= isset($oldValues) && isset($oldValues['password']) ? $oldValues['password'] : ''; ?>"
                    >
                    <?php if (isset($errors) && isset($errors['password'])) { ?>
                        <div class="invalid-feedback">
                            <?= $errors['password']; ?>
                        </div>
                    <?php } ?>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <br>
                <a class="btn btn-outline-secondary" href="/auth/register" role="button">Create New Account</a>
            </form>
        </div>
    </div>
</div>

<?php
    include 'layouts/footer.php';
?>