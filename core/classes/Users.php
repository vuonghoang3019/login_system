<?php
    class Users
    {
        protected $db;

        public function __construct()
        {
            $this->db = Database::instance();
        }
        public function get($table, $fields = array())
        {
            $columns = implode(', ', array_keys($fields));
            $sql = "SELECT * FROM {$table} WHERE {$columns} = :{$columns}";
            if($stmt = $this->db->prepare($sql))
            {
                foreach ($fields as $key => $value)
                {
                    $stmt->bindValue(":{$key}",$value);
                }
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_OBJ);
            }
        }
        public function emailExist($email)
        {
			$email = $this->get('users', array('email' => $email));
			return ((!empty($email))) ? $email : false;
        }
        public function usernameExist($username)
        {
            $username = $this->get('users', array('username' => $username));
			return ((!empty($username))) ? $username : false;
        }
        public function hash($password)
        {
            return password_hash($password, PASSWORD_BCRYPT);
        }
        public function redirect($location)
        {
            header("Location: ".BASE_URL.$location);
        }
        public function userData($user_id)
        {
            return $this->get('users', array('user_id' => $user_id));       
        }
        public function logout()
        {
            $_SESSION = array();
            session_destroy();
            $this->redirect('index.php');
        }
        public function isLoggedIn()
        {
            return ((isset($_SESSION['user_id']))) ? true : false;
        }
        public function update($table, $fields, $condition){
			$columns  = '';
			$where    = " WHERE ";
			$i        = 1;
			//create columns
			foreach($fields as $name => $value)
			{
				$columns .= "`{$name}` = :{$name}";
				if($i < count($fields)){
					$columns .= ", ";
				}
				$i++;
			}
			//create sql query
			$sql = "UPDATE {$table} SET {$columns}";
			//adding where condition to sql query
			foreach($condition as $name => $value){
				$sql .= "{$where} `{$name}` = :{$name}";
				$where = " AND ";
			}
			//check if sql query is prepared
			if($stmt = $this->db->prepare($sql)){
				foreach ($fields as $key => $value) {
					//bind columns to sql query
					$stmt->bindValue(":{$key}", $value);
					foreach ($condition as $key => $value) 
					{
						$stmt->bindValue(":{$key}", $value);
					}
				}
				//execute the query
				$stmt->execute();
			}
		}
        public function insert($table, $fields = array())
		{
			$columns = implode(", ", array_keys($fields));
			$values  = ":".implode(", :", array_keys($fields));
			//sql query
            $sql = "INSERT INTO {$table} ({$columns}) VALUES({$values})";
			//check if sql is prepared
			if($stmt = $this->db->prepare($sql)){
				//bind values to placeholders
				foreach ($fields as $key => $value)
				{
					$stmt->bindValue(":{$key}", $value);
				}
				//execute
				$stmt->execute();
				//return user_id
				return $this->db->lastInsertId();
            }
        }
        public function _debug($data)
        {
        echo '<pre style="background: #000;color: #fff;width:100%;overflow:auto"';
        echo '<div>Your ID: '.$_SERVER['REMOTE_ADDR'].'</div>';
        $debug_backtrace = debug_backtrace();
        $debug = array_shift($debug_backtrace);
        echo '<div>File: '.$debug['file'].'</div>';
        echo '<div>Line: '.$debug['file'].'</div>';
        if(is_array($data) || is_object($data))
        {
            print_r($data);
        }
        else
        {
            var_dump($data);
        }
        echo '</pre>';
         }
        
        
    }
?>
