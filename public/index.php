<?php

// Создаём менеджер событий

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Postgresql;
use Phalcon\Config\Adapter\Ini ;


$loader = new Loader();

$loader->registerNamespaces(
    [
        "dopForma\models" => __DIR__ . "/../models/",
    //    "dopForma\controllers" => __DIR__ . "/controllers/",
    ]
);

$config = new Ini(
    "../config.ini"
);

//echo "<pre>".print_r($config->db->host,1)."</pre>";

$loader->register();

$di = new FactoryDefault(); 

$di->set(
    "db",
    function()use ($config){
        return new Postgresql([
   'host'     =>  $config->db->host,
   'dbname'   =>  $config->db->dbname,
   'port'     =>  $config->db->port,
   'username' =>  $config->db->username,
   'password' =>  $config->db->password
         ]);        
    }
); 
 
 
$app = new Micro($di);


include_once __DIR__."/../handlers/user.php";



$app->error(
    function ($ex) {
        echo " An error has occurred ".$ex->getMessage() ;
    }
);

try{
$app->handle();
}catch(Exception $e){
    echo $e->getMessage();
}

?>