<?php
/**
 * Created by PhpStorm.
 * User: sify
 * Date: 15-6-26
 * Time: 下午3:48
 */
try{
    date_default_timezone_set("PRC");
    define('APP_PATH',realpath('..')."/");
    $config=new Phalcon\Config\Adapter\Ini(APP_PATH.'app/config/config.ini');

    //Register an class auto loader
    $loader = new Phalcon\Loader();
    $loader->registerDirs(
        array(
            APP_PATH.$config->application->controllersDir,
            APP_PATH.$config->application->pluginsDir,
            APP_PATH.$config->application->libraryDir,
            APP_PATH.$config->application->modelsDir
        )
    )->register();

    //Create a DI
    $di = new Phalcon\DI\FactoryDefault();

    //Setup the database service
    $di->set('db', function() use ($config) {
        return new Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host"     => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname"   => $config->database->dbname,
            'charset'  => 'UTF8'
        ));
    });

    //set routes
    $di->set('router', function ()
    {
        require APP_PATH.'app/config/routes.php';
        return $router;
    });

    //Setup the view component
    $di->set('view', function() use($config){
        $view = new Phalcon\Mvc\View();
        $view->setViewsDir( APP_PATH.$config->application->viewsDir );
        $view->registerEngines(array(
            ".volt" => 'volt',
            ".phtml" => 'Phalcon\Mvc\View\Engine\Php',
        ));
        return $view;
    });

    //Setup a base URI so that all generated URIs include the "tutorial" folder
    $di->set('url', function() use($config){
        $url = new Phalcon\Mvc\Url();
        $url->setBaseUri($config->resource->baseUri);
        return $url;
    });

    //Start the session the first time when some component request the session service
    $di->setShared('session', function() {
        $session = new Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    });

    //Setting up volt
    $di->set('volt', function($view, $di) use($config) {
        $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
        $volt->setOptions(array(
            "compiledPath" => APP_PATH.$config->cache->voltCacheDir
        ));
        return $volt;
    }, true);

    // set plugins
    $di->set("dispatcher",function() use($di)
    {
		$dispatcher    =new Phalcon\Mvc\Dispatcher();
		$eventsManager = $di->getShared("eventsManager");
		$security      = new Security($di);
		$eventsManager->attach('dispatch', $security);
		$dispatcher->setEventsManager($eventsManager);
		return $dispatcher;
    });

    /*//flash info in the page
    $di->set('flash',function ()
    {
        return new Phalcon\Flash\Session(array(
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info'
        ));
    });*/

    //cookie crypt
    $di->set('crypt', function()
    {
        $crypt = new Phalcon\Crypt();
        $crypt->setKey('sify21'); //Use your own key!
        return $crypt;
    });

    //cookie
    $di->set("cookies",function()
    {
        $cookies=new Phalcon\Http\Response\Cookies();
        return $cookies;
    });

    /*//generate html tags through calling functions
    $di->set('tag',function(){
        return new Phalcon\Tag();
    });*/

    //Handle the request
    $app = new \Phalcon\Mvc\Application($di);
    echo $app->handle()->getContent();

}catch (\Phalcon\Exception $e){
    echo "Phalcon 异常：", $e->getMessage();
}catch (Exception $e){
    echo "Php 异常：", $e->getMessage();
}