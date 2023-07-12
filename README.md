# Really Easy PHP API

## Structure
    .
    ├── api
    │   ├── v1                          # Api version
    │   │   ├── App                     # Classes
    │   │   │   ├── Auth.php            # Authentication class
    │   │   │   ├── autoload.php        # Require all files
    │   │   │   │── Config.php          # Config class for the App
    │   │   │   ├── Dao.php             # Data access object
    │   │   │   │── Endpoint.php        # This class contains the endpoint logic
    │   │   │   │── FsDs.php            # Data source class for file system with json
    │   │   │   └── Router.php          # This class contains the router logic
    │   │   ├── Endpoints               # Directory with the endpoints
    │   │   │   └── Demo.php            # Example endpoint Demo for demo.json
    │   │   └── index.php               # This is the entry point for the API
    ├── data                            # Directory for the File System data source files
    │   ├── schema                      # Directory for the schemas
    │   │   └── demo.json               # Example schema for endpoint Demo
    │   └── demo.json                   # Example data for endpoint Demo
    ├── index.php                       # Empty file
    ├── LICENSE                         # License file
    └── README.md                       # This file

## Setup

* Tested with PHP 7.4, 8.0, 8.1
* Download zip file and copy to the webserver
* Add new endpoints as described below
* Delete the example endpoint "api\v1\Endpoints\Demo.php"

## Authorisation

There is a simple authorisation built in. 
The authorisation is a simple check for an token. 
The tokens can be found in the file "data\auth.json".

## Adding endpoints

Endpoints go in the directory "api/v1/Endpoints".
The name of the file will be the endpoint name.
The public methods in the class will be your actions.
The http request methods get, post, put and delete have a default action defined.

* copy "api\v1\Endpoints\Demo.php" to a new file
  * file name starts with an upper case, e.g. "Demo.php"
  * class name must have the same name as the file
* create a schema file in "data\schema" directory, see "data\schema\demo.json" for an example
* create a data file in "data" directory, see "data\demo.json" for an example

## Use the following syntax for the api

https://localhost/api/v1/?path={endpoint}/{action}/{id0}/{id1}/..

Demo can be found in: https://localhost/demo/index.php

## Default actions, if no action given

* GET method > get action is executed
* POST method -> post action is executed; triggers put action
* PUT method -> put action is executed
* DELETE method -> delete action is executed
