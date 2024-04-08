<?php
function loginHandler() {
    $pdo = getConnection();
    $statement = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $statement->execute([$_POST["email"]]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        //header('Location: ' . getPathWithId($_SERVER['HTTP_REFERER']) . '?info=invalidCredentials');
        // javított sor:  
        header('Location: /etterem/admin?info=invalidCredentials');  
        return;
    }
    $isVerified = password_verify($_POST['password'], $user["password"]);    
    if(!$isVerified) {
        //header('Location: ' . getPathWithId($_SERVER['HTTP_REFERER']) . '?info=invalidCredentials');
        // javított sor:  
        header('Location: /etterem/admin?info=invalidCredentials'); 
        return;
    }
    session_start();  
    $_SESSION['userId'] = $user['id']; 
    //header('Location: ' . getPathWithId($_SERVER['HTTP_REFERER']));
    header('Location: /etterem/admin');
}

function logoutHandler() {     
    session_start();
    $params = session_get_cookie_params(); 
    setcookie(session_name(),  '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
    session_destroy(); 
    //header('Location: ' . getPathWithId($_SERVER['HTTP_REFERER']));
    header('Location: /etterem/');
}

// új függvény
function redirectToLoginPageIfNotLoggedIn()
{
    if(isLoggedIn()) {
        return;
    }
    header('Location: /etterem/admin');
    exit;
}

function isLoggedIn(): bool
{
    if (!isset($_COOKIE[session_name()])) {
        return false;
    }
    session_start();
    if (!isset($_SESSION['userId'])) {
        return false;
    }
    return true;
}
?>