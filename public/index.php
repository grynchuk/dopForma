<?php
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//$d=json_decode(file_get_contents('php://input'),true);
//var_dump($d);
// Создаём менеджер событий

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Postgresql;
use Phalcon\Config\Adapter\Ini ;
use Phalcon\Http\Request;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use dopForma\models\user;
use dopForma\tools\useful;
use dopForma\tools\responses\factory as respFac;

$request = new Request();

$loader = new Loader();
 // 
$loader->registerNamespaces(
    [ 
        "dopForma\models" => __DIR__ . "/../models/",
        'dopForma\tools' => __DIR__.'/../tools/',
        'dopForma\interfaces' => __DIR__.'/../interfaces/'
        
        
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

$eventsManager = new EventsManager();
$eventsManager->attach(
    "micro:beforeExecuteRoute",
    function (Event $event, $app) {
        
         if(
                   $app->request->getServer('SCRIPT_URL')=='/users'
               and $app->request->isGet()
           ){
             
           }else{
//               echo $app->request->get('userId').'   '
//                  .$app->request->get('password').'   ';
              // die();
              try{
                user::checkUser(
                    $app->request->get('userId')
                  , $app->request->get('password')
                  );
                
               }catch( Exception $e ){
                   respFac::create('ext', $app->request )
                            ->sendError($e->getMessage());
                   
                 //  print('fff');
                   return false;
               } 
           }
        
        }
    
);

 
$app = new Micro($di);

include_once __DIR__."/../handlers/choice.php";

include_once __DIR__."/../handlers/user.php";

include_once __DIR__."/../handlers/exam.php";

include_once __DIR__."/../handlers/totalChoice.php";



//$app->error(
//    function ($ex) use ($app) {    
//    echo "-----";
//         respFac::create('ext', $app->request )
//         ->sendError($ex->getMessage());
//    }
//);


//$app->setEventsManager($eventsManager);    
//$app->handle();



try{    
$app->setEventsManager($eventsManager);    
$app->handle();
}catch(Exception $e){
    respFac::create('ext',$app->request)
    ->sendError($e->getMessage());
}

?>