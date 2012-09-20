<?php
session_start();

echo "Welcome on the mobile site of iHerbarium. <br> Get value : <br>";
print_r($_GET);

echo " <br> Post value : <br>";
print_r($_POST);

echo " <br> Session value : <br>";
print_r($_SESSION);


if(isset($_POST['lat'])&&isset($_POST['long']))
    {
        $_SESSION['lat'] = $_POST['lat'];
        $_SESSION['long'] = $_POST['long'];
    }
if(isset($_GET['lat'])&&isset($_GET['long']))
    {
        $_SESSION['lat'] = $_GET['lat'];
        $_SESSION['long'] = $_GET['long'];
    }

if(isset($_GET['username'])&&isset($_GET['md5passwd']))
    {
        $_SESSION['username'] = $_GET['username'];
        $_SESSION['md5passwd'] = $_GET['md5passwd'];
    }
    
$action ="home";
if(isset($_GET['action']))
    {
        if($_GET['action']=='mapnearplant')$action ="mapnearplant";
        if($_GET['action']=='show')$action ="mapnearplant";
    }
echo "<br>";
if($action =="home")
    {
        echo "<a href=mobile.php> affichage de la meme page pour tester les sessions</a>";
    }
?>
