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
}
