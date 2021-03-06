<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \App\Config;
use \Core\View;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{


    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (name, email, password_hash)
                    VALUES (:name, :email, :password_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // Name
        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Invalid email';
        }
        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'email already taken';
        }

        // Password
        if (strlen($this->password) < 6) {
            $this->errors[] = 'Please enter at least 6 characters for the password';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Password needs at least one letter';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Password needs at least one number';
        }
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     * @param string $ignore_id Return false anyway if the record found has this ID
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	 

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Send password reset instructions to the user specified
     *
     * @param string $email The email address
     *
     * @return void
     */
    public static function sendPasswordReset($email)
    {
        $user = static::findByEmail($email);

        if ($user) {

            if ($user->startPasswordReset()) {

                $user->sendPasswordResetEmail();

            }
        }
    }

    /**
     * Start the password reset process by generating a new token and expiry
     *
     * @return void
     */
    protected function startPasswordReset()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 2;  // 2 hours from now

        $sql = 'UPDATE users SET password_reset_hash = :token_hash,
                    password_reset_expires_at = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Send password reset instructions in an email to the user
     *
     * @return void
     */
    protected function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($this->email, 'Password reset', $text, $html);
    }

    /**
     * Find a user model by password reset token and expiry
     *
     * @param string $token Password reset token sent to user
     *
     * @return mixed User object if found and the token hasn't expired, null otherwise
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users
                WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {

            // Check password reset token hasn't expired
            if (strtotime($user->password_reset_expires_at) > time()) {

                return $user;
            }
        }
    }

    /**
     * Reset the password
     *
     * @param string $password The new password
     *
     * @return boolean  True if the password was updated successfully, false otherwise
     */
    public function resetPassword($password)
    {
        $this->password = $password;

        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users
                    SET password_hash = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expires_at = NULL
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	public function loadIncomesCategories($user_id)
	{
		//$host = Config::DB_HOST;
		//$database = Config::DB_NAME;
		//$username =Config::DB_USER;
		//$password = Config::DB_PASSWORD;
				//try
				//{
					//$connect = new PDO ("mysql:host=$host;dbname=$database", $username, $password);
					//$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db = static::getDB();


            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			

            return $stmt->execute();
					
					//FETCH ALL DATA FROM TABLE
					for($x=1; $x<=4; $x++) 
					{
						//query
						$query = "SELECT name FROM incomes_category_default WHERE id='$x'";
						$stmt = $db->prepare($query);
						//$data = $connect->query($query);
						foreach ($stmt as $row)
						{
							$name = $row["name"];
							
							$sql=("INSERT INTO incomes_category_assigned_to_users (id, user_id, name) 
													VALUES (:id, :user_id, :name)");
							$stmt = $db->prepare($sql);

							$stmt->bindValue(':id', NULL, PDO::PARAM_INT);
							$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
							$stmt->bindValue(':name', $name, PDO::PARAM_STR);
							$stmt->execute();
						}
					}
				//}
				//catch(PDOException $error)
				//{
				//	$error->getMessage();
				//}
	}
	
	public function loadExpensesCategories($user_id)
	{
		//$host = Config::DB_HOST;
		//$database = Config::DB_NAME;
		//$username =Config::DB_USER;
		//$password = Config::DB_PASSWORD;
				//try
				//{
					//$connect = new PDO ("mysql:host=$host;dbname=$database", $username, $password);
					//$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					//FETCH ALL DATA FROM TABLE
					for($x=1; $x<=16; $x++) 
					{
						//query
						$query = "SELECT name FROM expenses_category_default WHERE id='$x'";
						$data = $connect->query($query);
						foreach ($data as $row)
						{
							$name = $row["name"];
							
							$sql=("INSERT INTO expenses_category_assigned_to_users (id, user_id, name) 
													VALUES (:id, :user_id, :name)");
							$db = static::getDB();
							$stmt = $db->prepare($sql);

							$stmt->bindValue(':id', NULL, PDO::PARAM_INT);
							$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
							$stmt->bindValue(':name', $name, PDO::PARAM_STR);
							$stmt->execute();
						}
					}
				//}
				
	}
	
	public function loadPaymentMethods($user_id)
	{
		$host = Config::DB_HOST;
		$database = Config::DB_NAME;
		$username =Config::DB_USER;
		$password = Config::DB_PASSWORD;
				try
				{
					$connect = new PDO ("mysql:host=$host;dbname=$database", $username, $password);
					$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					//FETCH ALL DATA FROM TABLE
					for($x=1; $x<=3; $x++) 
					{
						//query
						$query = "SELECT name FROM payment_methods_default WHERE id='$x'";
						$data = $connect->query($query);
						foreach ($data as $row)
						{
							$name = $row["name"];
							
							$sql=("INSERT INTO payment_methods_assigned_to_users (id, user_id, name) 
													VALUES (:id, :user_id, :name)");
							$db = static::getDB();
							$stmt = $db->prepare($sql);

							$stmt->bindValue(':id', NULL, PDO::PARAM_INT);
							$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
							$stmt->bindValue(':name', $name, PDO::PARAM_STR);
							$stmt->execute();
						}
					}
				}
				catch(PDOException $error)
				{
					$error->getMessage();
				}
	}
	
	public function getIDByMail($email)
	{
		$host = Config::DB_HOST;
		$database = Config::DB_NAME;
		$username =Config::DB_USER;
		$password = Config::DB_PASSWORD;	
		
		$connect = new PDO ("mysql:host=$host;dbname=$database", $username, $password);
		$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
					//FETCH DATA FROM TABLE
						$query = "SELECT id FROM users WHERE email='$email'";
						$data = $connect->query($query)->fetch(PDO::FETCH_ASSOC);
						$id=$data["id"];
						return $id;
	}
	
	public function getRowFromTableByMail($email)
	{
		$sql=("SELECT * FROM users WHERE email=:email");
		$db = static::getDB();
         $stmt = $db->prepare($sql);
         $stmt->bindValue(':email', $email, PDO::PARAM_INT);
         $result=$stmt->execute();
		 var_dump($result);
	}
	
}
