<?php
class Test extends Controller {
    public function __construct(){
        echo "Test controller loaded successfully!<br>";
    }

    public function index(){
        echo "Test index method called!<br>";
        echo "Controller: Test<br>";
        echo "Method: index<br>";
        echo "Params: " . print_r(func_get_args(), true);
    }

    public function hello($name = 'World'){
        echo "Hello, $name!<br>";
        echo "This is a test method with parameter.<br>";
    }
}
?>