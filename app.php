<?php

use App\Controllers\UserController;

include 'vendor/autoload.php';
$dbh = new PDO('mysql:host=localhost;dbname=test', 'root','');
$path = $_SERVER['REQUEST_URI'];

switch ($path) {
    case '/user/table':
        $user = new UserController();
        $user->getUsers();
        break;
    case '/user/registration':
        $user = new UserController();
        $user->showRegistration();
        break;

}

//var_dump($_SERVER['REQUEST_URI']);

//$loader = new FilesystemLoader('template/');
//$twig = new Environment($loader);
//$template = $twig->load('index.html.twig');
//
//
//
//echo $template->render(['table' => $table]);



