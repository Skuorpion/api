<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Ramsey\Uuid\Uuid;

return function (App $app) {

    /*********** Ecritures ***********/

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

    $app->post('/dossiers/{uuid}/ecritures', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');
        $values = json_decode($request->getBody()->getContents());
        $uuid = Uuid::uuid4();

        $tempDate = explode('-', $values->date);
        if ($values->amount < 0 || !checkdate(intval($tempDate[1]), intval($tempDate[2]), intval($tempDate[0])) ) {
            $newResponse = $response->withStatus(400 );
            return $newResponse;
        }

        $sql = 'INSERT INTO ecritures (
                        uuid,
                        label,
                        date,
                        type,
                        amount,
                        dossier_uuid
                    ) VALUES (
                        :uuid,
                        :label,
                        :date,
                        :type,
                        :amount,
                        :dossier_uuid
                    )';

        try {
            // Get DB Object
            $db = new db();
            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':uuid', $uuid);
            $stmt->bindParam(':label', $values->label);
            $stmt->bindParam(':date', $values->date);
            $stmt->bindParam(':type', $values->type);
            $stmt->bindParam(':amount', $values->amount);
            $stmt->bindParam(':dossier_uuid', $dossier);
            $stmt->execute();
            $db = null;

            $newResponse = $response->withStatus(201);
            $newResponse->getBody()->write('{"' . $uuid . '": "uuid qui vient d\'être généré"}');
            return $newResponse;
        } catch (PDOException $e) {
            // show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->put('/dossiers/{uuidDossier}/ecritures/{uuidEcriture}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuidDossier');
        $ecriture = $request->getAttribute('uuidEcriture');
        $values = json_decode($request->getBody()->getContents());

        $tempDate = explode('-', $values->date);
        if ($values->amount < 0 || !checkdate(intval($tempDate[1]), intval($tempDate[2]), intval($tempDate[0])) ) {
            $newResponse = $response->withStatus(400 );
            return $newResponse;
        }

        $sql = 'UPDATE ecritures SET 
                    label = :label,
                    date = :date,
                    type = :type,
                    amount = :amount,
                    dossier_uuid = :dossier_uuid
                WHERE uuid = :uuid';

        try {
            // Get DB Object
            $db = new db();

            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':uuid', $ecriture);
            $stmt->bindParam(':label', $values->label);
            $stmt->bindParam(':date', $values->date);
            $stmt->bindParam(':type', $values->type);
            $stmt->bindParam(':amount', $values->amount);
            $stmt->bindParam(':dossier_uuid', $dossier);
            $stmt->execute();
            $db = null;

            $newResponse = $response->withStatus(204);
            return $newResponse;
        } catch (PDOException $e) {
            // show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->delete('/dossiers/{uuidDossier}/ecritures/{uuidEcriture}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuidDossier');
        $ecriture = $request->getAttribute('uuidEcriture');

        $sql = 'DELETE FROM ecritures WHERE uuid = :uuid AND dossier_uuid = :dossier_uuid';

        try {
            // Get DB Object
            $db = new db();

            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':uuid', $ecriture);
            $stmt->bindParam(':dossier_uuid', $dossier);
            $stmt->execute();
            $db = null;

            // print out the result as json format
            $newResponse = $response->withStatus(204);
            return $newResponse;
        } catch (PDOException $e) {
            // show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });





    /*********** Dossiers ***********/

    $app->get('/dossiers/{uuid}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');

        $sql = 'SELECT name
                FROM dossiers
                WHERE uuid = :dossier';

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
