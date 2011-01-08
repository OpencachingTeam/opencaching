
This readme will assist new developers setting up their own development environment including a database.

Server
Install wamp/lamp

Database
Create a new database (in this readme called ocxx). Easiest done through pmpmyadmin.
cd to directory doc/sql/tables
concat all files to one
cat *.sql > create_all.txt
From phpmyadmin import this into database ocxx.

Apache
Edit your httpd.conf and set the documentroot to point to the htdocs directory in the source.
Optional for a development environment install mod_rewrite.

php
Config php to enable mcrypt, mbstring.
Switch on display_errors for development.

Code
Copy htdocs/config2/settings-sample.inc.php to settings.inc.php
Modify the settings.inc.php to your database, your db user, password. The domain and mail also needs to be set.
Cookie name must also be set otherwise you cannot login.

Run
Open localhost (or localhost:8080) from your browser.
Register a new user. If you have configured mails, then you will get a mail with activation code.
If not, open localhost/activation.php and enter the activation code that you can see in the user table with phpmyadmin.

Test
Use another browser (like Firefox and IE at the same time). Use them with different user accounts logged in.



























