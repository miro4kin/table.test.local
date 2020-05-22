<?php

namespace App\Controllers;

use App\Services\UserService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class UserController
{


    public function getUsers()
    {

        $userService = new UserService();
        $users = $userService->selectUsers();

        var_dump($users);
        die();

        $loader = new FilesystemLoader('template/');
        $twig = new Environment($loader);
        $template = $twig->load('usersTable.html.twig');

        echo $template->render(['users' => $users]);
    }

    public function showRegistration()
    {
        $result = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            $userService = new UserService();
            $result = $userService->insertUser();
        }

        $loader = new FilesystemLoader('template/');
        $twig = new Environment($loader);
        $template = $twig->load('registration.html.twig');

        echo $template->render(['result'=> $result]);

    }

}