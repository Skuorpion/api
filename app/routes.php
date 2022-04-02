<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use Slim\App;

return function (App $app) {

    /*********** Ecritures ***********/

    $app->get('/dossiers/{uuid}/ecritures', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');

        $sql = 'SELECT * FROM ecritures WHERE dossier_uuid = :dossier';

        try {
            // Get DB Object
            $db = new db();
            // connect to DB
            $db = $db->connect();

            // query
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':dossier', $dossier);
            $stmt->execute();
            $ecritures = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // print out the result as json format
            $response->getBody()->write('{"items" =>' . json_encode($ecritures) . '}');
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

        // Testing the presence of properties
        if (
            !property_exists($values, 'label') ||
            !property_exists($values, 'date') ||
            !property_exists($values, 'type') ||
            !property_exists($values, 'amount')
        ) {
            $newResponse = $response->withStatus(400);
            return $newResponse;
        }

        // Testing the validity of the properties
        $tempDate = explode('-', $values->date);
        if (
            strlen($values->label) > 255 ||
            $values->amount < 0 ||
            !checkdate(intval($tempDate[1]), intval($tempDate[2]), intval($tempDate[0])) ||
            !in_array($values->type, array('C', 'D'))
        ) {
            $newResponse = $response->withStatus(400);
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
            // Connect to DB
            $db = $db->connect();

            // Query
            $stmt = $db->prepare($sql);
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
            // Show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->put('/dossiers/{uuidDossier}/ecritures/{uuidEcriture}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuidDossier');
        $ecriture = $request->getAttribute('uuidEcriture');
        $values = json_decode($request->getBody()->getContents());

        // Testing the presence of properties
        if (
            !property_exists($values, 'label') ||
            !property_exists($values, 'date') ||
            !property_exists($values, 'type') ||
            !property_exists($values, 'amount')
        ) {
            $newResponse = $response->withStatus(400);
            return $newResponse;
        }

        // Testing the validity of the properties
        $tempDate = explode('-', $values->date);
        if (
            strlen($values->label) > 255 ||
            $values->amount < 0 ||
            !checkdate(intval($tempDate[1]), intval($tempDate[2]), intval($tempDate[0])) ||
            !in_array($values->type, array('C', 'D'))
        ) {
            $newResponse = $response->withStatus(400);
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

            // Connect to DB
            $db = $db->connect();

            // Query
            $stmt = $db->prepare($sql);
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
            // Show error message as Json format
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

            // Connect to DB
            $db = $db->connect();

            // Query
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uuid', $ecriture);
            $stmt->bindParam(':dossier_uuid', $dossier);
            $stmt->execute();
            $db = null;

            $newResponse = $response->withStatus(204);
            return $newResponse;
        } catch (PDOException $e) {
            // Show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });


    /*********** Dossiers ***********/

    $app->get('/dossiers', function (Request $request, Response $response) {

        $sql = 'SELECT * FROM dossiers';

        try {
            // Get DB Object
            $db = new db();
            // connect to DB
            $db = $db->connect();

            // Query
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $dossiers = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Get "ecritures" for each "dossier"
            foreach ($dossiers as $dossier){
                $sql = 'SELECT *
                    FROM ecritures
                    WHERE dossier_uuid = :uuid';

                // Query
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':uuid', $dossier->uuid);
                $stmt->execute();
                $dossier->ecritures = $stmt->fetchAll(PDO::FETCH_OBJ);
            }

            $db = null;
            // Print out the result as json format
            $response->getBody()->write('{"items" =>' . json_encode( $dossiers ) . '}');
            return $response;
        } catch (PDOException $e) {
            // Show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->get('/dossiers/{uuid}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');

        $sql = 'SELECT * FROM dossiers WHERE uuid = :dossier';

        try {
            // Get DB Object
            $db = new db();
            // connect to DB
            $db = $db->connect();

            // Query
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':dossier', $dossier);
            $stmt->execute();
            $dossiers = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // Print out the result as json format
            $response->getBody()->write('{"items" =>' . json_encode($dossiers) . '}');
            return $response;
        } catch (PDOException $e) {
            // Show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->post('/dossiers', function (Request $request, Response $response) {
        $values = json_decode($request->getBody()->getContents());
        $uuid = Uuid::uuid4();

        // Testing the presence of properties
        if (
            !property_exists($values, 'login') ||
            !property_exists($values, 'password')
        ) {
            $newResponse = $response->withStatus(400);
            return $newResponse;
        }

        if (property_exists($values, 'name')) {
            if (strlen($values->name) > 255) {
                $newResponse = $response->withStatus(400);
                return $newResponse;
            }
        }

        // Testing the validity of the properties
        if (
            strlen($values->login) > 255 ||
            strlen($values->password) > 255
        ) {
            $newResponse = $response->withStatus(400);
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

            // Query
            $stmt = $db->prepare($sql);
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
            // Show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->put('/dossiers/{uuid}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');
        $values = json_decode($request->getBody()->getContents());

        // Testing the presence of properties
        if (
            !property_exists($values, 'login') ||
            !property_exists($values, 'password')
        ) {
            $newResponse = $response->withStatus(400);
            return $newResponse;
        }

        if (property_exists($values, 'name')) {
            if (strlen($values->name) > 255) {
                $newResponse = $response->withStatus(400);
                return $newResponse;
            }
        }

        // Testing the validity of the properties
        if (
            strlen($values->login) > 255 ||
            strlen($values->password) > 255
        ) {
            $newResponse = $response->withStatus(400);
            return $newResponse;
        }


        $sql = 'UPDATE dossiers SET 
                    password = :password,
                    name = :name
                WHERE uuid = :uuid';

        try {
            // Get DB Object
            $db = new db();

            // Connect to DB
            $db = $db->connect();

            // Query
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uuid', $dossier);
            $stmt->bindParam(':password', $values->password);
            $stmt->bindParam(':name', $values->name);
            $stmt->execute();
            $db = null;

            $newResponse = $response->withStatus(204);
            return $newResponse;
        } catch (PDOException $e) {
            // Show error message as Json format
            $response->getBody()->write('{"error": {"msg": ' . $e->getMessage() . '}');
            return $response;
        }
    });

    $app->delete('/dossiers/{uuid}', function (Request $request, Response $response) {
        $dossier = $request->getAttribute('uuid');
        $values = json_decode($request->getBody()->getContents());

        // Testing the presence of properties
        if (
            !property_exists($values, 'login') ||
            !property_exists($values, 'password')
        ) {
            $newResponse = $response->withStatus(400);
            return $newResponse;
        }


        $sql = 'DELETE FROM dossiers 
                WHERE uuid = :uuid AND (
                    SELECT count(uuid)
                    FROM ecritures
                    WHERE dossier_uuid = :uuid
                ) < 1';

        try {
            // Get DB Object
            $db = new db();

            // Connect to DB
            $db = $db->connect();

            // Query
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uuid', $dossier);
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
};
