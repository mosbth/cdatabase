mos/cdatabase
=========
[![Latest Stable Version](https://poser.pugx.org/mos/cdatabase/v/stable.svg)](https://packagist.org/packages/mos/cdatabase) [![Total Downloads](https://poser.pugx.org/mos/cdatabase/downloads.svg)](https://packagist.org/packages/mos/cdatabase) [![Latest Unstable Version](https://poser.pugx.org/mos/cdatabase/v/unstable.svg)](https://packagist.org/packages/mos/cdatabase) [![License](https://poser.pugx.org/mos/cdatabase/license.svg)](https://packagist.org/packages/mos/cdatabase)
[![Build Status](https://travis-ci.org/mosbth/cdatabase.png?branch=master)](https://travis-ci.org/mosbth/cdatabase)
[![Code Coverage](https://scrutinizer-ci.com/g/mosbth/cdatabase/badges/coverage.png?s=f999ab1961684a91050b095682f7ab7a13ccb534)](https://scrutinizer-ci.com/g/mosbth/cdatabase/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/mosbth/cdatabase/badges/quality-score.png?s=1c2fc1af0df7fb7ee1e4f379a81253583a750297)](https://scrutinizer-ci.com/g/mosbth/cdatabase/)

PHP classes for database handling upon PDO.

Read an article about it here ["CDatabase - PHP classes för working with SQL queries and databases"](http://dbwebb.se/opensource/cdatabase)

By Mikael Roos, me@mikaelroos.se.



License
------------------

This software is free software and carries a MIT license.



History
-----------------------------------


v0.1.x (latest)

* Added phpdoc
* Merged #4
* Added unittesting and made it work on Travis.
* Added support for group by clause.
* Reformatted config-files with comments.
* Double check that lastInsertId works.
* Adding debug mode to hide or display connection details when connect() fails.
* Added error reporting to testprograms in webroot.
* Added utility `webroot/check_driver.php` to see if and what PDO drivers are loaded.
* Merged issue #1 from Emil to include limit and offset and made it work with MySQL.


v0.1.0 (2014-04-17)

* Using and modifying class `CDatabase` from Anax-oophp and merging with same class from Lydia.
* Adding trait for `TSQLQueryBuilderBasic`.
* Adding `webroot` with testfiles for SQLite, MySQL and general SQLBuilder.
* Adding `composer.json` and publishing to Packagist as `mos/cdatabase`.



```
 .  
..:  Copyright (c) 2013 - 2014 Mikael Roos, me@mikaelroos.se
```
