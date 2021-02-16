<?php
require "vendor/autoload.php";
require "config/config.php";

try {
    $url = isset($_GET["url"]) ? $_GET["url"] : 'index/index';
    $url = explode("/", $url);
    unset($_GET["url"]);

    $controllerName = $url[0] ? $url[0] . 'Controller' :  'IndexController';
    unset($url[0]);
    $actionName = (isset($url[1]) && $url[1]) ? $url[1] . 'Action' :  'indexAction';
    unset($url[1]);

    $params = $url;
    $class = 'Controllers\\' . $controllerName;
    if (!class_exists($class)) {
        throw new Exception("Controller Doesn't found!");
    }
    if (!method_exists($class, $actionName)) {
        throw new Exception("Action Doesn't found!");
    }

    $controller = new $class();
    call_user_func_array([$controller, $actionName], $params);
} catch (Exception $e) {
    echo $e->getMessage();
}
