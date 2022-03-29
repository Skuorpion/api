# Macompta - Ecritures

Le but de ce projet est de créer des APIs dans le cadre d'une application web PHP de gestion d'écritures comptables.

## Desciption

Une écriture comptable est une transaction financière qui doit être en crédit ou en débit.

Elle est principalement composée d'un montant, d'un libellé, d'une date et d'un type d'opération.

Chaque écriture est rattaché à un dossier.

Il faudra créer les APIs REST permettant de pouvoir gérer ces écritures sur le principe d'un CRUD (Create / Update / Delete).

Il faudra en faire de même pour la ressource dossier.

Elles devront être testées via POSTMAN ou équivalent.

Un export des données POSTMAN devra être fourni pour pouvoir facilement exécuter les appels. (format POSTMAN, curl...)

## Prérequis

* PHP 7+
* Symfony ou équivalent (SlimPHP*,  Laravel...)
* MySQL
* Git
* Pas d'utilisation d'un ORM (utilisation de requêtes SQL natives)
* Idéalement, respect des conventions PSR2. Fichier des rulesets joint. (voir l'outil phpcs pour plus de détail)


Le candidat peut utiliser les outils de son choix pour le développement en dehors des prérequis.

nb: Pour SlimPHP, un skeleton de base utilisé en interne PEUT ETRE fourni.

Mais l'idée est d'utiliser la plateforme que le candidat connait le mieux. Il ne s'agit pas de juger de la connaissance d'un framework.

## Points d'évaluation

* Qualité du code
* Robustesse des contrôles

Il faudra travailler sur la branche main, et créer un commit pour chaque exercice: `git commit -m "Exercice 1: création de la table"`.

Une fois terminé, Il faudra zipper le projet final et l'envoyer par mail à epham@macompta.fr et à echartier@macompta.fr.

## Implémention

L'uuid n'est pas forcément connu à ce stade. Il s'agit d'un format pour gérer les identifiants (comme un auto-increment) mais avec un format spécifique. 
Il peut être gérer comme une string de 36 caractères au niveau base. Au niveau "usage", cela se traite comme n'importe quel identiifiant.

Pour la gestion des uuid, le package ramsey/uuid (https://github.com/ramsey/uuid) peut être utiliser : 

 use Ramsey\Uuid\Uuid;
 $uuid = Uuid::uuid4();



Toutes les opérations sont en général rattachés à un dossier. Il faut donc que les endpoints commencent par identifiier le dossier.



## Exercice 1

Créer la table d'écritures, et pouvoir lancer une migration pour générer cette table.

La table d'écritures est composée des champs suivants:

| Nom du champ | Type | description |
|`uuid` | PRIMARY KEY  VARCHAR(36)| uuid de l'écriture |
|`label` | VARCHAR 255 NOT NULL DEFAULT '' | Libellé de l'écriture |
|`date` | date NOT NULL DEFAULT '0000-00-00' | Date de l'écriture |
|`type` | Enum "C", "D" | Type d'opération "C" => Crédit, "D" => Débit |
|`amount` | double(14,2) NOT NULL DEFAULT 0.00 | Montant |
|`created_at` | timestamp NULL DEFAULT current_timestamp() | Date de création |
|`updated_at` | timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() | Date  de modification |


La table dossiers est composée des champs suivants:

| Nom du champ | Type | description |
|`uuid` | PRIMARY KEY  VARCHAR(36)| uuid du dossier |
|`login` | VARCHAR 255 NOT NULL DEFAULT | identifiant de connexion |
|`password` | VARCHAR(255) NOT NULL | mdp du dossier
|`name` | nom du dossier
|`created_at` | timestamp NULL DEFAULT current_timestamp() | Date de création |
|`updated_at` | timestamp NULL DEFAULT 

Créé une clé étrangère dans la table ecriutres(dossier_uuid) vers dosssiers(uuid) avec UPDATE RESTRICT et DELETE CASCADE.

## Exercice 2

Création d'un endpoint pour récupérer la liste des écritures pour UN dossier sous ce format

GET /dossiers/{uuid}/ecritures
Reponse

200
{
	"items" => [
		{ 
			
			label,
			[...]
		},
		{
			label,
			[...]
		}
		
	]
}

## Exercice 3

Création d'un endpoint pour l'ajout d'une ecriture DANS UN dossier.

POST /dossiers/{uuid}/ecritures
Body
{
	"label": "xxx",
	"date" : "dd/mm/yyyy",
	[...]
}



Reponse 201
{
	"uuid": "uuid qui vient d'être généré"
}

**Contraintes de validation:**

Le montant ne doit pas être négatif.
la date saisie doit être une date valide.


## Exercice 4

Création d'un endpoint pour modifier une ecriture.
Dans le body devra être transmis systématiquement TOUS les champs. Pas seulement ceux qui doivent être modifiés.

PUT /dossiers/{uuid}/ecritures/{uuid}
Body
{
	"uuid": "eee"
	"label": "xxx"
	[...]
	""
}


Reponse 204

**Contraintes de validation:**

Les mêmes contraintes de validation s'applique que dans la création.

## Exercice 5

Bon ben, pas de surpise, on supprime !

DELETE /dossiers/{uuid}/ecritures/{uuid}

Response 204.

## Exercice 6,7,8,9

Même chose que pour écritures mais pour dossier : GET , POST, PUT, DELETE.

GET /dossiers/{uuid}
POST /dossiers
PUT /dossiers/{uuid}
DELETE /dossiers/{uuid}


**Contraintes de validation:**
login / password obligatoire
un dossier avec ecritures ne peut être supprimer.
login NON modifiale


## Exercice 10

Endpoint pour récupérer la liste de TOUS les dossiers avec ses écritures.

Le format de sortie est à définir par le candidat mais doit être proche des deux endpoints GET précédent.

La méthode pour récupérer les données devra être optimisé. S'il y a un grand nombre de données, cela devrait avoir peu d'impact sur la requete.
