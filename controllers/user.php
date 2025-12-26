<?php


namespace User;

use Exception;
use Connection\Connection;

class User
{
    private $fullname;
    private $email;
    private $password;
    private $confirm_password;

    public function __construct($email , $password , $fullname = null , $confirm_password = null)
    {
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
        $this->confirm_password = $confirm_password;
    }

    function create()
    {

        try {

            $connection = Connection::connect_PDO();
            $ERRORS = [] ;
            if (!preg_match('/^[ a-zA-Z0-9_-]{3,}$/', $this->fullname)) $ERRORS["fullname"] = 'name is required and contain at least 3 characters';
            if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $this->email)) $ERRORS["email"] = 'email is invalid';
            if (!preg_match('/^[^\s]{8,}$/', $this->password)) $ERRORS["password"] = 'password is required and contain at least 8 characters';
            if ($this->confirm_password != $this->password) $ERRORS["confirm_password"] = 'confirmed password should be idontical with password';
            

            //EMAIL VERIFICATION
            $get_user_statment = $connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1 ");
            $get_user_statment->execute([$this->email]);
            $row = $get_user_statment->fetch() ;

            if ($row) $ERRORS["email"] = 'email is already used';
            if (count($ERRORS)) throw new Exception('nvalid data') ;            
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

            $statment = $connection->prepare("INSERT INTO users (full_name , email , password) VALUES (? , ? , ? )");

            if (!$statment) throw new Exception('invalid statment');
            $status = $statment->execute([$this->fullname, $this->email, $hashed_password]);

            if (!$status) {
                $errorinfo = $statment->errorInfo() ;
                throw new Exception('SQL execution error: ' . $errorinfo[2]);
            }

            //GET USER DATA
            $get_user_statment= $connection->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $get_user_statment->execute([$this->email]);
            $row_authuser = $get_user_statment->fetch();


            $_SESSION['AuthUser'] = $row_authuser;
            $_SESSION['success'] = ["message" => "user created with success"];
            return $row_authuser; // return the created object 

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return $ERRORS;
        }
    }

    public static function login($email , $password)
    {
        try {

            $connection = Connection::connect_PDO();
            $ERRORS = [] ;
            
            if (empty($email)) $ERRORS["email"] = 'email is required';
            if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) $ERRORS["email"] = 'email is invalid';

            if (empty($password)) $ERRORS["password"] = 'password is required';
            if (!preg_match('/^[^\s]{8,}$/', $password)) $ERRORS["password"] = 'password is required and contain at least 8 characters';

            if (count($ERRORS)) {
                throw new Exception('invalid data');
                $_SESSION['error'] = $ERRORS;
            }

            $statment = $connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1  ");

            if (!$statment) {
                $ERRORS['error'] = "statment is not correct";
                throw new Exception('statment is not correct');
            }

            $status = $statment->execute([$email]);

            if (!$status) {
                $errorInfo = $statment->errorInfo() ;
                $ERRORS['error'] = "sql error :" . $errorInfo[2] ;
                throw new Exception('sql error');
            }

            $row = $statment->fetch();

            if (!$row) {
                $ERRORS['error'] = "email doesn't exists";
                throw new Exception('email doesn\'t exists');
            }

            if (!password_verify($password, $row['password'])) {
                $ERRORS['error'] = "password is not correct";
                throw new Exception('password is not correct');
            }

            $_SESSION['success'] = 'user loged in successfuly';
            $_SESSION['AuthUser'] = $row;
            return $row;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return $ERRORS;
        }
    }

    static function logout()
    {
        unset($_SESSION['AuthUser']);
        $_SESSION['success'] = 'log out with success';
        return true;
    }
}

