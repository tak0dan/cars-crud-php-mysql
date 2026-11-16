<?php

require 'config.php';


$q = '';
if (isset($_GET['q']))
{  
    $q = trim($_GET['q']);
    $q = (string) $q;
}  

// puts a ~ under every letter of $text that matches the search text $q.
// gives back the text, the line with the ~ and how many matches it found
function underline($text, $q)
{
    $marks = array();
    $count = 0;
    $len  = strlen($text);
    $qlen = strlen($q);

    if ($qlen == 0)
    {
        return array($text, '', 0);
    }

    // 1st try: only worked when the text started with q ("to" yes, "ota" no)
    // if (substr($text, 0, $qlen) == $q) { ... }

    // go letter by letter, if q starts here (no matter upper or lower case) remember the positions
    for ($i = 0; $i < $len; $i++)
    {
        $piece = substr($text, $i, $qlen);

        if (strtolower($piece) == strtolower($q))
        {
            $count = $count + 1;

            for ($j = 0; $j < $qlen; $j++)
            {
                $marks[$i + $j] = '~';
            }
        }
    }

    // now build the line, a space where there is no ~
    $line = '';
    for ($i = 0; $i < $len; $i++)
    {
        if (isset($marks[$i]))
        {
            $line = $line . '~';
        }
        else
        {
            $line = $line . ' ';
        }
    }

    return array($text, $line, $count);
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
        // how many ~ matches in total, just to show it (count)
        $total_matches = 0;
        $total_matches = 0;

        echo '<p class="msg">' . $total_cars . ' car(s) found.</p>';
        echo '<table>';        echo '<tr><th>Id</th><th>Brand</th><th>Model</th><th></th></tr>';

        foreach ($cars as $car)
        {  
            echo '<tr>';
            echo '<td>' . esc($car['id']) . '</td>';
            $brand_info = underline($car['marca'], $q);
            $model_info = underline($car['modelo'], $q);
            $total_matches = $total_matches + $brand_info[2] + $model_info[2];

            // echo '<td>' . esc($car['marca']) . '</td>';     // old version, without the ~
            echo '<td><pre>' . esc($brand_info[0]) . "\n" . $brand_info[1] . '</pre></td>';            echo '<td><pre>' . esc($model_info[0]) . "\n" . $model_info[1] . '</pre></td>';            echo '<td><a href="editar.php?id=' . esc($car['id']) . '">Edit</a></td>';
            echo '</tr>';

        }

        echo '</table>';

        if ($q != '')
        {
            echo '<p>Matches marked with ~: ' . $total_matches . '</p>';
        }    }
}    

page_bottom();
