# bricks-api

This is a little api REST that aims to expose some methods to save shops and lego set for a private collection. All data are stored in simple text files with serializated php object.

## usages

### add new lego set

curl -H "Content-Type: application/json" -X POST --data '{}' http://localhost:8080/api/v1/set/75105 # millenium falcon;

### add new shop

curl -H "Content-Type: application/json" -X POST --data '{"name":"Toys Center","address":"Piazzale della Cooperazione, 4, 47122 Forlì FC"}' http://localhost:8080/api/v1/shop/

### add new insight

curl -H "Content-Type: application/json" -X POST --data '{"shop":"toys-center","set":"75105","value":"15800"}' http://localhost:8080/api/v1/insight/

## php.ini configuration

Due to a [php 5.6 bug](http://stackoverflow.com/questions/26261001/warning-about-http-raw-post-data-being-deprecated),  some warning could appear in log. To solve this little issue uncomment this line in php.ini

```bash
;always_populate_raw_post_data = -1
```
