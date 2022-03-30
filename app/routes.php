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
            $ecritures = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // print out the result as json format
            $response->getBody()->write(json_encode($ecritures));
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
            $dossiers = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // print out the result as json format
            $response->getBody()->write(json_encode($dossiers));
            return $response;
        } catch (PDOException $e) {
            // show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->post('/dossiers', function (Request $request, Response $response) {
        $values = json_decode($request->getBody()->getContents());
        $uuid = Uuid::uuid4();

        if (!property_exists($values, 'login') || !property_exists($values, 'password')) {
            $newResponse = $response->withStatus(400 );
            return $newResponse;
        }

        $sql = 'INSERT INTO dossiers (
                        uuid,
                        login,
                        password,
                        name
                    ) VALUES (
                        :uuid,
                        :login,
                        :password,
                        :name
                    )';

        try {
            // Get DB Object
            $db = new db();
            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':uuid', $uuid);
            $stmt->bindParam(':login', $values->login);
            $stmt->bindParam(':password', $values->password);
            $stmt->bindParam(':name', $values->name);
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

    $app->put('/dossiers/{uuid}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');
        $values = json_decode($request->getBody()->getContents());

        if (!property_exists($values, 'login') || !property_exists($values, 'password')) {
            $newResponse = $response->withStatus(400 );
            return $newResponse;
        }

        $sql = 'UPDATE dossiers SET 
                    password = :password,
                    name = :name
                WHERE uuid = :uuid';

        try {
            // Get DB Object
            $db = new db();

            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':uuid', $dossier);
            $stmt->bindParam(':password', $values->password);
            $stmt->bindParam(':name', $values->name);
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

    $app->delete('/dossiers/{uuid}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');
        $values = json_decode($request->getBody()->getContents());

        if (!property_exists($values, 'login') || !property_exists($values, 'password')) {
            $newResponse = $response->withStatus(400 );
            return $newResponse;
        }

        $sql = 'SELECT count(*) AS NbEcriture
                FROM ecritures
                WHERE uuid = :uuid';

        try {
            // Get DB Object
            $db = new db();

            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':uuid', $dossier);
            $stmt->execute();
            $NbEcriture = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            if ($NbEcriture[0]->NbEcriture > 0) {
                $newResponse = $response->withStatus(400 );
                return $newResponse;
            }

        } catch (PDOException $e) {
            // show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }

        $sql = 'DELETE FROM dossiers WHERE uuid = :uuid';

        try {
            // Get DB Object
            $db = new db();

            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':uuid', $dossier);
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
};
