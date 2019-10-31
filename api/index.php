<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

define('TABLE', 'hoteles_promociones');
function getConnection()
{
    $dbhost = "localhost";
    $dbuser = 'root';//"forge";
    $dbpass = '';//"aNJ4RJXLYMItxxOOar3W";
    $dbname = 'nearby';//"nearbybooking";
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

$app->post('/guardar-promocion', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    $payload = json_encode(['success' => true, 'msg' => 'saving...',], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/editar-promocion/{id}', function (Request $request, Response $response, $args) {

    $payload = json_encode(['success' => true, 'msg' => 'editing...',], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/eliminar-promocion/{id}', function (Request $request, Response $response, $args) {

    $id = 0;
    $db = getConnection();
    $nrows = $db->exec('DELETE FROM countries WHERE id = ' . $id);

    $payload = json_encode(['success' => true, 'msg' => 'deleting...',], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
