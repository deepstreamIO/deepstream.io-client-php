# deepstream.io-client-php
PHP Client using the dsh HTTP API

# Installing & running tests
Running the tests is a bit trickier as it requires a two-node deepstream cluster, consisting of a Websocket deepstream with a test-provider that answers RPCs and listens for events and a HTTP deepstream that the actual tests are run against:

![Diagram](diagram.png)

- Install PHP - you can get it from e.g. [http://windows.php.net/download/](http://windows.php.net/download/) for windows
- Add the folder with the executables (e.g. php.exe, php-cli.exe) to your path
- Download PHP Unit from [https://phpunit.de/](https://phpunit.de/)
- Move the `phpunit-6.2.1.phar` file to your `deepstream.io-client-php` folder
- Make it executable via 
```bash
chmod +x phpunit.phar
```
- Download a local version of Redis and run it on its default port
- Download the latest deepstream version and unzip it
- run `git clone git@github.com:deepstreamIO/dsx-connection-http.git` in its lib directory
- install the plugin via `yarn install`
- copy the configs in `ds-conf` into your deepstream's conf directory
- install the redis msg connector using `./deepstream.exe install msg redis`
- start two deepstream instances with 
 ```bash
 ./deepstream.exe start -c conf/config-http.yml
 ```
and
 ```bash
 ./deepstream.exe start -c conf/config-ws.yml
 ```
- install the test provider in the `deepstream.io-client-php`
```bash
cd test-provider
yarn install
```
- run the test provider
```bash
node test-provider.js
```
- run the tests using
```
php phpunit-6.2.1.phar --bootstrap src\deepstream-client.php test\client-test.php
```

If it all works it looks like this
![Screenshot](screenshot.png)