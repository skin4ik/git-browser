Git Browser
=======================

Requirements
------------
LAMP

Introduction
------------
Git browser based on zend framework 2

Installation
------------

Clone the repository and manually invoke `composer` using the shipped
`composer.phar`:

    cd my/project/dir
    git clone git://github.com/skin4ik/git-browser
    cd git-browser
    php composer.phar self-update
    php composer.phar install

Restore from SQL dump (my/project/dir/git-browser/db/dump.sql)

Go to my/project/dir/git-browser/config/autoload/ and copy file local.php.dist with name local.php.
Set DB parameters in this file and file global.php.

Thats all. Project must be working.
