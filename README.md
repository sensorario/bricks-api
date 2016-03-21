# bricks-api

```bash
./bin/loadfixtures
./bin/runserver
```

## php.ini configuration

Due to a [php 5.6 bug](http://stackoverflow.com/questions/26261001/warning-about-http-raw-post-data-being-deprecated),  some warning could appear in log. To solve this little issue uncomment this line in php.ini

```bash
;always_populate_raw_post_data = -1
```
