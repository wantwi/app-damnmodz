<?php

class CounterController
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getAllCounters()
    {
        $dbHandler = $this->config->getDbHandler();
        $users = $dbHandler->selectAll('users');
        echo json_encode($users);
    }

    public function createUser()
    {
        http_response_code(201);
        echo json_encode(['message' => 'User created successfully']);
    }

    public function getCounterById($id)
    {
        $dbHandler = $this->config->getDbHandler();
        $user = $dbHandler->selectData('users', 'id', $id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit();
        }

        echo json_encode($user);
    }
}