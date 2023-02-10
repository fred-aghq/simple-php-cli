# Installation
## Configuring the application
The toolkit can be configured via env files passed into the container from docker compose.
Any of these vars will be available via PHP's `getenv()`

1. copy `config/.env.example` to `config/app.env`.

> Possible `APP_ENV` values: `dev`, `production`

## Usage
Out of the box, the toolkit is configured for production mode -  if not developing or altering a command's code, it
can be run as-is.

1.`docker compose run --rm app list`

## Developing/Testing Commands 

The override file provides composer and other dependency containers, and is configured to bind-mount the application 
code for instant feedback to code changes.

Any other containers required for development/test usage should be defined in the override file, and committed to 
the override.yml.dist file.

1. `cp ./docker-compose.override.yml.dist ./docker-compose.override.yml`
2. `docker compose run composer install`
3. You should see the vendor directory appear on your host machine within `./app`


# Creating a Command
https://symfony.com/doc/current/console.html#creating-a-command

> Keep in mind that this uses the *component* and isn't a full symfony/flex app.

# Using Composer
The composer container is available in the `docker-compose.override.yml` file. 

Using the correct version of composer to manage this application's dependencies can be done like so:

```shell
docker compose run --rm composer 
```
