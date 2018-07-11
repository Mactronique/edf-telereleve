# edf-telereleve

Programme PHP pour lire les informations client du compteur électrique.

[English version](README-en.md)

# Pré-requis 

* PHP 7.0 ou plus récent
* [composer](https://getcomposer.org)
* Le port série du compteur électrique connecté à votre ordinateur.

# Connect the Serial Port

Le compteur électrique dispose d'un port série pour le télé-releve client. 
Vous pouvez connecter ce port série à votre ordinateur avec une petite carte.

Ce schéma de la petite carte est décrit dans la [documentation ERDF](doc/ERDF-NOI-CPT_02F.pdf).

Vous devez acheter les composants et la construire avant de poursuivre l'installation de ce programme.

# Installation

Cloner le projet ou télécharger l'archive.

Une fois réalisé, ouvrir un terminal et se placer dans le dossier du projet, puis exécuter la commande suivante :

```bash
$ php composer.phar install --no-dev -o
```

Si vous compter stocker les données dans une base InfluxDB, exécuter la commande suivante dans le terminal :

```bash
$ php composer.phar require influxdb/influxdb-php
```

# Configuration

Ajouter le fichier de configuration vide en exécutant la commande suivante :

```bash
$ touch config.yml
```

## Définir la configuration du port série

La clé `compteur` est le model du compteur électrique.
Seulement les modèles `CBEMM` et `CBETM` sont actuellement supporté.
Pour un particulier le modèle électronique blanc est le modèle `CBEMM`.
Ce modèle est également compatible avec le compteur communiquant Linky.

La clé `device` est le chemin vers le port série de votre machine sur lequel est connecté le compteur.
Sur linux, la commande `ls /dev/tty*` permet de lister les ports séries disponible.

Par exemple sur un Raspberry Pi 3 le port série du GPIO est `/dev/ttyS0`.

Voici un exemple d'une configuration à placer dans le fichier `config.yml` à la racine du projet.

```yaml
compteur: CBEMM
device: /dev/ttyAMA0
```

Par défaut les données sont stocké dans une base SQLite nommé `data.sqlite` et placé à la racine du projet.

# Utilisation

Dans le terminal exécuter la commande suivante :

```bash
$ ./telereleve
```

# Exécution des tests

Ouvrir un terminal et se placer à la racine du projet. Puis exécuter les commandes suivantes :

```bash
$ php composer.phar install -o
$ ./run-unit
```

La première install les outils de développement du projet et la seconde exécuter les tests.

# Configuration complète

Voci toutes les clés de configuration possible :

```yaml
compteur: CBEMM #this value is by default
device: /dev/ttyAMA0 # this value is the GPIO serial port for the Raspberry Pi
storage:
    driver: Sqlite # This is the default value. Another storage supported is 'InfluxDb'.
    parameters: # This is the default value. This constains arbitrary array configuration key for the driver.
        path: datas.sqlite
# Parameters array for the InfluxDB driver :
        host: localhost
        port: 8086
        database: telereleve
# Parameters array for Chain storage driver :
    driver: Chain
    parameters:
        storages: # set all storage here. You can set many storage with same driver 
            sqlite: # the key for driver. Is used on error. This array contains the driver configuration.
                driver: Sqlite
                parameters:
                    path: datas.sqlite
            influx:
                driver: InfluxDb
                parameters:
                    host: localhost
                    port: 8086
                    database: telereleve
        skip_on_storage_error: false # if true, no error stop the save process. If one storage is on error, the error is ignored.
enable_email: false # By default, the email sending is disabled.
template: default.text.twig # The name file for default template for email body content.
log_file: telereleve.log # The file log
smtp:
    server: 127.0.0.1
    port: 25
    security: null 
    username: null
    password: null
    mime: text/plain
    from: 
        display_name: TeleReleve
        email: me@localhost
    to: 
        display_name: Me
        email: me@localhost
```
