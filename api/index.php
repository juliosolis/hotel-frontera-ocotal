<?php

use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../settings.php';

$app = AppFactory::create();
$app->setBasePath('/api');

function jsonResponse(Response $response, array $payload, $status = 200)
{
    $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
}

function requestData(Request $request)
{
    $data = (array) $request->getParsedBody();
    return array_map(function ($value) {
        return is_string($value) ? trim($value) : $value;
    }, $data);
}

function integerRouteArg(array $args, $name)
{
    $value = filter_var(isset($args[$name]) ? $args[$name] : null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $value === false ? null : $value;
}

function isPromotionAdmin()
{
    global $isAdmin;
    return $isAdmin === true;
}

function promotionImageUpload($field, $promotionId)
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $upload = $_FILES[$field];
    if ($upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > 5 * 1024 * 1024 || !is_uploaded_file($upload['tmp_name'])) {
        throw new RuntimeException('La imagen no pudo procesarse.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Solo se permiten imágenes JPG o PNG.');
    }

    $size = getimagesize($upload['tmp_name']);
    if (!$size || $size[0] < $size[1]) {
        throw new RuntimeException('Solo se permiten imágenes horizontales.');
    }

    $directory = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/img/promociones';
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('No fue posible preparar el directorio de imágenes.');
    }

    $filename = (int) $promotionId . '.' . $extensions[$mime];
    if (!move_uploaded_file($upload['tmp_name'], $directory . '/' . $filename)) {
        throw new RuntimeException('No fue posible guardar la imagen.');
    }

    return $filename;
}

$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', 'https://hotelfronteraocotal.com/')->withStatus(302);
});

$app->post('/send-email', function (Request $request, Response $response) {
    $data = requestData($request);
    $fields = ['nombre' => 120, 'email' => 254, 'asunto' => 160, 'mensaje' => 4000];
    foreach ($fields as $field => $maximum) {
        if (empty($data[$field]) || mb_strlen($data[$field]) > $maximum) {
            return jsonResponse($response, ['success' => false, 'msg' => 'Por favor revise los campos requeridos.'], 422);
        }
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return jsonResponse($response, ['success' => false, 'msg' => 'Ingrese un correo electrónico válido.'], 422);
    }

    $recaptchaSecret = defined('RECAPTCHA_SECRET') ? RECAPTCHA_SECRET : '';
    if (!$recaptchaSecret || empty($data['g-recaptcha-response'])) {
        return jsonResponse($response, ['success' => false, 'msg' => 'No fue posible validar el formulario.'], 422);
    }

    $captcha = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt_array($captcha, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['secret' => $recaptchaSecret, 'response' => $data['g-recaptcha-response']]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $captchaResponse = curl_exec($captcha);
    $captchaStatus = curl_getinfo($captcha, CURLINFO_HTTP_CODE);
    curl_close($captcha);
    $captchaResult = json_decode($captchaResponse ?: '');
    if ($captchaStatus !== 200 || empty($captchaResult->success)) {
        return jsonResponse($response, ['success' => false, 'msg' => 'No fue posible validar el formulario.'], 422);
    }

    $mail = new PHPMailer(true);
    $mail->setFrom('web@hotelfronteraocotal.com', 'Hotel Frontera');
    $mail->addReplyTo($data['email'], $data['nombre']);
    $mail->addAddress('hotelfronterasa@yahoo.com', 'Hotel Frontera');
    $mail->Subject = 'Contacto Web: ' . $data['asunto'];
    $mail->isHTML(true);
    $mail->Body = '<h3>De: ' . htmlspecialchars($data['nombre'], ENT_QUOTES, 'UTF-8') . '</h3>'
        . '<p>Correo: ' . htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p>Mensaje: ' . nl2br(htmlspecialchars($data['mensaje'], ENT_QUOTES, 'UTF-8')) . '</p>';
    $mail->AltBody = "De: {$data['nombre']}\nCorreo: {$data['email']}\nMensaje: {$data['mensaje']}";

    try {
        $mail->send();
    } catch (Throwable $exception) {
        error_log('Hotel contact mail failed: ' . $exception->getMessage());
        return jsonResponse($response, ['success' => false, 'msg' => 'No fue posible enviar el mensaje. Intente de nuevo.'], 502);
    }

    return jsonResponse($response, ['success' => true, 'msg' => 'Mensaje enviado correctamente.']);
});

$app->get('/promociones', function (Request $request, Response $response) {
    $db = getConnection();
    $statement = $db->prepare('SELECT id, titulo, precio, descripcion, fecha_creacion FROM ' . TABLA . ' WHERE hotel_id = ? ORDER BY id DESC');
    $statement->execute([HOTELID]);
    $promotions = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($promotions as &$promotion) {
        $promotion['img'] = '/img/logo@2x.png';
        foreach (['jpg', 'png'] as $extension) {
            $path = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/img/promociones/' . (int) $promotion['id'] . '.' . $extension;
            if (is_file($path)) {
                $promotion['img'] = '/img/promociones/' . (int) $promotion['id'] . '.' . $extension;
                break;
            }
        }
    }

    return jsonResponse($response, ['success' => true, 'total' => count($promotions), 'promociones' => $promotions]);
});

$app->get('/promociones/{id}', function (Request $request, Response $response, array $args) {
    $id = integerRouteArg($args, 'id');
    if (!$id) return jsonResponse($response, ['success' => false, 'msg' => 'Promoción no encontrada.'], 404);
    $statement = getConnection()->prepare('SELECT id, titulo, precio, descripcion FROM ' . TABLA . ' WHERE hotel_id = ? AND id = ?');
    $statement->execute([HOTELID, $id]);
    $promotion = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$promotion) return jsonResponse($response, ['success' => false, 'msg' => 'Promoción no encontrada.'], 404);
    return jsonResponse($response, ['success' => true, 'promocion' => $promotion]);
});

