<?php

require 'config.php';   

$pdo = db_connect();   

$id = 0;   
if (isset($_REQUEST['id']))
{   
    $id = (int) $_REQUEST['id'];   
}   

$message = '';   


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{   
    // the delete button sends "eliminar", the save button does not
    if (isset($_POST['eliminar']))
    {   
        $stmt = $pdo->prepare('SELECT foto FROM coches WHERE id = ?');   
        $stmt->execute(array($id));   
        $old = $stmt->fetch();   

        // remove the uploaded picture too, but never touch urls
        if ($old && strpos($old['foto'], 'fotos/') === 0)
        {   
            @unlink(__DIR__ . '/' . $old['foto']);   
        }   

        $stmt = $pdo->prepare('DELETE FROM coches WHERE id = ?');   
        $stmt->execute(array($id));   

        page_top('Car deleted');   
        echo '<p class="msg ok">The car is gone from the database.</p>';   
        echo '<p><a class="btn" href="buscar.php">Back to the search</a></p>';   
        page_bottom();   
        exit;   
    }   


    $marca  = trim($_POST['marca']);   
    $modelo = trim($_POST['modelo']);   

    if ($marca == '' || $modelo == '')
    {   
        $message = '<p class="msg err">Brand and model are both needed.</p>';   
    }   
    else
    {   
        $stmt = $pdo->prepare('SELECT foto FROM coches WHERE id = ?');   
        $stmt->execute(array($id));   
        $old = $stmt->fetch();   

        $foto = '';   
        if ($old)
        {   
            $foto = $old['foto'];   
        }   
        $foto = save_photo($foto);   

        $stmt = $pdo->prepare('UPDATE coches SET marca = ?, modelo = ?, foto = ? WHERE id = ?');   
        $stmt->execute(array($marca, $modelo, $foto, $id));   

        $message = '<p class="msg ok">Car updated.</p>';   
    }   
}   


$stmt = $pdo->prepare('SELECT * FROM coches WHERE id = ?');   
$stmt->execute(array($id));   
$car = $stmt->fetch();   

page_top('Edit car');   

if (!$car)
{   
    echo '<p class="msg err">There is no car with that id.</p>';   
    page_bottom();   
    exit;   
}   

echo $message;   

$url_value = '';   
if (preg_match('#^https?://#', (string) $car['foto']))
{   
    $url_value = $car['foto'];   
}   
?>

<?php if ($car['foto'] != '') { ?>
<p><img class="carpic" src="<?php echo esc($car['foto']); ?>" alt="car picture"></p>
<?php } ?>

<form method="post" enctype="multipart/form-data" class="form">

  <input type="hidden" name="id" value="<?php echo esc($car['id']); ?>">

  <label for="marca">Brand</label>
  <input type="text" id="marca" name="marca" value="<?php echo esc($car['marca']); ?>" required autofocus>

  <label for="modelo">Model</label>
  <input type="text" id="modelo" name="modelo" value="<?php echo esc($car['modelo']); ?>" required>

  <label for="foto">Change picture (file)</label>
  <input type="file" id="foto" name="foto" accept="image/*">

  <label for="foto_url">or picture url</label>
  <input type="text" id="foto_url" name="foto_url" value="<?php echo esc($url_value); ?>">

  <div class="actions">
    <button type="submit" class="btn primary">Save changes</button>
    <button type="submit" name="eliminar" value="1" class="btn secondary" onclick="return confirm('Delete this car?')">Delete car</button>
  </div>

</form>

<?php

page_bottom();   
