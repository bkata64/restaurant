<?php 

function dishCreateFormHandler() {
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    // $statement = $pdo->prepare('SELECT * FROM dishtypes');
    // $statement->execute();
    // $dishtypes = $statement->fetchAll(PDO::FETCH_ASSOC);
    $dishtypes = getAllDishTypes($pdo);
    echo render("admin-wrapper.phtml", [
        'content' => render('create-dish.phtml', [
            'dishtypes' => $dishtypes
        ])
    ]);
}

function dishCreateHandler() { 
    redirectToLoginPageIfNotLoggedIn();   
    $pdo = getConnection();
    // $statement = $pdo->prepare("INSERT INTO `dishes` (`name`, `slug`, `description`, `price`, `isActive`, `dishTypeId`)  VALUES ( ?, ?, ?, ?, ?, ?)");
    // $statement->execute([
    //     filter_var($_POST["name"], FILTER_SANITIZE_STRING),
    //     slugify($_POST["name"]),
    //     filter_var($_POST["description"], FILTER_SANITIZE_STRING),
    //     (int)$_POST["price"],
    //     (bool)$_POST["isActive"],
    //     (int)$_POST["dishTypeId"]
    // ]);
    // így is lehet:
    $stmt = $pdo->prepare(
        "INSERT INTO `dishes` 
        (`name`, `slug`, `description`, `price`, `isActive`, `dishTypeId`) 
        VALUES 
        (:nev, :slug, :leiras, :ar, :aktiv, :dishTypeId);" 
    );

    $stmt->execute([
        "nev" => $_POST['name'],
        "slug" => slugify($_POST['name']),
        "leiras" => $_POST['description'],
        "ar" =>  $_POST['price'],
        "aktiv" =>  (int)isset($_POST['isActive']),
        "dishTypeId" =>  $_POST['dishTypeId'],
    ]);

    header('Location: /admin?info=createSuccessful'); 
}
function dishUpdateHandler($vars) {  
    /* UPDATE dishes SET `name` = ?, `slug` = ?, `description` = ?, `price` = ?, `isActive` = ?, `dishTypeId` = ? WHERE `id` = ?;  */
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $statement = $pdo->prepare("UPDATE dishes SET `name` = ?, `slug` = ?, `description` = ?, `price` = ?, `isActive` = ?, `dishTypeId` = ? WHERE `id` = ?;");
    $statement->execute([
        filter_var($_POST["name"], FILTER_SANITIZE_STRING),
        slugify($_POST["name"]),
        filter_var($_POST["description"], FILTER_SANITIZE_STRING),
        (int)$_POST["price"],
        (bool)$_POST["isActive"],
        (int)$_POST["dishTypeId"],
        (int)$vars['dishId']
    ]);
    header('Location: /admin?info=updateSuccessful'); 
}

function dishDeleteHandler($vars) { 
    // DELETE FROM dishes WHERE id = ?;
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $statement = $pdo->prepare("DELETE FROM dishes WHERE id = ?");
    $statement->execute([$vars['id']]);
    header('Location: /admin?info=deleteSuccessful');
}



function dishEditHandler($vars)
{    
    // echo "<pre>";
    // var_dump($vars);
    // echo 'Étel szerkesztése: ' . $vars['keresoBaratNev'];
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $statement = $pdo->prepare("SELECT * FROM dishes WHERE slug = ?");
    $statement->execute([$vars['keresoBaratNev']]);
    $dish = $statement->fetch(PDO::FETCH_ASSOC);

    // echo "<pre>";
    // var_dump($dish);
    // exit;

    // $statement = $pdo->prepare('SELECT * FROM dishtypes');
    // $statement->execute();
    // $dishtypes = $statement->fetchAll(PDO::FETCH_ASSOC);
    $dishtypes = getAllDishTypes($pdo);
    echo render("admin-wrapper.phtml", [
        'content' => render('edit-dish.phtml', [
            'dishtypes' => $dishtypes,
            'dish' => $dish
        ])
    ]);
}

?>