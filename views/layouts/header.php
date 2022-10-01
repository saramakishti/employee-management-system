<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EMS<?= isset($subtitle) ? " | $subtitle" : "" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .username {
            padding: 8px 12px;
        }
        .delete-icon {
            cursor: pointer;
            color: tomato;
        }
    </style>
</head>
<body>
<?php
use Controllers\AuthController;

if (isset($_SESSION[AuthController::USER_SESSION])) { ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Employee List</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/employee">All Employees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/employee?status=full-time">Full-Time</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/employee?status=part-time">Part-Time</a>
                    </li>
                </ul>
                <form class="d-flex align-items-center justify-content-end" action="/auth/logout" method="post">
                    <label class="username">Welcome back, <?= $_SESSION[AuthController::USER_SESSION] ?></label>
                    <button class="btn btn-danger" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>
<?php } ?>
<script>
    let search = window.location.search;
    if (search) {
        search = search.slice(1);
    }
    const queryParams = search
        .split('&')
        .reduce((acc, curr) => {
            const [key, value] = curr.split('=');
            return { ...acc, [key]: value };
        }, {});

    const navLinks = document.getElementsByClassName('nav-link');

    if (queryParams && queryParams.status) {
        const { status } = queryParams;

        if (status === 'full-time') {
            navLinks[1].classList.add('active');
        } else {
            navLinks[2].classList.add('active');
        }
    } else {
        navLinks[0].classList.add('active');
    }
</script>