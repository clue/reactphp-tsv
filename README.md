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
$stdin = new React\Stream\ReadableResourceStream(STDIN, $loop);
$stream = new Clue\React\Tsv\Decoder($stdin);

$stream->on('data', function ($data) {
    echo 'Name ' . $data['name'] . '\'s birthday is ' . $data['birthday'] . PHP_EOL;
});
```

You can now process this example by running this on the command line:

```bash
$ php birthdays.php < users.tsv 
Alice's birthday is 2017-01-01
Carol's birthday is 2006-01-01
Dave's birthday is 1995-01-01  3.1.1.1
```

## Install

[![A clue·access project](https://raw.githubusercontent.com/clue-access/clue-access/main/clue-access.png)](https://github.com/clue-access/clue-access)

*This project is currently under active development,
you're looking at a temporary placeholder repository.*

Do you want early access to my unreleased projects?
You can either be patient and wait for general availability or
consider becoming a [sponsor on GitHub](https://github.com/sponsors/clue) for early access.

Do you sponsor me on GitHub? Thank you for supporting sustainable open-source, you're awesome!
The prototype is available here: [https://github.com/clue-access/reactphp-tsv](https://github.com/clue-access/reactphp-tsv).

Support open-source and join [**clue·access**](https://github.com/clue-access/clue-access) ❤️

## License

This project will be released under the permissive [MIT license](LICENSE).

> Did you know that I offer custom development services and issuing invoices for
  sponsorships of releases and for contributions? Contact me (@clue) for details.
