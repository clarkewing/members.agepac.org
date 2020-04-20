# AGEPAC Members area

This is the forum that will be put into production in a members only area on agepac.org.

### What is the AGEPAC?
-----
The [AGEPAC](http://www.agepac.org "View the AGEPAC website") is a non-profit association representing [EPL](http://www.enac.fr/en/pilot-training)'s (Airline Pilot Degree students) from the [ENAC](http://www.enac.fr "View the ENAC website") (French National Civil Aviation Academy) whose primary objectives are as follows:

- To develop and maintain a network of members
- To promote the ENAC's EPL curriculum on the international civil aviation stage
- To work in cooperation with any entity on topics relating to aeronautics
- To assist members encountering personal or professional difficulties during training

<sub><sup>The AGEPAC is registered in France as an _Association Loi de 1901_ and appearing in the _Journal Officiel_ with RNA number W313017548.</sup></sub>

## Installation

### Step 1.

 > To run this project, you must have PHP 7.2 or higher installed as a prerequisite.

 Begin by cloning this repository to your machine, and installing all Composer dependencies.

 ```bash
 git clone git@github.com:clarkewing/agepac.org-members.git
 cd agepac.org-members && composer install
 php artisan key:generate
 mv .env.example .env
 ```

 ### Step 2.

 Next, create a new database and reference its name and username/password within the project's `.env` file. In the example below, we've named the database, "agepac."

 ```
 DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=agepac
 DB_USERNAME=root
 DB_PASSWORD=
 ```

 Then, migrate your database to create tables.

 ```
 php artisan migrate
 ```

 ### Step 3.

 Until an administration portal is available, manually insert any number of "channels" (think of these as forum categories) into the "channels" table in your database.

 Once finished, clear your server cache, and you're all set to go!

 ```
 php artisan cache:clear
 ```

 ### Step 4.

 Use your forum! Visit `http://agepac.test/threads` to create a new account and publish your first thread.