foreach (['/promociones', '/promociones/{id}'] as $route) {
    $app->map(['POST', 'DELETE'], $route, function (Request $request, Response $response, array $args) {
        if (!isPromotionAdmin()) return jsonResponse($response, ['success' => false, 'msg' => 'No autorizado.'], 403);
        $id = integerRouteArg($args, 'id');
        $db = getConnection();

        if ($request->getMethod() === 'DELETE') {
            if (!$id) return jsonResponse($response, ['success' => false, 'msg' => 'Promoción no encontrada.'], 404);
            $statement = $db->prepare('DELETE FROM ' . TABLA . ' WHERE hotel_id = ? AND id = ?');
            $statement->execute([HOTELID, $id]);
            return jsonResponse($response, ['success' => $statement->rowCount() > 0]);
        }

        $data = requestData($request);
        if (empty($data['titulo']) || empty($data['precio']) || empty($data['descripcion']) || mb_strlen($data['titulo']) > 160 || mb_strlen($data['descripcion']) > 4000 || !is_numeric(str_replace('$', '', $data['precio']))) {
            return jsonResponse($response, ['success' => false, 'msg' => 'Por favor revise los campos requeridos.'], 422);
        }
        $price = (float) str_replace('$', '', $data['precio']);
        if ($id) {
            $statement = $db->prepare('UPDATE ' . TABLA . ' SET titulo = ?, precio = ?, descripcion = ? WHERE hotel_id = ? AND id = ?');
            $statement->execute([$data['titulo'], $price, $data['descripcion'], HOTELID, $id]);
        } else {
            $statement = $db->prepare('INSERT INTO ' . TABLA . ' (hotel_id, titulo, precio, descripcion, fecha_creacion) VALUES (?, ?, ?, ?, ?)');
            $statement->execute([HOTELID, $data['titulo'], $price, $data['descripcion'], date('Y-m-d H:i:s')]);
            $id = (int) $db->lastInsertId();
        }
        try {
            promotionImageUpload('imagen', $id);
        } catch (RuntimeException $exception) {
            return jsonResponse($response, ['success' => false, 'msg' => $exception->getMessage()], 422);
        }
        return jsonResponse($response, ['success' => true, 'msg' => 'Promoción guardada correctamente.']);
    });
}

$app->run();
