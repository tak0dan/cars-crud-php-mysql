# Cars CRUD (PHP + MySQL)

Small web app to add, search, edit and delete cars. Menu on the first page, no sessions and no login on purpose.
School practice for the DWES module, written with plain PHP (PDO), MySQL / MariaDB, HTML, CSS and a little JS.

- Live demo (runs in the browser): https://tak0dan.github.io/cars-crud-php-mysql/demo/
- Project page: https://tak0dan.github.io/cars-crud-php-mysql/
- PDF with the exercise summary and screenshots: [docs/documentacion.pdf](docs/documentacion.pdf)

## What it does

| # | Page | What happens |
|---|------|--------------|
| 1 | `alta.php` | Form with brand, model and picture (file or url). The `id` is auto incremented. |
| 2 | `buscar.php` | Search by brand or model. |
| 3 | `editar.php` | Shows the picture of the car, lets you change the data or delete the car. |
| 4 | `admin_crearbasedatosytablas.php` | Creates the `coches` database, the `coches` table and two example cars with pictures from the internet. |

`index.php` is the menu. `config.php` holds the connection data and the helpers every page uses.

Table:

```sql
CREATE TABLE coches (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    marca  VARCHAR(50) NOT NULL,
    modelo VARCHAR(80) NOT NULL,
    foto   VARCHAR(255)
);
```

## Way 1: with XAMPP

1. Install XAMPP (Windows: the normal installer. Linux: `manager-linux-x64.run`, installs into `/opt/lampp`).
2. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. Copy this folder into the htdocs folder and call it `coches`:
   - Windows: `C:\xampp\htdocs\coches`
   - Linux: `/opt/lampp/htdocs/coches` (the `fotos` folder must be writable by Apache: `chmod 777 fotos`)
4. Open http://localhost/coches/admin_crearbasedatosytablas.php once. It creates everything.
5. Open http://localhost/coches/ and use the menu.

XAMPP defaults are already what `config.php` expects (user `root`, empty password, `localhost:3306`).
phpMyAdmin is at http://localhost/phpmyadmin if you want to look at the table.

## Way 2: without XAMPP

You need PHP 8 with the `pdo_mysql` extension and a MySQL or MariaDB server.

```bash
# 1. the database server (any MySQL / MariaDB is fine, this one is a throw-away copy on port 3307)
mariadb-install-db --no-defaults --datadir=/tmp/carsdb
mariadbd --no-defaults --datadir=/tmp/carsdb --port=3307 --bind-address=127.0.0.1 --socket=/tmp/carsdb.sock &

# 2. the web server, from the project folder
DB_HOST=127.0.0.1 DB_PORT=3307 php -d extension=pdo_mysql -S localhost:8090

# 3. open http://localhost:8090/admin_crearbasedatosytablas.php once, then http://localhost:8090/
```

The connection can be changed with the env variables `DB_HOST`, `DB_PORT`, `DB_USER` and `DB_PASS`
(or just edit the defaults at the top of `config.php`).

## Way 3: the web demo

The same PHP files run inside the browser (PHP compiled to WebAssembly, SQLite instead of MySQL) from the
`docs/demo` folder. No server needed, so it also works on GitHub Pages.
After changing a PHP file run `bash tools/build-demo.sh` to copy it into the demo.

## Looks

Very primitive on purpose. `style.css` only keeps the colours of the SunshineOS site (dark background, warm text,
gold links and buttons). Add `?plain=1` to any page to see the version without any css (a cookie remembers it),
`?plain=0` to come back.
