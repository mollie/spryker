# Connect the Mollie standalone package for development

## Overview

This is the document outlining how to connect the Mollie spryker standalone project to the Spryker demo project.
## Basic Usage

### Clone the repositories

To have everything set up for development, we need to clone 2 projects. First project is the actual Mollie standalone package that we are developing the logic in, and the second is the Spryker demo project where we are doing acceptance testing, and we are manually testing the needed integration.

### Spryker B2C Marketplace Demo

The project can be downloaded from the Sprykers github repository: 

```php
https://github.com/spryker-shop/b2c-demo-marketplace
```
Install the Spryker project as we usually would following the official README.md instructions.


### Mollie Standalone Package

Mollie package can be downloaded from the repository as separate project:
```php
https://github.com/mollie/spryker
```

Inside of the project, we have a small docker compose that we will use for running the static analyzers and the project.

Start the docker compose in detach mode using command 
```php
docker compose up -d
```
To install all dependencies in the package, run the following command:
```php
docker compose exec php composer install
```
After you do it from within the container, it’s also needed to be triggered from the project itself in order for the git hooks to be properly set. Just run command:
```php
composer install
```

## Connect the project
To actually connect the standalone project with the existing demo project, we need to make some adjustments to the demo project.
In composer.json file, add a new repository with the relative path to the standalone project.

### Example:
```php
 "repositories": [
      ...
        {
            "type": "path",
            "url": "/home/user/projects/Mollie",
            "options": {
                "symlink": true
            }
        }
    ],
```
The symlink option means that all the changes in the connected project, will be automatically reflected in the vendor directory of this demo project.
Adjust the docker/deployment/default/docker-compose.yml file to connect the new volume. It’s not enough just to connect the project. We also need to make the project accessible in the docker container of the demo project. We do that by adding the new volume like: 
```php
x-volumes:
  &app-volumes
  volumes:
      - logs:${SPRYKER_LOG_DIRECTORY}:rw
      - ./:/data
      - /home/user/projects/Mollie:/home/user/projects/Mollie:rw
```

Also, within the same file, we need to adjust the cli service, and add the new file:
```php
  cli:
    image: ${SPRYKER_DOCKER_PREFIX}_run_cli:${SPRYKER_DOCKER_TAG}
    ...
    volumes:
    ...
      - /home/user/projects/Mollie:/home/user/projects/Mollie:rw
```
After these adjustments in docker-compose.yml, its necessary to do docker/sdk up for the changes to take effect.

If you do docker/sdk boot, all the changes done in docker-compose.yml will be deleted.
Within the demo project run the following command to actually install the new package:
```php
docker/sdk cli composer require mollie/spryker-payment
```