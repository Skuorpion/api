<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/dossiers/{uuid}/ecritures', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');

        $sql = 'SELECT * 
                FROM ecritures
                WHERE dossier_uuid = :dossier';

        try {
            // Get DB Object
            $db = new db();
            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':dossier', $dossier);
            $stmt->execute();
            $arts = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // print out the result as json format
            $response->getBody()->write(json_encode($arts));
            return $response;
        } catch (PDOException $e) {
            // show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });
};
