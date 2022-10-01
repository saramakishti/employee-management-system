<?php

namespace Controllers;

use Models\AuthModel;
use Models\EmployeeModel;

use PDOException;

class EmployeeController extends Controller
{
    public const EMPLOYEE_NAME = 'EMPLOYEE_NAME';
    public const EMPLOYEE_ERROR = 'EMPLOYEE_ERROR';
    public const EMPLOYEE_SUCCESS = 'EMPLOYEE_SUCCESS';

    public function __construct(array $globals)
    {
        $this->globals = $globals;
        $this->model = new EmployeeModel();
    }

    public function index()
    {
        $employeeList = [];

        $username = $this->globals[AuthController::USER_SESSION];
        $userModel = new AuthModel();
        $users = $userModel->fetchBy([
            'username' => $username
        ]);

        if (count($users) === 1) {
            $userId = $users[0]['id'];
            $limit = isset($this->globals['pageSize']) ? intval($this->globals['pageSize']) : 8;
            $offset = ((isset($this->globals['pageNo']) ? intval($this->globals['pageNo']) : 1) - 1) * $limit;

            $employeeList = $this->model->fetchPaginatedBy($limit, $offset, array_merge(['userId' => $userId], $this->globals));
        }

        return include __DIR__.'/../views/employee.php';
    }

    public function add()
    {
        $requestMethod = $this->globals['REQUEST_METHOD'];

        switch ($requestMethod) {
            case 'GET':
                $_SESSION[self::EMPLOYEE_SUCCESS] = null;
                return include __DIR__.'/../views/edit-employee.php';
            case 'POST':
                $username = $this->globals[AuthController::USER_SESSION];
                $userModel = new AuthModel();
                $users = $userModel->fetchBy([
                    'username' => $username
                ]);

                if (count($users) === 1) {
                    $name = $this->globals['name'];
                    $jobDescription = $this->globals['jobDescription'];
                    $startDate = $this->globals['startDate'];
                    $status = strtoupper($this->globals['status']);
                    $userId = $users[0]['id'];

                    $employeeId = null;
                    try {
                        $employeeId = $this->model->insert([
                            'name' => $name,
                            'jobDescription' => $jobDescription,
                            'startDate' => date('Y-m-d H:i:s', strtotime($startDate)),
                            'status' => $status,
                            'userId' => $userId,
                        ]);
                    } catch (PDOException $e) {
                        return include __DIR__.'/../views/edit-employee.php';
                    }
                    $_SESSION[self::EMPLOYEE_SUCCESS] = 'Employee was added successfully!';
                    header("Location: ../employee/$employeeId");
                }
                break;
        }
        return false;
    }

    public function getById(int $id)
    {
        $requestMethod = $this->globals['REQUEST_METHOD'];

        if ($requestMethod === 'GET') {
            $username = $this->globals[AuthController::USER_SESSION];
            $userModel = new AuthModel();
            $users = $userModel->fetchBy([
                'username' => $username
            ]);

            if (count($users) === 1) {
                $userId = $users[0]['id'];

                $employees = $this->model->fetchBy([
                    'id' => $id,
                    'userId' => $userId
                ]);

                if (count($employees) === 1) {
                    $employee = $employees[0];

                    $id = $employee['id'];
                    $name = $employee['name'];
                    $userId = $employee['userId'];

                    $_SESSION[self::EMPLOYEE_NAME] = $name;

                    $jobDescription = $employee['jobDescription'];
                    $startDate = $employee['startDate'];
                    $status = $employee['status'];

                    $oldValues = [
                        'id' => $id,
                        'name' => $name,
                        'jobDescription' => $jobDescription,
                        'startDate' => date('Y-m-d\TH:i:s', strtotime($startDate)),
                        'status' => $status
                    ];

                    return include __DIR__.'/../views/edit-employee.php';
                }
            }
        }
        return false;
    }

    public function edit(int $id): bool {
        $requestMethod = $this->globals['REQUEST_METHOD'];

        if ($requestMethod === 'POST') {
            $username = $this->globals[AuthController::USER_SESSION];
            $userModel = new AuthModel();
            $users = $userModel->fetchBy([
                'username' => $username
            ]);

            if (count($users) === 1) {
                $userId = $users[0]['id'];

                $employees = null;
                $errors = [];
                $oldValues = [];
                try {
                    $employees = $this->model->fetchBy([
                        'id' => $id,
                        'userId' => $userId
                    ]);
                } catch (PDOException $e) {
                    $errors['code'] = $e->getCode();
                    $errors['message'] = $e->getMessage();
                    return __DIR__.'/../views/edit-employee.php';
                }

                if (count($employees) === 1) {
                    $name = $this->globals['name'];
                    $jobDescription = $this->globals['jobDescription'];
                    $startDate = $this->globals['startDate'];
                    $status = strtoupper($this->globals['status'] ?? 'FULL-TIME');

                    $oldValues = [
                        'id' => $id,
                        'name' => $name,
                        'jobDescription' => $jobDescription,
                        'startDate' => date('Y-m-d\TH:i:s', strtotime($startDate)),
                        'status' => $status,
                    ];

                    try {
                        $this->model->update(
                            [
                                'name' => $name,
                                'jobDescription' => $jobDescription,
                                'startDate' => date('Y-m-d H:i:s', strtotime($startDate)),
                                'status' => $status
                            ], [
                                'id' => $id,
                                'userId' => $userId
                            ]
                        );
                    } catch (PDOException $e) {
                        $errors['code'] = $e->getCode();
                        $errors['message'] = $e->getMessage();
                        $_SESSION[self::EMPLOYEE_ERROR] = 'Unable to update this employee';
                        header("Location: ../../employee/$id");
                    }

                    $_SESSION[self::EMPLOYEE_SUCCESS] = 'Employee information was updated successfully!';
                    header("Location: ../../employee/$id");
                }
            }
        }
        header("Location: ../../employee/$id");
        return false;
    }

    public function delete(int $id)
    {
        $requestMethod = $this->globals['REQUEST_METHOD'];

        if ($requestMethod === 'DELETE') {
            $username = $this->globals[AuthController::USER_SESSION];
            $userModel = new AuthModel();
            $users = $userModel->fetchBy([
                'username' => $username
            ]);

            if (count($users) === 1) {
                $userId = $users[0]['id'];

                $employees = null;
                try {
                    $employees = $this->model->fetchBy([
                        'id' => $id,
                        'userId' => $userId
                    ]);
                } catch (PDOException $e) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode([
                        'status' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ],
                        'data' => []
                    ]);
                    return;
                }

                if (count($employees) === 1) {
                    $employee = $employees[0];

                    try {
                        $this->model->delete([
                            'id' => $id,
                            'userId' => $userId
                        ]);
                    } catch (PDOException $e) {
                        header('HTTP/1.1 409 Conflict');
                        echo json_encode([
                            'status' => [
                                'code' => $e->getCode(),
                                'message' => $e->getMessage()
                            ],
                            'data' => []
                        ]);
                        return;
                    }

                    header('HTTP/1.1 200 Ok');
                    echo json_encode([
                        'status' => [
                            'code' => 200,
                            'message' => 'Employee "'.$employee['name'].'" was deleted successfully'
                        ],
                        'data' => $employee
                    ]);
                } else {
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode([
                        'status' => [
                            'code' => 404,
                            'message' => "Employee with id = $id was not found in the database"
                        ],
                        'data' => []
                    ]);
                }
            }
        }
    }
}