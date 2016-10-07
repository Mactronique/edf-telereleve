# edf-telereleve
Programme PHP pour lire le télérelevé

# Install

Clone the project or download the tarball.

Open terminal and execute :

```
$ php composer.phar install --no-dev -o
```

If you want use InfluxDB storage, exetute this command

```
$ php composer.phar require influxdb/influxdb-php:^1.4
```

# Usage

In the terminal :

```
$ ./telereleve
```

# Tests


Open terminal and execute :

```
$ php composer.phar install -o
$ ./run-unit
```
