<?php

/**
 * Copy to settings.php only on the hosting environment. Never commit settings.php.
 * Keep this file out of source control after copying it to settings.php.
 */
define('DEV', getenv('HOTEL_SUPPORT_EMAIL') ?: 'support@example.com');
define('TABLA', getenv('PROMOTIONS_TABLE') ?: 'hoteles_promociones');
define('HOTELID', (int) (getenv('HOTEL_ID') ?: 74));
define('RECAPTCHA_SECRET', '6LfTIcUSAAAAANZ3Z1pCu6OOoHR8dXXH8wwMwsSy');

function getConnection()
{
    $dsn = getenv('DB_DSN');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');

    if (!$dsn || !$user || $password === false) {
        throw new RuntimeException('Database configuration is incomplete.');
    }

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

$isAdmin = false;
