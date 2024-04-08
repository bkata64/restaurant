<?php

if ($_SERVER['DEPLOYMENT_MODE'] === "DEV") {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

require './router.php';
require './slugifier.php';
require './dishes.php';
require './dishTypes.php';
require './auth.php';

$method = $_SERVER["REQUEST_METHOD"];
$parsed = parse_url($_SERVER['REQUEST_URI']);
$path = $parsed['path'];

// Útvonalak regisztrálása
$routes = [
    // [method, útvonal, handlerFunction],
    ['GET', '/', 'homeHandler'],
    ['GET', '/admin', 'adminHandler'],
    ['GET', '/admin/uj-etel-letrehozasa', 'dishCreateFormHandler'],
    ['GET', '/admin/etel-tipusok', 'dishTypeCreateFormHandler'],
    ['POST', '/admin/login', 'loginHandler'],
    ['GET', '/admin/etel-szerkesztese/{keresoBaratNev}', 'dishEditHandler'],
    ['POST', '/logout', 'logoutHandler'],
    ['POST', '/admin/create-dish', 'dishCreateHandler'],
    ['POST', '/admin/create-dish-type', 'dishTypeCreateHandler'],
    ['POST', '/admin/delete-dish/{id}', 'dishDeleteHandler'],
    ['POST', '/admin/update-dish/{dishId}', 'dishUpdateHandler'],
];

// Útvonalválasztó inicializálása
$dispatch = registerRoutes($routes);
$matchedRoute = $dispatch($method, $path);
$handlerFunction = $matchedRoute['handler'];
$handlerFunction($matchedRoute['vars']);

// Handler függvények deklarálása
function homeHandler()
{
    $pdo = getConnection();
    // $statement = $pdo->prepare('SELECT * FROM dishtypes');
    // $statement->execute();
    // $dishtypes = $statement->fetchAll(PDO::FETCH_ASSOC);
    $dishtypes = getAllDishTypes($pdo);
    //$dishtypeAndDishes = [];

    //foreach ($dishtypes as $dishtype) {   
    foreach ($dishtypes as $index => $dishtype) {   
        //$statement = $pdo->prepare("SELECT * FROM dishes WHERE dishTypeId = ?");        
        $statement = $pdo->prepare("SELECT * FROM dishes WHERE isActive = 1 AND dishTypeId = ?");        
        $statement->execute([$dishtype["id"]]);
        $dishes = $statement->fetchAll(PDO::FETCH_ASSOC); 
        // új sor: 
        $dishtypes[$index]['dishes'] = $dishes;      
        //$dishtype['dishes'] = [];
        //foreach ($dishes as $dish) {
            //array_push($dishtype['dishes'], $dish);            
        //}                     
        //array_push($dishtypeAndDishes, $dishtype);
    }   

    echo render("wrapper.phtml", [
        'content' => render('public-menu.phtml', [
            //'dishes' => $dishtypeAndDishes
            'dishes' => $dishtypes
        ])
    ]);
}

function adminHandler() {
    if(!isLoggedIn()) {
        echo render("wrapper.phtml", [
            'content' => render('login.phtml', [                
                'url' => $_SERVER['REQUEST_URI'],
                'info' => $_GET['info'] ?? ''
            ])
        ]);
        return;
    }
    $pdo = getConnection();
    //$statement = $pdo->prepare("SELECT * FROM dishes");        
    $statement = $pdo->prepare("SELECT * FROM dishes ORDER BY id DESC");        
    $statement->execute();
    $dishes = $statement->fetchAll(PDO::FETCH_ASSOC);  
    echo render("admin-wrapper.phtml", [
        'content' => render('dish-list.phtml', [
            'dishes' => $dishes,
            'info' => $_GET['info'] ?? ''
        ])
    ]);
}

function notFoundHandler()
{
    // új tartalom
    http_response_code(404);
    echo render("wrapper.phtml", [
        'content' => render('404.phtml')
    ]);
    //echo 'Oldal nem található';
}

function render($path, $params = [])
{
    ob_start();
    require __DIR__ . '/views/' . $path;
    return ob_get_clean();
}

function getConnection()
{
    $dsn="mysql:host=". $_SERVER['DB_HOST'] . ";dbname=" . $_SERVER['DB_NAME'];
    return new PDO($dsn, $_SERVER['DB_USER'], $_SERVER['DB_PASSWORD']);
}


// function getPathWithId($url) { 
//     $parsed = parse_url($url);
//     if(!isset($parsed['query'])) {
//         return $url;
//     }
//     $queryParams = []; 
//     parse_str($parsed['query'], $queryParams);
//     return $parsed['path'] . "?id=" . $queryParams['id'];
// }

