<?php
 
class MySQLDAO
{
  
    var $dbhost = null;
    var $dbuser = null;
    var $dbpass = null;
    var $conn = null;
    var $dbname = null;
    var $result = null;

    function __construct($dbhost, $dbuser, $dbpassword, $dbname) {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpassword;
        $this->dbname = $dbname;
    }  
    
    
    public function openConnection() { 
        $this->conn = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        if (mysqli_connect_errno())
            throw new Exception("Could not establish connection with database");
        $this->conn->set_charset("utf8");
    }
    
    public function closeConnection() {
        if ($this->conn != null)
            $this->conn->close();
    }
    
    
      public function getUserDetails($email)
    {
        $returnValue = array();
        $sql = "select * from users where email='" . $email . "'";
  
        $result = $this->conn->query($sql);
        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if (!empty($row)) {
                $returnValue = $row;
            }
        }
        return $returnValue;
    }   
    
     public function registerUser($email, $first_name, $last_name, $password, $salt)
    { 
        $sql = "insert into users set email=?, first_name=?, last_name=?, user_password=?, salt=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("sssss", $email, $first_name, $last_name, $password, $salt);
        $returnValue = $statement->execute();

        return $returnValue;  
    }   
    
    
     public function storeEmailToken($user_id, $email_token)
    { 
        $sql = "insert into email_tokens set user_id=?, email_token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("is", $user_id, $email_token);
        $returnValue = $statement->execute();

        return $returnValue;  
    } 
    
    
    function getUserIdWithToken($emailToken)
    {
        $returnValue = array();
        $sql = "select user_id from email_tokens where email_token='" . $emailToken . "'";
  
        $result = $this->conn->query($sql);
        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if (!empty($row)) {
                $returnValue = $row['user_id'];
            }
        }
        return $returnValue;  
        
    }
    
    function setEmailConfirmedStatus($status, $user_id)
    {
        $sql = "update users set isEmailConfirmed=? where user_id=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("ii", $status, $user_id);
        $returnValue = $statement->execute();  
        
        return $returnValue;
        
    }
    
    
    function deleteUsedToken($emailToken)
    {
        $sql = "delete from email_tokens where email_token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("s", $emailToken);
        $returnValue = $statement->execute();  
        
        return $returnValue;
    }
    
    
    public function storePasswordToken($user_id, $token)
    {
        $sql = "insert into password_tokens set user_id=?, password_token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("is", $user_id, $token);
        $returnValue = $statement->execute();
        
        return $returnValue;
        
    }  
 
    
    function getUserIdWithPasswordToken($token)
    {
        $returnValue = null;
        $sql = "select user_id from password_tokens where password_token='" . $token . "'";
  
        $result = $this->conn->query($sql);
        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if (!empty($row)) {
                $returnValue = $row['user_id'];
            }
        }
        return $returnValue;  
    }
    
    function updateUserPassword($user_id,$secured_password,$salt)
    {
        $sql = "update users set user_password=?, salt=? where user_id=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("ssi", $secured_password, $salt, $user_id);
        $returnValue = $statement->execute();
        
        return $returnValue;    
    }
    
    function deleteUsedPasswordToken($token)
    {
        $sql = "delete from password_tokens where password_token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("s", $token);
        $returnValue = $statement->execute();
        
        return $returnValue;  
    }
    
 
    public function searchFriends($searchWord)
    {
        $returnValue = array();
        
        $sql = "select * from friends where 1";
       
        if(!empty($searchWord))
        {
            $sql .= " and ( first_name like ? or last_name like ? )";
        }
  
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        if(!empty($searchWord))
        {
          $searchWord = '%' . $searchWord . "%";
          $statement->bind_param("ss",  $searchWord , $searchWord);
        }
        
        $statement->execute();
       
        $result = $statement->get_result();
        
         while ($myrow = $result->fetch_assoc()) 
         {
           $returnValue[] = $myrow;
         }
         
        return $returnValue;
    } 
    
    function deleteFriendRecord($friend_token) 
    {
        $sql = "delete from friends where token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("s", $friend_token);
        $statement->execute();
        
        $returnValue = $statement->affected_rows;
        
        return $returnValue;  
    }
    
}

?>
