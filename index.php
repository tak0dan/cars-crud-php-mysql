<?php

require 'config.php';

page_top('Cars manager');

?>
<p class="sub">Small CRUD for a cars table. No login, no sessions, pick an option.</p>

<!-- first menu was just 3 links in a list, kept in case the teacher wants it simple
<ul>
  <li><a href="alta.php">Add a car</a></li>
  <li><a href="buscar.php">Search</a></li>
  <li><a href="admin_crearbasedatosytablas.php">Admin</a></li>
</ul>
-->


<div class="blk">
  <h3>1. Add a car</h3>
  <p>Brand, model and a picture (upload it or paste a url). The id is auto incremented.</p>
  <a href="alta.php">Open the form</a>
</div>


<div class="blk">
  <h3>2. Search and edit</h3>
  <p>Look cars up by brand or model, then edit or delete the ones in the result.</p>
  <a href="buscar.php">Open the search</a>
</div>


<div class="blk">
  <h3>3. Admin setup</h3>
  <p>Creates the database, the table and two example cars with pictures from the internet.</p>
  <a href="admin_crearbasedatosytablas.php">Run it</a>
</div>

<?php

page_bottom();   

