<?php
   
    $APP_PATH['root'] = $_SERVER['DOCUMENT_ROOT'] . '/IncubatecNF/';
    $APP_PATH['sys'] = $APP_PATH['root'] . 'sys/';
    $APP_PATH['view'] = $APP_PATH['root'] . 'views/';
    $APP_PATH['remessa'] = $APP_PATH['root'] . '_remessa/';

    require_once('autoloader.php');
    
    if(isset($_POST['action']) && !empty($_POST['action'])  ){

        $ctrl_name = ucfirst($_POST['class']);
        if(class_exists($ctrl_name)){
            $_controller = new $ctrl_name();
            $_response = $_controller->init();

            if (isset($_response)) {
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Expires: Wed, 05 Jun 1985 05:00:00 GMT');
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($_response);
                exit;
            }
        }
    }else{
        header('Content-type: text/html; charset=utf-8');
        require_once('base.html');
        exit;
    }


    