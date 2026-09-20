<?php

/**
 * Copy this file to settings.php on the server, then replace every value
 * marked REPLACE_ME. settings.php is private and must never be committed.
 */
define('HASH', 'REPLACE_ME_WITH_A_LONG_RANDOM_ADMIN_SECRET');
define('DEV', 'support@example.com');
define('TABLA', 'hoteles_promociones');
define('HOTELID', 74);
define('RECAPTCHA_SECRET', 'REPLACE_ME_WITH_THE_RECAPTCHA_SECRET');

function getConnection()
{
    $dbhost = '127.0.0.1';
    $dbuser = 'REPLACE_ME_DB_USER';
    $dbpass = 'REPLACE_ME_DB_PASSWORD';
    $dbname = 'REPLACE_ME_DB_NAME';

    return new PDO(
        "mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}

$isAdmin = !empty($_GET['hash']) && hash_equals(HASH, (string) $_GET['hash']);
