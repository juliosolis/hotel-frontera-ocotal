<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../vendor/autoload.php';
require '../../settings.php';

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
    return $response->withHeader('Content-Type', 'application/json');
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

$app->get('/promociones', function (Request $request, Response $response, $args) {

    $db = getConnection();
    $stm = $db->query('SELECT * FROM ' . TABLA . ' WHERE hotel_id = ' . HOTELID . ' ORDER BY id DESC');
    $promociones = $stm->fetchAll(PDO::FETCH_ASSOC);
    $total = $stm->rowCount();

    foreach ($promociones as $ix => $promo) {
        $promociones[$ix]['img'] = '/img/logo@2x.png';
        $file = $_SERVER['DOCUMENT_ROOT'] . '/img/promociones/' . $promo['id'];

        if (file_exists($file . '.' . 'jpg')) {
            $promociones[$ix]['img'] = "/img/promociones/" . $promo['id'] . "." . "jpg";
        }elseif(file_exists($file . '.' . 'png')){
            $promociones[$ix]['img'] = "/img/promociones/" . $promo['id'] . "." . "png";
        }
    }

    $payload = json_encode(['success' => true, 'msg' => 'db connected', 'total' => $total, 'promociones' => $promociones], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/promociones/{id}', function (Request $request, Response $response, $args) {

    $db = getConnection();
    $stm = $db->query('SELECT * FROM ' . TABLA . ' WHERE hotel_id = 74 and id = ' . $args['id']);
    $promocion = $stm->fetch(PDO::FETCH_ASSOC);
    $promocion['precio'] = '$ ' . number_format($promocion['precio'], 2);
    $success = $promocion ? true : false;
    $msg = $promocion ? 'Promoción existe.' : 'Promoción no existe.';

    $payload = json_encode(['success' => $success, 'msg' => $msg, 'promocion' => $promocion], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/promociones', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $db = getConnection();
    $imagen_existe = !empty($_FILES['imagen']['name']);
    list($width, $height) = !empty($_FILES['imagen']['tmp_name']) ? getimagesize($_FILES['imagen']['tmp_name']) : '';

    if (empty($data['titulo']) || empty($data['precio']) || empty($data['descripcion'])) {
        $success = false;
        $msg = 'Por favor rellene todos los campos requeridos';
    } elseif ($imagen_existe && !in_array($_FILES['imagen']['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
        $success = false;
        $msg = 'Solo se permiten imagenes con formato jpg y png';
    } elseif ($imagen_existe && ($width < $height) == true) {
        $success = false;
        $msg = 'Solo se permiten imagenes horizontales';
    } else {
        $success = false;
        $msg = 'Hubo un error, por favor comuniquese con el desarrollador ' . DEV;

        $stm = 'INSERT INTO ' . TABLA . ' (hotel_id, titulo, precio, descripcion, fecha_creacion) VALUES (?,?,?,?,?)';
        $db->prepare($stm)->execute([HOTELID, $data['titulo'], $data['precio'], $data['descripcion'], date('Y-m-d H:i:s')]);
        $rowID = $db->lastInsertId();

        if ($rowID) {
            if ($imagen_existe) {
                $destino = $_SERVER['DOCUMENT_ROOT'] . '/img/promociones/';
                list($name, $ext) = explode('.', $_FILES['imagen']['name']);

                $filename = strval($rowID) . "." . strtolower($ext);
                move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . $filename);
            }
            $success = true;
            $msg = 'Promoción guardada exitosamente';
        }
    }

    $payload = json_encode(['success' => $success, 'msg' => $msg], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/promociones/{id}', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $db = getConnection();
    $imagen_existe = !empty($_FILES['imagen']['name']);
    list($width, $height) = !empty($_FILES['imagen']['tmp_name']) ? getimagesize($_FILES['imagen']['tmp_name']) : '';

    if (empty($data['titulo']) || empty($data['precio']) || empty($data['descripcion'])) {
        $success = false;
        $msg = 'Por favor rellene todos los campos requeridos';
    } elseif ($imagen_existe && !in_array($_FILES['imagen']['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
        $success = false;
        $msg = 'Solo se permiten imagenes con formato jpg y png';
    } elseif ($imagen_existe && ($width < $height) == true) {
        $success = false;
        $msg = 'Solo se permiten imagenes horizontales';
    } else {
        $success = false;
        $msg = 'Hubo un error, por favor comuniquese con el desarrollador ' . DEV;

        $sql = 'UPDATE ' . TABLA . ' SET titulo = ?, precio = ?, descripcion = ? WHERE hotel_id = ' . HOTELID . ' and id = ' . $args['id'];
        $precio = str_replace('$ ', '', $data['precio']);
        $db->prepare($sql)->execute([$data['titulo'], $precio, $data['descripcion']]);

        if ($db) {
            if ($imagen_existe) {
                $destino = $_SERVER['DOCUMENT_ROOT'] . '/img/promociones/';
                list($name, $ext) = explode('.', $_FILES['imagen']['name']);
                $filename = strval($data['id']) . "." . $ext;

                move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . $filename);
            }
            $success = true;
            $msg = 'Promoción editada exitosamente';
        }
    }

    $payload = json_encode(['success' => $success, 'msg' => $msg], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/promociones/{id}', function (Request $request, Response $response, $args) {
    $db = getConnection();

    $affected = $db->exec('DELETE FROM ' . TABLA . ' WHERE hotel_id = 74 and id = ' . $args['id']);
    $success = $affected ? true : false;
    $msg = $affected ? 'Promoción eliminada.' : 'Hubo un error, por favor comuniquese con el desarrollador ' . DEV;

    $payload = json_encode(['success' => $success, 'msg' => $msg,], JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
