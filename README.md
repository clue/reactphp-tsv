# clue/reactphp-tsv

Streaming TSV (Tab-Separated Values) parser and encoder for [ReactPHP](https://reactphp.org/).

## Quickstart example

TSV (Tab-Separated Values) is a very simple text-based format for storing a
large number of (uniform) records, such as a list of temparature records or log
entries.

```
name	birthday	ip
Alice	2017-01-01	1.1.1.1
Carol	2006-01-01	2.1.1.1
Dave	1995-01-01	3.1.1.1
```

Once [installed](#install), you can use the following code to read a TSV stream from `STDIN`:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$stdin = new React\Stream\ReadableResourceStream(STDIN);
$stream = new Clue\React\Tsv\TsvDecoder($stdin);

$stream->on('data', function ($data) {
    echo 'Name ' . $data['name'] . '\'s birthday is ' . $data['birthday'] . PHP_EOL;
});

$stream->on('error', function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});
```

You can now process this example by running this on the command line:

```bash
$ php birthdays.php < users.tsv 
Alice's birthday is 2017-01-01
Carol's birthday is 2006-01-01
Dave's birthday is 1995-01-01
```

## Install

[![A clue·access project](https://raw.githubusercontent.com/clue-access/clue-access/main/clue-access.png)](https://github.com/clue-access/clue-access)

*This project is currently under active development,
you're looking at a temporary placeholder repository.*

The code is available in early access to my sponsors here: https://github.com/clue-access/reactphp-tsv

Do you sponsor me on GitHub? Thank you for supporting sustainable open-source, you're awesome! ❤️ Have fun with the code! 🎉

Seeing a 404 (Not Found)? Sounds like you're not in the early access group. Consider becoming a [sponsor on GitHub](https://github.com/sponsors/clue) for early access. Check out [clue·access](https://github.com/clue-access/clue-access) for more details.

This way, more people get a chance to take a look at the code before the public release.

Rock on 🤘

## License

This project will be released under the permissive [MIT license](LICENSE).

> Did you know that I offer custom development services and issuing invoices for
  sponsorships of releases and for contributions? Contact me (@clue) for details.
