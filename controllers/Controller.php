<?php

namespace Controllers;

use Models\Model;

abstract class Controller
{
    protected Model $model;
    protected array $globals;

    abstract function index();

    abstract function getById(int $id);
}