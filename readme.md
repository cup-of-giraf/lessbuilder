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

###Build directories

The finder is recursive.

    $ ./less build ./web/css/
    Building /my/web/main.less into /my/web/css/main.css [ OK ]
    Building /my/web/backend/main.less into /my/web/css/backend/main.css [ OK ]

or

    $ ./less build ./less --target ./web/css

###Watcher

Option `--watch` (or `-w`) enable the watcher to build automatically your .less files each time you save them.
The watcher is based on check files every 2 seconds.
You can change the interval time with `--watch-interval` (or `-i`) option.


    $ ./less build -w ./web

or

    $ ./less build -w -i3 ./web/css/main.less

Future
------

- Keep the subpath for directories build using target
