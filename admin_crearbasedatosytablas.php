<?php

require 'config.php';    

page_top('Admin: create database and table');

$steps = array();

$failed = false;
$ok = true;


try
{
    // step 1: the database. (sqlite has no such thing, the file is the database)
    // NOTE: this has to go BEFORE the table, the first time i did it the other way round and got "Unknown database"
    if (DB_DRIVER == 'mysql')
    {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
        $server = new PDO($dsn, DB_USER, DB_PASS); 
        $server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $server->exec('CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');    
        $steps[] = 'Database "' . DB_NAME . '" is ready.';
    }


    // step 2: now connect to it and make the table
    $pdo = db_connect();

    if (DB_DRIVER == 'sqlite')
    {    
        $sql = 'CREATE TABLE IF NOT EXISTS coches (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            marca VARCHAR(50) NOT NULL,
            modelo VARCHAR(80) NOT NULL,
            foto VARCHAR(255)
        )';
    }  
    else
    {   
        $sql = 'CREATE TABLE IF NOT EXISTS coches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            marca VARCHAR(50) NOT NULL,
            modelo VARCHAR(80) NOT NULL,
            foto VARCHAR(255)
        ) ENGINE=InnoDB';  
    }   
    $pdo->exec($sql);
    $steps[] = 'Table "coches" (id, marca, modelo, foto) is ready.';


    // step 3: the two example cars.
    // only when the table is empty, if not every visit to this page added 2 more (that happened)
    $total = $pdo->query('SELECT COUNT(*) AS n FROM coches')->fetch(); 

    if ($total['n'] == 0)
    {  
        $stmt = $pdo->prepare('INSERT INTO coches (marca, modelo, foto) VALUES (?, ?, ?)');   

        $stmt->execute(array(
            'Toyota',
            'Corolla Cross',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Toyota_Corolla_Cross_Hybrid_1X7A1861.jpg/960px-Toyota_Corolla_Cross_Hybrid_1X7A1861.jpg'
        ));

        // first url of the mustang gave a 400 error, this one is ok
        $stmt->execute(array(
            'Ford',
            'Mustang',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/2018_Ford_Mustang_GT_5.0_Front.jpg/960px-2018_Ford_Mustang_GT_5.0_Front.jpg'
        ));

        $steps[] = 'Two example cars inserted (Toyota Corolla Cross, Ford Mustang).';
    }   
    else
    {
        $steps[] = 'The table already has cars, examples skipped.';   
    }    
}
catch (PDOException $e)
{
    $failed = true;
    $ok = false;
    $steps[] = 'Error: ' . $e->getMessage();  
}


foreach ($steps as $step)
{
    if ($failed)
    {
        echo '<p class="msg err">' . esc($step) . '</p>';   
    }    
    else
    {
        echo '<p class="msg ok">' . esc($step) . '</p>';
    }  
}  

if (!$failed)
{
    echo '<p><a class="btn" href="buscar.php?q=">See the cars</a></p>'; 
} 

page_bottom();   
