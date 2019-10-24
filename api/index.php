<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/api');

$app->get('/', function (Request $request, Response $response, $args) {
    return $response->withHeader('Location', 'http://hotel.fronteraocotal.com/')->withStatus(302);
});

$app->post('/send-email', function (Request $request, Response $response, $args) {

    $data = $request->getParsedBody();
    $mail = new PHPMailer;

    $html = '<h3>De: ' . $data['nombre'] . "</h3>";
    $html .= '<h3>Correo: ' . $data['email'] . "</h3>";
    $html .= '<h3>Asunto: ' . $data['asunto'] . "</h3>";
    $html .= '<p>Mensaje: ' . $data['mensaje'] . "</p>";

    $txt = 'De: ' . $data['nombre'] . "\n";
    $txt .= 'Correo: ' . $data['email'] . "\n";
    $txt .= 'Asunto: ' . $data['asunto'] . "\n";
    $txt .= 'Mensaje: ' . $data['mensaje'] . "\n";

    $mail->setFrom('web@hotelfronteraocotal.com', 'Julio Solis');
    $mail->addReplyTo('hotelfronterasa@yahoo.com', 'Hotel Frontera');
    $mail->addAddress('js@juliosolis.com', 'Julio Solis');
    //$mail->addAddress('hotelfronterasa@yahoo.com', 'Hotel Frontera');
    $mail->addBCC('js@juliosolis.com', 'Julio Solis L');
    $mail->Subject = 'Contacto Web: ' . $data['asunto'];
    $mail->msgHTML($mensaje);
    $mail->AltBody = $txt;

    if (!$mail->send()) {
        $result = false;
        $msg = 'Mailer Error: '. $mail->ErrorInfo;
    } else {
        $result = true;
        $msg =  'Message sent!';
    }

    $payload = json_encode(['success' => $result, 'msg' => $msg, 'mensaje' => $mensaje], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');

});

$app->run();
