<?php
class User
{
    // private -  can only be used BY this class
    // protected - make property/method visible in all classes that extend current class including the parent class    
    protected $pdo;

    // A constructor allows you to initialize an object's properties upon creation of the object.
    // If you create a __construct() function, PHP will automatically call this function when you create an object from a class.
    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function checkInput($variable)
    {
        $variable = htmlspecialchars($variable);
        $variable = trim($variable);
        $variable = stripslashes($variable);
        return $variable();
    }
    public function checkEmail($email_mobile)
    {
        $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email_mobile, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function create($table, $fields = array())
    {
        $columns = implode(',', array_keys($fields));
        $values = ':' . implode(', :', array_keys($fields));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";

        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($fields as $key => $data) {
                $stmt->bindValue(':' . $key, $data);
            }
            $stmt->execute();
            return $this->pdo->lastInsertedId();
        }
    }
}
