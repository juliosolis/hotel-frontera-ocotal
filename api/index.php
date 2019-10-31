<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

define('DEV', 'js@juliosolis.com');
define('TABLA', 'hoteles_promociones');
define('HOTELID', 74);

function getConnection()
{
    $dbhost = "localhost";
    $dbuser = 'root';//"forge";
    $dbpass = '';//"aNJ4RJXLYMItxxOOar3W";
    $dbname = 'nearbybooking';//"nearbybooking";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

$app = AppFactory::create();
$app->setBasePath('/api');

$app->get('/', function (Request $request, Response $response, $args) {
    return $response->withHeader('Location', 'http://hotel.fronteraocotal.com/')->withStatus(302);
});

$app->get('/db', function (Request $request, Response $response, $args) {

    $db = getConnection();
    $stm = $db->query("SELECT VERSION()");

    $version = $stm->fetch();

    $payload = json_encode(['success' => true, 'msg' => 'db connected', 'version' => $version], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json')->withStatus(302);
});

$app->post('/send-email', function (Request $request, Response $response, $args) {

    $data = $request->getParsedBody();

    if (
        empty($data['nombre']) || empty($data['email']) ||
        empty($data['asunto']) || empty($data['mensaje'])) {
        $result = false;
        $msg = 'Por favor rellene todos los campos, son requeridos';
        $html = '';
    } else {
        $g_recaptcha_response = http_build_query([
            'secret' => '6LfTIcUSAAAAANZ3Z1pCu6OOoHR8dXXH8wwMwsSy',
            'response' => $data['g-recaptcha-response']
        ]);

        $opts = ['http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $g_recaptcha_response
        ]];
        $context = stream_context_create($opts);

        $recaptcha_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $recaptcha_result = json_decode($recaptcha_response);

        if (!$recaptcha_result->success || empty($recaptcha_result->success)) {
            $msg = 'reCAPTCHA verification failed';
            $result = $recaptcha_result->success;
            $html = '';
        } else {
            $mail = new PHPMailer;

            $html = '<h3>De: ' . $data['nombre'] . "</h3>";
            $html .= '<h3>Correo: ' . $data['email'] . "</h3>";
            $html .= '<h3>Asunto: ' . $data['asunto'] . "</h3>";
            $html .= '<p>Mensaje: ' . $data['mensaje'] . "</p>";

            $txt = 'De: ' . $data['nombre'] . "\n";
            $txt .= 'Correo: ' . $data['email'] . "\n";
            $txt .= 'Asunto: ' . $data['asunto'] . "\n";
            $txt .= 'Mensaje: ' . $data['mensaje'] . "\n";

            $mail->setFrom('web@hotelfronteraocotal.com', 'Hotel Frontera');
            $mail->addReplyTo('hotelfronterasa@yahoo.com', 'Hotel Frontera');
            //$mail->addAddress('js@juliosolis.com', 'Julio Solis');
            $mail->addAddress('hotelfronterasa@yahoo.com', 'Hotel Frontera');
            $mail->addAddress('js@juliosolis.com', 'Julio Solis L');
            $mail->Subject = 'Contacto Web: ' . $data['asunto'];
            $mail->msgHTML($html);
            $mail->AltBody = $txt;

            if (!$mail->send()) {
                $result = false;
                $msg = 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                $result = true;
                $msg = 'Message sent!';
            }
        }
    }

    $payload = json_encode(['success' => $result, 'msg' => $msg, 'mensaje' => $html], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');

});

/*
 *
 * Editar
 * $sql = "UPDATE hoteles_promociones SET hotel_id = ?, name=?, surname=?, sex=? WHERE id=?";
 * $stmt= $pdo->prepare($sql);
 * $stmt->execute([HOTELID, $name, $surname, $sex, $id]);
 *
 * Insertar
 * $stm = $pdo->exec("INSERT INTO hoteles_promociones(hotel_id, population) VALUES ('Iraq', 38274000)");
 * $rowid = $pdo->lastInsertId();
 *
 */

$app->get('/promociones', function (Request $request, Response $response, $args) {

    $db = getConnection();
    $stm = $db->query('SELECT * FROM ' . TABLA . ' WHERE hotel_id = ' . HOTELID);
    $promociones = $stm->fetchAll(PDO::FETCH_ASSOC);
    $total = $stm->rowCount();

    $payload = json_encode(['success' => true, 'msg' => 'db connected', 'total' => $total, 'promociones' => $promociones], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json')->withStatus(302);
});

$app->get('/promociones/{id}', function (Request $request, Response $response, $args) {

    $db = getConnection();
    $stm = $db->query("SELECT * FROM hoteles_promociones WHERE hotel_id = 74 and id = " . $args['id']);
    $promocion = $stm->fetch(PDO::FETCH_ASSOC);
    $success = $promocion ? true : false;
    $msg = $promocion ? 'Promoción existe.' : 'Promoción no existe.';

    $payload = json_encode(['success' => $success, 'msg' => $msg, 'promocion' => $promocion], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json')->withStatus(302);
});

$app->post('/promociones', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $db = getConnection();

    if (empty($data['titulo']) || empty($data['precio']) || empty($data['descripcion'])){
        $success = false;
        $msg = 'Por favor rellene todos los campos requeridos';
    }else{
        $success = false;
        $msg = 'Hubo un error, por favor comuniquese con el desarrollador ' . DEV;

        $stm = "INSERT INTO hoteles_promociones (hotel_id, titulo, precio, descripcion, fecha_creacion) VALUES (?,?,?,?,?)";
        $db->prepare($stm)->execute([HOTELID, $data['titulo'], $data['precio'], $data['descripcion'], date('Y-m-d H:i:s')]);
        $rowid = $db->lastInsertId();

        if ($rowid){
            $success = true;
            $msg = 'Promocion guardada exitosamente';
        }
    }

    $payload = json_encode(['success' => $success, 'msg' => $msg,], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/promociones/{id}', function (Request $request, Response $response, $args) {

    $payload = json_encode(['success' => true, 'msg' => 'editing...',], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/promociones/{id}', function (Request $request, Response $response, $args) {
    $db = getConnection();

    $affected = $db->exec('DELETE FROM countries WHERE hotel_id = 74 and id = ' . $args['id']);
    $success = $affected ? true : false;
    $msg = $affected ? 'Promoción eliminada.' : 'Hubo un error, por favor comuniquese con el desarrollador ' . DEV;

    $payload = json_encode(['success' => $success, 'msg' => $msg,], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/promocion/{id}', function (Request $request, Response $response) {

});
$app->run();
