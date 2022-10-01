<?php
    $subtitle = 'All Employees';

    include 'views/layouts/header.php';
?>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
    <div id="statusToast" class="toast align-items-center text-white opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div id="statusToastMessage" class="toast-body">
                Message
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    const showToast = (message, error = false) => {
        const toastMessageEl = document.getElementById('statusToastMessage');
        toastMessageEl.textContent = message;

        const toastEl = document.getElementById('statusToast');
        toastEl.classList.add(error ? 'bg-danger' : 'bg-success');
        toastEl.classList.add('show');
        setTimeout(() => toastEl.classList.remove('show'), 10000);
    }

    const onDeleteClick = (event) => {
        if (event instanceof PointerEvent) {
            const button = event.target;
            const { dataset } = button;

            const id = +dataset.employeeId;
            const name = dataset.employeeName;

            if (confirm(`Are you sure that you want to delete "${name}" ?`)) {
                const httpRequest = new XMLHttpRequest();

                httpRequest.onreadystatechange = () => {
                    const { readyState, status, response } = httpRequest;
                    if (readyState === 4) {
                        const res = JSON.parse(response);

                        switch (status) {
                            case 200:
                                showToast(res.status.message);

                                const listGroup = document.getElementsByClassName('list-group')[0];
                                const listItems = document.getElementsByClassName('list-group-item');

                                for (let i = 0; i < listItems.length; i++) {
                                    const { dataset } = listItems.item(i)
                                    const listItemEmployeeId = +dataset.employeeId;

                                    if (listItemEmployeeId === id) {
                                        listGroup.removeChild(listItems.item(i));
                                    }
                                }
                                break;
                            case 403:
                            case 404:
                            case 409:
                                showToast(res.status.message);
                                break;
                        }
                    }
                };

                httpRequest.open('DELETE', `http://localhost/employee/${id}/delete`);
                httpRequest.send();
            }
        }
    }
</script>

<div class="container-fluid p-3">
    <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-primary" href="/employee/add" role="button">Add new employee</a>
    </div>
    <?php if (isset($employeeList)) { ?>
        <ol class="list-group list-group-numbered">
            <?php foreach ($employeeList as $employee) { ?>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                    data-todo-id="<?= $employee['id'] ?>"
                >
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            <a href="/employee/<?= $employee['id'] ?>"><?= $employee['name'] ?></a> | <?= date('d/m/Y H:i', strtotime($employee['startDate'])) ?>
                        </div>
                        <?= $employee['jobDescription'] ?>
                    </div>
                    <div class="d-flex flex-column align-items-end justify-content-center">
                        <span class="badge rounded-pill<?= $employee['status'] === 'FULL-TIME' ? ' bg-primary' : ' bg-success' ?>"><?= $employee['status'] ?></span>
                        <?php if ($employee['status'] === 'ACTIVE') { ?>
                            <span
                                data-todo-id="<?= $employee['id'] ?>"
                                data-todo-name="<?= $employee['name'] ?>"
                                class="material-icons delete-icon"
                                onclick="onDeleteClick(event)"
                            >Delete</span>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
        </ol>
    <?php } ?>
</div>

<?php
    include 'views/layouts/footer.php';
?>
