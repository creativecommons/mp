# mp

Model Platform


## Overview

:warning: **Please only run Model Platform locally.** The code has been written
to be robust against user error, but not attack.

Model platform has the following dependencies:

- php5
- php5-gd
- php5-sqlite

You can run Model Platform as follows:

```shell
cd mp/src
php -S localhost:6789
```

Then connect to: http://localhost:6789/ in your web browser to interact with
Model Platform.


## Creative Commons Hosted Documentation

The `doc/platform-toolkit.html` file is manually copied to
`/var/www/html/platform/toolkit/index.html` on the `faq` server.
