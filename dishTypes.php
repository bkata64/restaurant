<?php

function dishTypeCreateFormHandler() {
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    // $statement = $pdo->prepare('SELECT * FROM dishtypes');
    // $statement->execute();
    // $dishtypes = $statement->fetchAll(PDO::FETCH_ASSOC);
    $dishtypes = getAllDishTypes($pdo);
    echo render("admin-wrapper.phtml", [
        'content' => render('dish-type-list.phtml', [
            'dishtypes' => $dishtypes
        ])
    ]);
}

function dishTypeCreateHandler() {
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $statement = $pdo->prepare("INSERT INTO `dishtypes` (`name`, `slug`, `description`)  VALUES ( ?, ?, ?)");
    $statement->execute([
        filter_var($_POST["name"], FILTER_SANITIZE_STRING),
        slugify($_POST["name"]),
        filter_var($_POST["description"], FILTER_SANITIZE_STRING)        
    ]);
    header('Location: /admin/etel-tipusok'); 
}

function getAllDishTypes($pdo)
{    
    $stmt = $pdo->prepare("SELECT * FROM `dishTypes`");
    $stmt->execute();
    $dishTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dishTypes;
}

?>