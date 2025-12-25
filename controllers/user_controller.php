<?php


namespace User;

require_once '../connection/connection.php';
session_start();
unset($_SESSION['success'], $_SESSION['error']);

use Exception;
use Connection\Connection;

class User
{
    static $fullname;
    static $email;
    static $password;
    static $confirm_password;
    static $ERRORS = [];

    static function create($fullname = null,  $email = null, $password = null, $confirm_password = null)
    {
        self::$fullname = $fullname ?? (isset($_POST["fullname"]) ? trim($_POST["fullname"]) : '');
        self::$password = $password ?? (isset($_POST["password"]) ? trim($_POST["password"]) : '');
        self::$email    = $email ?? (isset($_POST["email"]) ? trim($_POST["email"]) : '');
        self::$confirm_password  = $confirm_password ?? isset($_POST["confirm_password"]) ? trim($_POST["confirm_password"]) : '';

        if (!preg_match('/^[ a-zA-Z0-9_-]{3,}$/', self::$fullname)) self::$ERRORS["fullname"] = 'name is required and contain at least 3 characters';
        if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', self::$email)) self::$ERRORS["email"] = 'email is invalid';
        if (!preg_match('/^[^\s]{8,}$/', self::$password)) self::$ERRORS["password"] = 'password is required and contain at least 8 characters';
        if (self::$confirm_password != self::$password) self::$ERRORS["confirm_password"] = 'confirmed password should be idontical with password';

        try {
            $connection = Connection::connect();
            //EMAIL VERIFICATION
            $get_user_statement = $connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1  ");
            $get_user_statement->bind_param('s', self::$email);
            $get_user_statement->execute();
            $result = $get_user_statement->get_result();
            $row = $result->fetch_assoc();

            if ($row) self::$ERRORS["email"] = 'email is already used';

            if (count(self::$ERRORS)) {
                throw new Exception('nvalid data');
            }

            self::$email = filter_var(self::$email, FILTER_VALIDATE_EMAIL);
            $hashed_password = password_hash(self::$password, PASSWORD_DEFAULT);


            $statment = $connection->prepare("INSERT INTO users (full_name , email , password) VALUES (? , ? , ? )");

            if (!$statment) {
                throw new Exception('invalid statment');
            }

            $statment->bind_param('sss', self::$fullname, self::$email, $hashed_password);
            $status = $statment->execute();
            // $statment->close();

            //GET USER DATA
            $get_user_statement->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $get_user_statement->bind_param('s', self::$email);
            $get_user_statement->execute();
            $result_authuser = $get_user_statement->get_result();
            $row_authuser = $result_authuser->fetch_assoc();

            if (!$status) {
                throw new Exception('sql error ' . $statment->error);
            }

            $_SESSION['AuthUser'] = $row_authuser;
            $connection->close();
            $_SESSION['success'] = ["message" => "user created with success"];
            return $row_authuser; // return the created object 

        } catch (Exception $e) {
            $connection->close();
            $_SESSION['error'] = $e->getMessage();
            return self::$ERRORS;
        }
    }

    static function login($email = null, $password = null)
    {
        try {

            $connection = Connection::connect();
            self::$password = $password ?? isset($_POST["password"]) ? trim($_POST["password"]) : '';
            self::$email    = $email ?? isset($_POST["email"]) ? trim($_POST["email"]) : '';

            if (empty(self::$email)) self::$ERRORS["email"] = 'email is required';
            if (empty(self::$password)) self::$ERRORS["password"] = 'password is required';
            if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', self::$email)) self::$ERRORS["email"] = 'email is invalid';
            if (!preg_match('/^[^\s]{8,}$/', self::$password)) self::$ERRORS["password"] = 'password is required and contain at least 8 characters';

            if (count(self::$ERRORS)) {
                throw new Exception('invalid data');
                $_SESSION['error'] = self::$ERRORS;
            }

            $statement = $connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1  ");

            if (!$statement) {
                self::$ERRORS['error'] = "statement is not correct";
                throw new Exception('statement is not correct');
            }

            $statement->bind_param('s', self::$email);
            $status = $statement->execute();

            if (!$status) {
                self::$ERRORS['error'] = "sql error :" . $statement->error;
                throw new Exception('sql error');
            }

            $result = $statement->get_result();
            $row = $result->fetch_assoc();

            if (!$row) {
                self::$ERRORS['error'] = "email doesn't exists";
                throw new Exception('email doesn\'t exists');
            }

            if (!password_verify(self::$password, $row['password'])) {
                self::$ERRORS['error'] = "password is not correct";
                throw new Exception('password is not correct');
            }

            $_SESSION['success'] = 'user loged in successfuly';
            $_SESSION['AuthUser'] = $row;
            $connection->close();
            return $row;
        } catch (Exception $e) {
            $connection->close();
            $_SESSION['error'] = $e->getMessage();
            return self::$ERRORS;
        }
    }

    static function logout()
    {
        unset($_SESSION['AuthUser']);
        $_SESSION['success'] = 'log out with success';
        return true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['register'])) User::create();
    if (isset($_POST['login'])) User::login();
    if (isset($_POST['logout'])) User::logout();

    header('location: ../index.php');
}
