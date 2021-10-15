# Macompta - Ecritures

Le but de ce projet et de créer une application web PHP de gestion d'écritures comptable.

## Desciption

Une écriture comptable et une transaction financière qui doit être en crédit ou en débit.

Elle est principalement composée d'un montant, un libellé, une date, et un type d'opération.

Il faudra créer une application web permettant de pouvoir gérer ces écritures sur le principe d'un CRUD (Create / Update / Delete).

L'application devra démarrer sur la liste des écritures.

On devra pouvoir ajouter une nouvelle écriture.

Dans la liste des écritures, on devra pouvoir modifier et supprimer une ligne.

## Prérequis

* PHP 7
* Symfony
* MySQL
* Git

Le candidat peut utiliser les outils de son choix pour le développement en dehors des prérequis.

## Points d'évaluation

* Qualité du code
* Robustesse des contrôles

L'aspect visuel ne sera pas évalué, le choix ou non d'un framework d'UI (type Boostrap) est à l'appréciation du candidat.

Il faudra travailler sur la branche master, et créer un commit pour chaque exercice: `git commit -m "Exercice 1: création de la table"`.

Une fois terminé, Il faudra zipper le projet final et l'envoyer par mail à jveillet@macompta.fr et epham@macompta.fr.

## Exercice 1

Créer la table d'écritures, et pouvoir lancer une migration pour générer cette table.

La table d'écritures et composée des champs suivants:

| Nom du champ | Type | description |
|`id` | PRIMARY KEY unsigned NOT NULL AUTO_INCREMENT | Identifiant de la ligne |
|`label` | VARCHAR 255 NOT NULL DEFAULT '' | Libellé de l'écriture |
|`date` | date NOT NULL DEFAULT '0000-00-00' | Date de l'écriture |
|`type` | Enum "C", "D" | Type d'opération "C" => Crédit, "D" => Débit |
|`amount` | double(14,2) NOT NULL DEFAULT 0.00 | Montant |
|`created_at` | timestamp NULL DEFAULT current_timestamp() | Date de création |
|`updated_at` | timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() | Date  de modification |

## Exercice 2

Afficher la page de liste des écritures

Créer une page web qui va accueillir la liste des écritures (vide pour le moment).

Prévoir le tableau qui va afficher les données.

Ajouter un bouton "nouveau" pour permettre de créer une nouvelle écriture.

## Exercice 3

Créer la page de création d'une écriture, avec un formulaire qui va permettre de saisir une écriture.

On doit pouvoir aller sur cette page en cliquant sur le bouton "nouveau" sur la page de la liste des écritures.

* Un champ de saisie pour le libellé
* Une liste de sélection pour le type.
* Un champ de saisie pour le montant
* Un champ pour la date de saisie.

On devra avoir 2 boutons à la fin du formulaire:

* "Enregistrer": enregistre les données et retour à la liste des écritures.
* "Annuler": n'enregistre pas et retour à la liste des écritures.

**Contraintes de validation:**

Le montant ne doit pas être négatif.
la date saisie doit être une date valide.

Si les contraintes de validation ne sont pas respectée, afficher une message d'erreur à l'enregistrement.

## Exercice 4

Modifier la page d'accueuil pour qu'elle affiche les écritures enregistrées.

Afficher un bouton de modification et de suppression pour chaque ligne.

## Exercice 5

Créer la page qui permet de modifier une écriture.

On doit pouvoir aller sur cette page en cliquant sur l'action "modifier" d'une ligne dans la liste des écritures.

On doit pouvoir changer le montant, le libellé et la date.

On doit avoir 2 boutons sur cette page:

* "Enregistrer": enregistre la fiche et retour à la liste des écritures.
* "Annuler": n'enregistre pas et retour à la liste des écritures.

**Contraintes de validation:**

Les même contraintes de validation s'applique que dans la création.

## Exercice 6

Dans la liste des écritures, quand on clique sur l'action de suppression d'une ligne, cela devra supprimer l'écriture et raffraichir la liste.

## Exercice 7

Ajouter un bouton de tri sur l'UI de la liste des écritures pour pouvoir trier par dates (croissantes).
Même chose pour le type d'opérations ("C" et "D").

Exemple d'affichage trié:

| ecriture 1 | 2021-01-01 | 100.0  | 'C'
| ecriture 2 | 2021-01-01 | 25.0   | 'D'
| ecriture 3 | 2021-01-02 | 12.0   | 'C'
| ecriture 4 | 2021-01-03 | 5000.0 | 'C'

etc..

## Bonus

Si le temps le permet voici deux exercices supplémentaires, il ne rentreront pas dans l'évaluation.

### Exercice 8

Afficher 3 totaux en fin de liste des écritures:

* "Total": total global des montants des écritures.
* "Total Crédit": total des montants des écritures en crédit (type "C").
* "Total Débit": total des montants des écritures en débit (type "D").

### Exercice 9

Si le montant total en crédit et différent du montant total en débit, afficher le total global en rouge.
