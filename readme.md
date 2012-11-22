lessbuilder
===========

Simple less builder command using leafo/lessphp (http://leafo.net/lessphp/).

Install
-------

First clone this repo. Then

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install
    $ chmod a+x less

Usage
-----

###Help

    $ ./less

###Build

    $ ./less build /my/web/file.less
    Building /my/web/file.less into /my/web/file.css [ OK ]

or

    $ ./less build /my/web/file.less --target /my/web/css
    Building /my/web/file.less into /my/web/css/file.css [ OK ]

or

    $ ./less build /my/web/file.less -t /my/web/css/main.css
    Building /my/web/file.less into /my/web/css/main.css [ OK ]

Future
------

- build entire directory
- watcher to build automatically