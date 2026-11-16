<?php

require 'config.php';   

$pdo = db_connect();   
$message = '';   


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{   
    $marca  = trim($_POST['marca']);   
    $modelo = trim($_POST['modelo']);   

    if ($marca == '' || $modelo == '')
    {   
        $message = '<p class="msg err">Brand and model are both needed.</p>';   
    }   
    else
    {   
        $foto = save_photo('');   

        // old version, before i moved to prepared statements:
        // $sql = "INSERT INTO coches (marca, modelo, foto) VALUES ('$marca', '$modelo', '$foto')";   
        // $pdo->query($sql);   

        $stmt = $pdo->prepare('INSERT INTO coches (marca, modelo, foto) VALUES (?, ?, ?)');   
        $stmt->execute(array($marca, $modelo, $foto));   

        $new_id = $pdo->lastInsertId();   
        $message = '<p class="msg ok">Car saved with id ' . esc($new_id) . '.</p>';   
    }   
}   


page_top('Add a car');   

echo $message;   
?>

<form method="post" enctype="multipart/form-data" class="form">

  <label for="marca">Brand</label>
  <input type="text" id="marca" name="marca" required autofocus>

  <label for="modelo">Model</label>
  <input type="text" id="modelo" name="modelo" required>

  <label for="foto">Picture (file)</label>
  <input type="file" id="foto" name="foto" accept="image/*">

   <label for="foto_url">or picture url</label>
   <input type="text" id="foto_url" name="foto_url" placeholder="https://...">

  <button type="submit" class="btn primary">Save car</button>

</form>

<?php

page_bottom();   
