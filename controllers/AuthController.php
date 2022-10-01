<?php

namespace Controllers;

use Models\AuthModel;

class AuthController extends Controller
{
    public const USER_SESSION = 'EMS_LOGGED_USER';

    public function __construct(array $globals = [])
    {
        $this->globals = $globals;
        $this->model = new AuthModel();
    }

    public function login()
    {
        $requestMethod = $this->globals['REQUEST_METHOD'];

        switch ($requestMethod) {
            case 'GET':
                return include __DIR__.'/../views/login.php';
            case 'POST':
                $username = $this->globals['username'];
                $password = $this->globals['password'];

                $errors = [];
                $oldValues = [
                    'username' => $username,
                    'password' => $password
                ];

                if (!$username || !$password) {
                    if (!$username) {
                        $errors['username'] = 'Username is required';
                    }
                    if (!$password) {
                        $errors['password'] = 'Password is required';
                    }
                    return include __DIR__.'/../views/login.php';
                }

                $users = $this->model->fetchBy([
                    'username' => $username
                ]);

                if (count($users) === 1) {
                    if (password_verify($password, $users[0]['password'])) {
                        $_SESSION[self::USER_SESSION] = $username;
                        header('Location: ../employee');
                    } else {
                        $errors['password'] = 'Wrong password';
                    }
                } else {
                    $errors['username'] = 'Wrong username';
                }
                return include __DIR__.'/../views/login.php';
        }
        return include __DIR__.'/../views/login.php';
    }

    public function register() {
        $requestMethod = $this->globals['REQUEST_METHOD'];

        switch ($requestMethod) {
            case 'GET':
                return include __DIR__.'/../views/register.php';
            case 'POST':
                $username = $this->globals['username'];
                $password = $this->globals['password'];
                $confirmPassword = $this->globals['confirmPassword'];

                $errors = [];
                $oldValues = [
                    'username' => $username,
                    'password' => $password,
                    'confirmPassword' => $confirmPassword
                ];

                if (!$username || !$password || !$confirmPassword) {
                    if (!$username) {
                        $errors['username'] = 'Username is required';
                    }
                    if (!$password) {
                        $errors['password'] = 'Password is required';
                    }
                    if (!$confirmPassword) {
                        $errors['confirmPassword'] = 'Confirm password is required';
                    }
                    return include __DIR__.'/../views/register.php';
                }

                if ($password === $confirmPassword) {
                    if (
                        $this->model->insert([
                            'username' => $username,
                            'password' => password_hash($password, PASSWORD_DEFAULT),
                            'role' => 3
                        ])
                    ) {
                        $_SESSION[self::USER_SESSION] = $username;
                        header('Location: ../employee');
                    }
                } else {
                    $errors['confirmPassword'] = 'Confirmed password does not match with password';
                }
                return include __DIR__.'/../views/register.php';
        }
        return include __DIR__.'/../views/register.php';
    }

    public function logout() {
        $_SESSION[AuthController::USER_SESSION] = null;
        return include __DIR__.'/../views/login.php';
    }

    function index()
    {
        header('Location: ../auth/login');
    }

    function getById(int $id)
    { }
}