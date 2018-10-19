#Football API Exercise
##Installation and [Instructions](#instructions)

Requirements - PHP 7.2

---
###Installation

When you have a local copy of the repository, start by creating a .env file

You can copy the .env.dist file which has suitable default values

Then run

<pre>
composer install
</pre>

You may be prompted to update php modules if some are missing

***
**VERY IMPORTANT!!!!!**

YMMV but had lots of problems getting ORM annotations to work

Before running server - run the following command

<pre>
composer dump-autoload
</pre>

You have been warned....

***
You can check that everything is set up properly by running the tests

<pre>
./bin/phpunit
</pre>

To start using the system in anger..

Build the database..

<pre>
php bin/console doctrine:schema:create
</pre>

There is a small fixture you can load if you want

<pre>
php bin/console doctrine:fixtures:load
</pre>

Then... 

<pre>
php bin/console server:run
</pre>
---
<a name="instructions"></a>
###Instructions

The following instructions assume that you are already familiar with the remit

The repo exposes a very simple REST API - all data is transferred as JSON

The api methods are protected using JWT

Start by nagivating to 

<pre>
/login
</pre>

You should be prompted for a username and password

<pre>
commercialpeople
kerching
</pre>

On successful login, the page responds with the json token

You can use this token either in the url as a "key" field

<pre>
/api/team/?key={jwt}
</pre>

Or include a header called "APIKey" with the token as the value

***
The following methods are available

> **GET - /api/league**

> list all the leagues

> **GET - /api/league/{id}**

> list a specific league

> **GET - /api/team**

> list all the teams

> **GET - /api/team/{id}**

> list a specific team

> **POST - /api/team**

> create a team

> **POST - /api/league**

> create a league

> **PUT - /api/team/{id}**

> update a specific team

> **DELETE - /api/league/{id}**

> remove a specific league

***

####Entity details

The models are very simple and have the following structure

**Team:**

* name: (VARCHAR 255)

* strip: (VARCHAR 255)

* league_id: int or NULL

**League:**

* name: (VARCHAR 255)

All text fields are unique


