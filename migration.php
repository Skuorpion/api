<?php
// include DB connection file
require __DIR__ . '/app/db.php';

$sqlCreateEcrituresTable = "CREATE TABLE `ecritures` (
    `uuid` VARCHAR(36) NOT NULL UNIQUE,
    `label` VARCHAR(255) NOT NULL DEFAULT '',
    `date` DATE NULL,
    `type` ENUM('C', 'D'),
    `amount` DOUBLE(14,2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `dossier_uuid` VARCHAR(36),
    PRIMARY KEY (`uuid`),
    FOREIGN KEY (dossier_uuid) REFERENCES dossiers(uuid) ON UPDATE RESTRICT ON DELETE CASCADE
);";

$sqlCreateDossiersTable = "CREATE TABLE `dossiers` (
    `uuid` VARCHAR(36) NOT NULL UNIQUE,
    `login` VARCHAR(255) NOT NULL DEFAULT '',
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`uuid`)
);";



try {
    // Get DB Object
    $db = new db();

    // connect to DB
    $db = $db->connect();

    // query
    $stmt = $db->query( $sqlCreateDossiersTable );
    $arts = $stmt->fetchAll( PDO::FETCH_OBJ );

    // query
    $stmt = $db->query( $sqlCreateEcrituresTable );
    $arts = $stmt->fetchAll( PDO::FETCH_OBJ );
    $db = null; // clear db object

    // print out the result as json format
    echo json_encode( $arts );

}
catch( PDOException $e ) {
    // show error message as Json format
    echo '{"error": {"msg": ' . $e->getMessage() . '}';
}