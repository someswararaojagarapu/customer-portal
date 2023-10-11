# customer-portal

PROJECT SETUP

Welcome to the Code Assignment project! This repository contains the codebase for assessment

## Requirements

- PHP Version: 8.1.8
- Symfony Version: 6.3.5
- React
- Redis Cache

## Getting Started

Follow these steps to get the project up and running on your local machine.

### Clone the Repository

git clone --branch main https://github.com/someswararaojagarapu/customer-portal.git

## Install Dependencies
Use Composer to install the required dependencies.

composer install

up and run redis-server.exe (Please note, without redis server up APIs won't work)

run another terminal : yarn encore dev --watch

## Start the Symfony Server
Launch the Symfony server to run the application.

symfony server:start

## Running PHPUnit Tests
PHPUnit is used for testing the application. Run the following command to execute the tests.

Please note Currently Unit tests are WIP

./vendor/bin/phpunit

## APIs
We can see all the APIs in Swagger

API URL:

1. https://localhost:8000/api/server/filter/list

Method : GET

This API will return all filter related json

2. https://localhost:8000/api/server/information/list

Method : POST
Request Payload :

{
"storage": "0 to 5000",
"ram": ["8GB","16GB"],
"hardDiskType": "SATA",
"location": "AmsterdamAMS-01"
}

This API will return after applying filters, will return server information.