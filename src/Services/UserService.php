<?php

namespace App\Services;


use PDO;
use PDOException;

class UserService
{

    public function __construct()
    {s

    }

    private function conn()
    {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $db_name = 'myproject';
        return new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
    }

    public function getUsers(): array
    {
        return $this->selectUsers();
    }

    public function selectUsers()
    {
        try {
            $result = $this->conn()->query('SELECT * FROM user_list');
            $users = [];
            while ($user = $result->fetch(\PDO::FETCH_ASSOC)) {
                $users[] = $user;
            }
            return $users;
        } catch (PDOException $e) {
            return "Ошибка!: " . $e->getMessage();
        }
    }

    public function insertUser()
    {
        $result = [
            'message' => '',
            'status' => true
        ];

        if (!isset($_POST['registration'])) {
            $result['status'] = false;
            $result['message'] = 'Отсутствуют POST данные';
            return $result;
        }

        $result = $this->isValid($_POST['registration']);

        if (!$result['status']) {
            return $result;
        }

        $this->createTable();

        $result = $this->insertUserSql($_POST['registration']);

        return $result;
    }

    public function createTable()
    {
        $result = [
            'message' => '',
            'status' => true
        ];
        try {
            $query = 'CREATE TABLE IF NOT EXISTS `user_list` (';
            $query .= '`id` INT NOT NULL AUTO_INCREMENT,';
            $query .= '`name`   VARCHAR(256),';
            $query .= '`login`    VARCHAR(256) NOT NULL,';
            $query .= '`password` VARCHAR(256) NOT NULL,';
            $query .= '`email` VARCHAR(256) NOT NULL,';
            $query .= '`registration_date` DATETIME DEFAULT NOW(),';
            $query .= 'PRIMARY KEY (`id`))';

            $answer = $this->conn();
            $answer->exec($query);
            return $answer;
        } catch (PDOException $e) {
            return "Ошибка!: " . $e->getMessage();
        }

    }

    public function isValid($formParams)
    {
        $result = [
            'status' => true,
            'message' => ''
        ];

        if (empty($formParams['login'])) {
            $result['status'] = false;
            $result['message'] = 'Отсутствует логин пользователя';
        }

        if (empty($formParams['password'])) {
            $result['status'] = false;
            $result['message'] = 'Отсутствует пароль пользователя';
        }

        if (empty($formParams['email'])) {
            $result['status'] = false;
            $result['message'] = 'Отсутствует email пользователя';
        }
        // проверить на емаил
        if (!preg_match('/^[a-z0-9_.-]+@[a-z_.-]+\.[a-z]{2,}$/', $formParams['email'])) {
            $result['status'] = false;
            $result['message'] = 'email пользователя некорректен';
        }

        if (empty($formParams['name'])) {
            $result['status'] = false;
            $result['message'] = 'Отсутствует Ф.И.О пользователя';
        }

        if (!is_string($formParams['name'])) {
            $result['status'] = false;
            $result['message'] = 'Ф.И.О пользователя содержит недопустимые символы';
        }
        if (!preg_match("/^[а-яёa-z\s]+$/iu", $formParams['name'])) {
            $result['status'] = false;
            $result['message'] = 'Ф.И.О пользователя содержит недопустимые символы';
        }

        return $result;
    }

    public function insertUserSql($user)
    {
        $result = [
            'message' => '',
            'status' => true
        ];
        try {
            $query = 'INSERT INTO `user_list` (`name`, `login`, `password`, `email`)';
            $query .= 'VALUES (';
            $query .= "'{$user['name']}',";
            $query .= "'{$user['login']}',";
            $query .= "'{$user['password']}',";
            $query .= "'{$user['email']}')";

            $newUser = $this->conn();
            $newUser->exec($query);
            return $newUser;
        } catch (PDOException $e) {
            return "Ошибка!: " . $e->getMessage();
        }
    }

    public function insertJsonUser()
    {
        $user = $_POST;
        unset($user['registration']);
        $user['registration_date'] = date('Y-m-d H:i:s');

        if (file_exists('user.json') === true) {
            $file = file_get_contents('user.json');
            $userList = json_decode($file, true);
            $users = $userList;
        }
        $users[] = $user;
        $userList = json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return file_put_contents('user.json', $userList);
    }

//
//    public function getTable()
//    {
//        $link = $this->conn();
//        $query = "SELECT * FROM `user_list`";
//        $result = mysqli_query($link, $query) or die(mysqli_error($link));
//        $table = [];
//        while ($row = mysqli_fetch_assoc($result)) {
//            $table[] = $row;
//        }
//        return $table;
//    }
//    public function __construct()
//    {
//        if (isset($_POST['registration'])) {
//            if (!$this->isValid($_POST)) {
//                echo "Не все данные введены";
//            }
//
//            $link =$this->conn();
//            $this->createTable($link);
//            if ($this->isPost() && $this->isValid($_POST)) {
//                $this->insertUser($link);
//                $this->insertJsonUser();
//            }
//        }
//    }

//    private function isPost()
//    {
//        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//            return true;
//        }
//        return false;
//    }
//
//    public function isValid($formParams)
//    {
//        foreach ($formParams as $key => $value) {
//            if ($value === '') {
//                return false;
//            }
//        }
//        return true;
//    }
//
//    public function insertUser($data)
//    {
//        $this->createTable();
//        $user = $_POST;
//        $newUser = mysqli_query($link, "INSERT INTO `user_list` (`name`, `login`, `password`, `email`)
//        VALUES ( '{$user['name']}', '{$user['login']}', '{$user['password']}', '{$user['email']}' )");
//        if ($newUser) {
//            header('Location: http://localhost/StudyProject/registration/table.html');
//        } else {
//            echo 'Произошла ошибка' . mysqli_error($link);
//        }
//
//    }
//
//    private function createTable()
//    {
//        $query = "CREATE TABLE IF NOT EXISTS `user_list`
//(
//    `id`     INT NOT NULL AUTO_INCREMENT,
//    `name`   VARCHAR(256),
//    `login`    VARCHAR(256) NOT NULL,
//    `password` VARCHAR(256) NOT NULL,
//    `email` VARCHAR(256) NOT NULL,
//    `registration_date` DATETIME DEFAULT NOW(),
//    PRIMARY KEY (`id`)
//)";
//        mysqli_query($this->conn(), $query);
//        if (mysqli_query($this->conn(), $query)) {
//            return 'Таблица создана';
//        }
//        return mysqli_error($this->conn());
//    }
//
//
//    public function getTable()
//    {
//        $link = $this->conn();
//        $query = "SELECT * FROM `user_list`";
//        $result = mysqli_query($link, $query) or die(mysqli_error($link));
//        $table = [];
//        while ($row = mysqli_fetch_assoc($result)) {
//            $table[] = $row;
//        }
//        return $table;
//    }
//
//    private function insertJsonUser()
//    {
//        $user = $_POST;
//        unset($user['registration']);
//        $user['registration_date'] = date('Y-m-d H:i:s');
//
//        if (file_exists('user.json') === true) {
//            $file = file_get_contents('user.json');
//            $userList = json_decode($file, true);
//            $users = $userList;
//        }
//        $users[] = $user;
//        $userList = json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//        return file_put_contents('user.json', $userList);
//    }
}