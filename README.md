# bricks-api

This is a little api REST that aims to expose some methods to save shops and lego set for a private collection. All data are stored in simple text files with serializated php object.

## usages

First of all, load fixtures, and then run server. This api run inside PHP built-in web server. Just run the script `runserver` to see it in your browser.


```bash
./bin/loadfixtures
./bin/runserver
```

## php.ini configuration

Due to a [php 5.6 bug](http://stackoverflow.com/questions/26261001/warning-about-http-raw-post-data-being-deprecated),  some warning could appear in log. To solve this little issue uncomment this line in php.ini

```bash
;always_populate_raw_post_data = -1
```
