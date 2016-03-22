# bricks-api

 - clone project
 - install vendors

```bash
git clone git@github.com:sensorario/bricks-api
cd bricks-api
php composer.phar install
```

 - run server

```bash
./bin/runserver
```

 - load fixtures

```bash
./bin/loadfixtures
```

- [open api from your browser](http://localhost:8080/api/v1/homepage/)

## php.ini configuration

Due to a [php 5.6 bug](http://stackoverflow.com/questions/26261001/warning-about-http-raw-post-data-being-deprecated),  some warning could appear in log. To solve this little issue uncomment this line in php.ini

```bash
;always_populate_raw_post_data = -1
```
