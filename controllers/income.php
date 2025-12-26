<?php

namespace Income;

use Connection\Connection;
use Exception;


class Income
{
    private $amount;
    private $description;
    private $category_id;

    public function __construct($amount = null, $category_id = null, $description = null)
    {
        $this->amount = $amount;
        $this->category_id = $category_id;
        $this->description = $description;
    }

    function create()
    {
        try {

            $ERRORS = [];
            $connection = Connection::connect_PDO();

            $amount = $this->amount ?? null;
            $description = $this->description ?? null;
            $category_id = $this->category_id ?? null;

            //VALIDATION AMOUNT
            if (!$amount) {
                $ERRORS['amount'] = 'amount is required';
            } else if ($amount < 0) {
                $ERRORS['amount']  = 'amount can\'t be negative';
            } else if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
                $ERRORS['amount'] = 'amount is invalid';
            }


            if (!$category_id) {
                $ERRORS['category_id'] = 'category_id is required';
            } else if (!intval($category_id)) {
                $ERRORS['category_id'] = 'category_id is not valid';
            } else if ($category_id <= 0) {
                $ERRORS['category_id']  = 'category_id can\'t be negative or null';
            }

            //IF THERE IS AN ERROR
            if (count($ERRORS)) {
                $_SESSION['error'] = $ERRORS;
                throw new Exception('invalid data');
            }

            //PREPARE STATEMENT TO CREATE NEW INCOME
            $stat = $connection->prepare('INSERT INTO incomes (amount, description , category_id ) VALUES (? , ?  , ?)');

            if (!$stat) {
                $ERRORS['error'] = ["Database error: " . $connection->errorInfo()[2]];
                throw new Exception('Database error');
            }

            $status = $stat->execute([$amount, $description, $category_id]);

            if (!$status) {
                $ERRORS['error'] = ['creation failed: ' . $stat->errorInfo()[2]];
                throw new Exception('creation failed');
            }

            $_SESSION['success'] = 'income created successfully';
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public static function delete($id)
    {
        try {

            $ERRORS = [];
            $connection = Connection::connect_PDO();

            //ID VALIDATION
            if (!$id) {
                $ERRORS['id'] = 'id is required';
            } else if (!intval($id) || $id <= 0) {
                $ERRORS['id'] = 'id must be a positive number';
            }

            //IF THERE IS AN ERROR
            if (count($ERRORS)) throw new Exception('invalid id');

            $statement = $connection->prepare('DELETE FROM incomes WHERE id = ? ');

            if (!$statement) {
                $ERRORS['error'] = ["Server error: " . $connection->errorInfo()[2]];
                throw new Exception('server error');
            }

            $state = $statement->execute([$id]);

            if (!$state) {
                $ERRORS['error'] = ["Database error: " . $connection->errorInfo()[2]];
                throw new Exception("Database error");
            }

            $_SESSION['success'] = 'income deleted successfully';
            return true;
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public function update($id, $amount = null, $description = null)
    {
        try {

            $ERRORS = [];
            $connection = Connection::connect_PDO();

            $params = [];
            $columns = [];
            // $type = '';

            //DATA VALIDATION

            if (!empty($amount)) {
                if ($amount < 0) {
                    $ERRORS['amount']  = 'amount can\'t be negative';
                } else if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
                    $ERRORS['amount'] = 'amount is invalid';
                } else {
                    $amount = floatval($amount);
                    $columns[] = $amount;
                    $params[] = 'amount = ?';
                }
            }

            if (!empty($description)) {
                $columns[] = $description;
                $params[] = 'description = ?';
            }

            if (!empty($category_id)) {
                $category_id  = intval($category_id);
                $columns[] = $category_id;
                $params[] = 'category_id = ?';
            }

            if (empty($id)) {
                $ERRORS['id'] = 'id is required';
            } else if (!is_numeric($id) || $id <= 0) {
                $ERRORS['id'] = 'id must be a positive number';
            } else {
                $id  = intval($id);
                $columns[] = $id;
            }

            //IF THERE IS AN ERROR
            if (count($ERRORS)) throw new Exception('invalid data');

            //IF THERE IS NOTHING TO UPDATE

            if (empty($amount) && empty($description) && empty($id_card)) {
                $ERRORS['error'] = 'ther is nothing to update';
                throw new Exception('invalid data');
            }

            //BUILD STATEMENT
            $stat = $connection->prepare("UPDATE incomes SET " . implode(', ', $params) . " WHERE id = ?");

            if (!$stat) {
                $ERRORS['error'] = "Database error: " . $connection->errorInfo()[2];
                throw new Exception('Database error');
            }

            $status = $stat->execute([...$columns]);

            if (!$status) {
                $ERRORS['error'] = 'update failed' . $stat->errorInfo()[2];
                throw new Exception('update failed');
            }

            //UPDATE SUCCEED
            $_SESSION['success'] = 'income updated successfully';
            return true;
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public static function getAll()
    {
        $connection = Connection::connect_PDO();
        $statement = $connection->prepare("SELECT i.* FROM incomes i
        INNER JOIN categories c ON c.id = i.category_id
        WHERE c.user_id = ?");

        $statement->execute([$_SESSION['AuthUser']['id']]) ;
        return $statement->fetchAll();

    }

    public static function getById($id)
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

            $statement = $connection->prepare("SELECT * FROM incomes WHERE id = ?");
            $statement->execute([$id]);
            return $statement->fetchAll();

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public static function getByCategory($category_id) {
        
        try {
            $ERRORS = [];
            $connection = Connection::connect_PDO();

            if (!$category_id) {
                $ERRORS['category_id'] = 'category_id is required';
                throw new Exception('id is required');
            } else if (!preg_match('/^[1-9][0-9]*$/', $category_id)) {
                $ERRORS['category_id'] = 'category_id is unvalid regex';
                throw new Exception('category_id is unvalid');
            }

            $statement = $connection->prepare("SELECT * FROM incomes WHERE category_id = ?");
            $statement->execute([$category_id]);
            return $statement->fetchAll();

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public static function get_total_incomes(){

        $connection = Connection::connect_PDO() ;
        $statement = $connection->prepare('SELECT sum(i.amount) as total_incomes FROM incomes i
        INNER JOIN categories c ON c.id = i.category_id
        WHERE c.user_id = ?');
        $statement->execute([$_SESSION['AuthUser']['id']]) ;
        return $statement->fetch() ;

    }

}
