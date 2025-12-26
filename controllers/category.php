<?php

namespace Category;



use Connection\Connection;
use Exception;

class Category
{

    private $name;
    private $limits;
    private $description;

    public function __construct($name, $limits, $description = null)
    {
        $this->name = $name;
        $this->limits = $limits;
        $this->description = $description;
    }


    public function create()
    {
        try {

            $ERRORS = [];
            $connection = Connection::connect_PDO();

            !empty($this->name) ? $name = trim($this->name) : $name = null;
            !empty($this->limits) ? $limits = trim($this->limits) : $limits = null;
            !empty($this->description) ? $description = trim($this->description) : $description = null;

            if (!$name) $ERRORS['name'] = 'name is required';
            if (!$limits) $ERRORS['limits'] = 'limits is required';

            // IF DATA IS MESSING
            if (count($ERRORS)) throw new Exception('messing values');

            if (!preg_match('/^[a-zA-Z0-9\s.,!?-]{3,100}$/', $name)) $ERRORS['name'] = 'Name must be 3-100 characters with valid characters';

            if (!$limits) {
                $ERRORS['limits'] = 'limits is required';
            } else if ($limits <= 0) {
                $ERRORS['limits']  = 'limits can\'t be negative or null';
            } else if (!preg_match('/^\d+(\.\d{1,2})?$/', $limits)) {
                $ERRORS['limits'] = 'limits is invalid';
            }

            // IF DATA IS INVALID
            if (count($ERRORS)) {
                $_SESSION['error'] = $ERRORS;
                throw new Exception('invalid values');
            }

            $limits = floatval($limits);
            $statement = $connection->prepare('INSERT INTO categories (name , limits , description , user_id) VALUES( ? , ? , ? , ? )');

            if (!$statement) {
                $ERRORS['connection'] = "connection: " . $connection->errorInfo()[2];
                throw new Exception('connection error');
            }

            $id = $_SESSION['AuthUser']['id'];
            $status = $statement->execute([$name, $limits, $description, $id]);


            if (!$status) {
                $ERRORS['error'] = "sql error : " . $statement->errorInfo()[2];
                throw new Exception('sql error');
            }

            //CREATION SUCCEED
            $_SESSION['success'] = 'category created successfully';
            return true;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public static function delete($id = null)
    {
        try {

            $ERRORS = [];
            $connection = Connection::connect_PDO();


            if (!$id) {
                $ERRORS['id'] = 'id is required';
                throw new Exception('id is required');
            } else if (!preg_match('/^[1-9][0-9]*$/', $id)) {
                $ERRORS['id'] = 'id is unvalid regex';
                throw new Exception('id is unvalid');
            }


            $statement = $connection->prepare('DELETE from categories WHERE id = ?');
            $status = $statement->execute([$id]);

            if (!$status) {
                $ERRORS['error'] = "sql error : " . $statement->errorInfo()[2];
                throw new Exception('sql error');
            }

            $_SESSION['success'] = 'category deleted successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public static function filter($id = null)
    {
        try {
            $ERRORS = [];
            $connection = Connection::connect_PDO();

            if (!$id) {
                $ERRORS['id'] = 'id is required';
                throw new Exception('id is required');
            } else if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                $ERRORS['id'] = 'id is unvalid regex';
                throw new Exception('id is unvalid');
            }


            $fetch_incomes = $connection->prepare('SELECT * from incomes 
                WHERE category_id = ? 
                ORDER BY updated_at DESC 
            
            ');

            $fetch_expenses = $connection->prepare('SELECT * from expenses 
                WHERE category_id = ? 
                ORDER BY updated_at DESC
            
            ');

            $fetch_incomes_result = $fetch_incomes->execute([$id]);
            $fetch_expenses_result = $fetch_expenses->execute([$id]);


            if (!$fetch_incomes_result) {
                $ERRORS['error'] = "sql error : " . $fetch_incomes->errorInfo()[2];
                throw new Exception('sql error');
            }

            if (!$fetch_expenses_result) {
                $ERRORS['error'] = "sql error : " . $fetch_expenses->errorInfo()[2];
                throw new Exception('sql error');
            }

            $data = ['incomes' => $fetch_incomes->fetchAll(), 'expenses' => $fetch_expenses->fetchAll()];
            return $data;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return $ERRORS;
        }
    }

    public static function fetchAllCat(){
        $connection = Connection::connect_PDO() ;
        $statement = $connection->prepare('SELECT * from categories where user_id = ?') ;
        $statement->execute([$_SESSION['AuthUser']['id']]) ;
        return $statement->fetchAll() ;    
    }
}




