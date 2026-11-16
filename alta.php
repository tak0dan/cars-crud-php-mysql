<?php

require 'config.php';

$pdo = db_connect();  
$message = '';

//print_r($_POST);    // checking what arrives from the form
//print_r($_FILES);    


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{  
    $marca  = trim($_POST['marca']);

    $modelo = trim($_POST['modelo']);

    $marca  = trim($marca);      // just in case
    $modelo = trim($modelo);

    // echo "marca: $marca  modelo: $modelo";    // test, ok

    if ($marca == '' || $modelo == '')
    {
        $message = '<p class="msg err">Brand and model are both needed.</p>';
    }
    else
    {    
        $foto = '';
        $foto = save_photo('');
        // $foto = $_POST['foto'];    // 1st try: only the text. but it has to be a picture so..


        // 2nd try
        // $sql = "INSERT INTO coches VALUES (NULL, '$marca', '$modelo', '$foto')";   
        // $pdo->query($sql);
        // works, but a name like O'Neil breaks the query (tested). not good


        // 3rd try with prepare, this one works
        $stmt = $pdo->prepare('INSERT INTO coches (marca, modelo, foto) VALUES (?, ?, ?)');
        $stmt->execute(array($marca, $modelo, $foto));

        $new_id = $pdo->lastInsertId();
        $new_id = (int) $new_id;
        $message = '<p class="msg ok">Car saved with id ' . esc($new_id) . '.</p>';
    }   
}


page_top('Add a car');

echo $message;
?>

<form method="post" enctype="multipart/form-data">

  <label for="marca">Brand</label>
  <input type="text" id="marca" name="marca" required autofocus>

  <label for="modelo">Model</label>
  <input type="text" id="modelo" name="modelo" required>

  <label for="foto">Picture (file)</label>
  <input type="file" id="foto" name="foto" accept="image/*">

   <label for="foto_url">or picture url</label>
   <input type="text" id="foto_url" name="foto_url" placeholder="https://...">

  <br>
  <button type="submit" class="btn">Save car</button>

</form>

<?php

page_bottom();   
