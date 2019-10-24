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
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->post('/send-email', function (Request $request, Response $response, $args) {

    $data = $request->getParsedBody();
    $mail = new PHPMailer;

    /*$mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'AKIAIRFALSLKJOFP45PQ';
    $mail->Password = 'AlIddGpZYemr73TzHPaVoInXJAt/IwyuFSCu0CiY1S9U';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;*/

    $mail->setFrom('soporte@nearbybooking.com', 'Julio Solis');
    $mail->addReplyTo('juliosolai@gmail.com', 'Julio Solis');
    $mail->addAddress('js@juliosolis.com', 'Cesar Lainez');
    $mail->Subject = 'Formulario de Contacto del Sitio Web';
    $mail->msgHTML($data['mensaje']);
    $mail->AltBody = $data['mensaje'];

    if (!$mail->send()) {
        $result = false;
        $msg = 'Mailer Error: '. $mail->ErrorInfo;
    } else {
        $result = true;
        $msg =  'Message sent!';
    }

    $payload = json_encode(['success' => $result, 'msg' => $msg, 'post' => $data], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');

});

$app->run();
