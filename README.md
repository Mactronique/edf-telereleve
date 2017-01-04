# edf-telereleve
Programme PHP pour lire le télérelevé

# Install

Clone the project or download the tarball.

Open terminal and execute :

```bash
$ php composer.phar install --no-dev -o
```

If you want use InfluxDB storage, exetute this command

```bash
$ php composer.phar require influxdb/influxdb-php:^1.4
```

# Configuration

Make the configuration file into the destination folder.

```bash
$ touch config.yml
```

## Set the serial device for your Electric Counter

The `compteur` key is the model of your electric counter. Only `CBEMM` and `CBETM` supported now.

The `device` key is the path to the serial device socket.

```yaml
compteur: CBEMM
device: /dev/ttyAMA0
```

By default, the storage is SQLite into `data.sqlite` file.

# Usage

In the terminal :

```bash
$ ./telereleve
```

# Tests


Open terminal and execute :

```bash
$ php composer.phar install -o
$ ./run-unit
```

# Configuration definition

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
                    port: 3386
                    database: telereleve
        skip_on_storage_error: false # if true, no error stop the save process. If one storage is on error, the error is ignored.
enable_email: false # By default, the email sending is disabled.
template: default.text.twig # The name file for default template for email body content.
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
