<?php

require 'config.php';


$q = '';
if (isset($_GET['q']))
{  
    $q = trim($_GET['q']);
    $q = (string) $q;
}  

page_top('Search cars');

?>
<form method="get">

  <label for="q">Brand or model</label>
  <input type="text" id="q" name="q" value="<?php echo esc($q); ?>" placeholder="toyota, mustang..." autofocus>

  <button type="submit" class="btn">Search</button>

</form>
<?php


// only search when the form was sent. empty text = shows every car (useful for testing)
if (isset($_GET['q']))
{
    $pdo = db_connect();

    // 1st version, it only found the exact word ("ford" yes, "for" no)
    // $sql = "SELECT * FROM coches WHERE marca = '$q' OR modelo = '$q'";

    // 2nd version with LIKE, found more but it is open to sql injection (tried  ' OR 1=1 -- )
    // $sql = "SELECT * FROM coches WHERE marca LIKE '%$q%' OR modelo LIKE '%$q%'";   

    $stmt = $pdo->prepare('SELECT id, marca, modelo FROM coches WHERE marca LIKE ? OR modelo LIKE ? ORDER BY marca, modelo');
    $like1 = '%' . $q . '%';
    $like2 = '%' . $q . '%';
    $stmt->execute(array($like1, $like2));
    $cars = $stmt->fetchAll();
    $total_cars = count($cars);

    // print_r($cars);


    if ($total_cars == 0)
    {  
        echo '<p class="msg">No cars found.</p>';
    }    
    else
    {
        echo '<table>';
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

        echo '</table>';  
    }
}    

page_bottom();
