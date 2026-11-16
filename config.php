<?php



// config.php  --  connection + the stuff every page needs
// every page does require 'config.php' at the top



// ------- OLD VERSION (mysqli) -------
// $conn = mysqli_connect("localhost", "root", "", "coches");
// if (!$conn)
// {    
//     die("Connection failed: " . mysqli_connect_error());    
// } 
// worked on xampp but the sqlite demo cant use it, so i moved everything to PDO
// (PDO does mysql and sqlite with the same code)



// reads a setting from the environment, if it is not there use the default.
// with xampp nothing is set so the defaults are used
function env_or($name, $default)
{
    $value = getenv($name);
    if ($value !== false)
    {  
        return $value;
    }

    // apache SetEnv and the web demo put it in $_SERVER, getenv doesnt see it there
    if (isset($_SERVER[$name]))
    {  
        return $_SERVER[$name];
    }

    return $default;
}   


//define('DB_HOST', 'localhost');     // first version, hardcoded
//define('DB_USER', 'root'); 

define('DB_DRIVER', env_or('DB_DRIVER', 'mysql'));       // mysql or sqlite (sqlite is only for the web demo)
define('DB_HOST',   env_or('DB_HOST', 'localhost')); 
define('DB_PORT',   env_or('DB_PORT', '3306')); 
define('DB_USER',   env_or('DB_USER', 'root'));          // xampp default user
define('DB_PASS',   env_or('DB_PASS', ''));              // xampp default has no password
define('DB_NAME',   'coches');    
define('DB_SQLITE', env_or('DB_SQLITE', __DIR__ . '/coches.sqlite'));




// the old look (no css at all) is still here to compare.  ?plain=1 turns it on, ?plain=0 turns it off
if (isset($_GET['plain']))
{ 
    setcookie('plain', $_GET['plain'], 0, '/'); 
    $_COOKIE['plain'] = $_GET['plain'];
}

$plain_mode = false;
if (isset($_COOKIE['plain']) && $_COOKIE['plain'] == '1')
{
    $plain_mode = true;   
}    




function esc($text)
{  
    // htmlspecialchars($text);   // forgot the quotes flag, a name with " broke the input value
    $text = (string) $text;
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}   



function db_connect()
{
    try
    {
        if (DB_DRIVER == 'sqlite')
        {
            $pdo = new PDO('sqlite:' . DB_SQLITE);
        }
        else
        { 
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS);

        }
    }
    catch (PDOException $e)
    {    
        // this happened when i opened buscar.php before running the admin page
        page_top('Database problem'); 
        echo '<p class="msg err">Could not connect: ' . esc($e->getMessage()) . '</p>';
        echo '<p>Did you already run <a href="admin_crearbasedatosytablas.php">the admin page</a>?</p>';
        page_bottom();
        exit;    
    } 

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);   // gave every value twice in the loop

    return $pdo;  
}




// gives back what goes into the "foto" column: the uploaded file or the url.
// $current = the old value, kept if nothing new was sent
function save_photo($current)
{
    //print_r($_FILES);     // to see what arrives
    //die('stop');

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK)
    {    
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)); 

        // TODO check that it is really an image, not only the extension
        if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp')))
        { 
            $target = 'fotos/car_' . uniqid() . '.' . $ext;  

            // move_uploaded_file($_FILES['foto']['tmp_name'], $target);   // relative path, apache couldnt find it
            if (move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/' . $target))
            {  
                return $target;  
            }
        }   
    }

    if (isset($_POST['foto_url']))
    {  
        $url = trim($_POST['foto_url']);    

        if (preg_match('#^https?://#i', $url))
        {
            return $url;
        }    
    }

    return $current;
}    




// ------- page layout -------

function page_top($title)
{
    global $plain_mode;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc($title); ?> - Cars</title>
<?php if ($plain_mode) { ?>
<style>
 body{font-family:sans-serif;max-width:760px;margin:30px auto;padding:0 15px} 
 label{display:block;margin-top:10px} 
 input[type=text]{width:100%;padding:6px;box-sizing:border-box}
 button,.btn{margin-top:15px;padding:8px 14px;cursor:pointer}
 table{border-collapse:collapse;width:100%;margin-top:15px}   
 td,th{border:1px solid #aaa;padding:6px;text-align:left}   
 img{max-width:200px;max-height:130px}
 .ok{color:green} .err{color:#b00}
</style>
</head>
<body>
<p><a href="index.php">&larr; Menu</a></p>
<h1><?php echo esc($title); ?></h1>
<?php } else { ?>
<link rel="stylesheet" href="style.css">
</head>
<body>
<p>
  <a href="index.php">Menu</a> |
  <a href="alta.php">Add</a> |
  <a href="buscar.php">Search</a> |
  <a href="admin_crearbasedatosytablas.php">Admin</a>
</p>
<h1><?php echo esc($title); ?></h1>
<?php }
}


function page_bottom()
{
    echo "</body>\n</html>\n"; 
}
