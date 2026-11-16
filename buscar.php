<?php

require 'config.php';   

$q = '';   
if (isset($_GET['q']))
{   
    $q = trim($_GET['q']);   
}   

page_top('Search cars');   

?>
<form method="get" class="form">

  <label for="q">Brand or model</label>
  <input type="text" id="q" name="q" value="<?php echo esc($q); ?>" placeholder="toyota, mustang..." autofocus>

  <button type="submit" class="btn primary">Search</button>

</form>
<?php


// only search once the form was sent, an empty text lists everything
if (isset($_GET['q']))
{   
    $pdo = db_connect();   

    // old version, it was open to sql injection:
    // $sql = "SELECT * FROM coches WHERE marca LIKE '%$q%' OR modelo LIKE '%$q%'";   

    $stmt = $pdo->prepare('SELECT id, marca, modelo FROM coches WHERE marca LIKE ? OR modelo LIKE ? ORDER BY marca, modelo');   
    $stmt->execute(array('%' . $q . '%', '%' . $q . '%'));   
    $cars = $stmt->fetchAll();   

    if (count($cars) == 0)
    {   
        echo '<p class="msg">No cars found.</p>';   
    }   
    else
    {   
        echo '<div class="tablewrap"><table>';   
        echo '<tr><th>Id</th><th>Brand</th><th>Model</th><th></th></tr>';   

        foreach ($cars as $car)
        {   
            echo '<tr>';   
            echo '<td>' . esc($car['id']) . '</td>';   
            echo '<td>' . esc($car['marca']) . '</td>';   
            echo '<td>' . esc($car['modelo']) . '</td>';   
            echo '<td><a href="editar.php?id=' . esc($car['id']) . '">Edit</a></td>';   
            echo '</tr>';   
        }   

        echo '</table></div>';   
    }   
}   

page_bottom();   
