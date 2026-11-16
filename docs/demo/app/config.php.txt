<?php



// ------------------------------------------------------------
// config + the small helpers every page uses
// ------------------------------------------------------------


// reads an env variable, falls back to a default if it is not set.
// with XAMPP nothing is set, so the defaults below are used
function env_or($name, $default)
{   
    $value = getenv($name);   
    if ($value !== false)
    {   
        return $value;   
    }   

    // apache's SetEnv (and the web demo) puts the values in $_SERVER instead
    if (isset($_SERVER[$name]))
    {   
        return $_SERVER[$name];   
    }   

    return $default;   
}   

define('DB_DRIVER', env_or('DB_DRIVER', 'mysql'));       // mysql or sqlite (sqlite is only for the web demo)
define('DB_HOST',   env_or('DB_HOST', 'localhost'));   
define('DB_PORT',   env_or('DB_PORT', '3306'));   
define('DB_USER',   env_or('DB_USER', 'root'));          // xampp default user
define('DB_PASS',   env_or('DB_PASS', ''));              // xampp default has no password
define('DB_NAME',   'coches');   
define('DB_SQLITE', env_or('DB_SQLITE', __DIR__ . '/coches.sqlite'));   



// plain mode = the old unstyled look, handy to compare. ?plain=1 turns it on, ?plain=0 off
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
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');   
}   


// opens the connection to the "coches" database
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
        page_top('Database problem');   
        echo '<p class="msg err">Could not connect: ' . esc($e->getMessage()) . '</p>';   
        echo '<p>Did you already run <a href="admin_crearbasedatosytablas.php">the admin page</a>?</p>';   
        page_bottom();   
        exit;   
    }   

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);   

    return $pdo;   
}   



// takes the uploaded picture (or the url from the form) and returns what goes in the "foto" column.
// $current is the old value, we keep it if nothing new was sent
function save_photo($current)
{   
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK)
    {   
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));   

        if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp')))
        {   
            $target = 'fotos/car_' . uniqid() . '.' . $ext;   

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



// ---- page layout ----

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
