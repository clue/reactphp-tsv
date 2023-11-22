# clue/reactphp-tsv

Streaming TSV (Tab-Separated Values) parser and encoder for [ReactPHP](https://reactphp.org/).

**Table of contents**

* [Support us](#support-us)
* [Quickstart example](#quickstart-example)
* [TSV format](#tsv-format)
* [Usage](#usage)
    * [TsvDecoder](#tsvdecoder)
    * [TsvEncoder](#tsvencoder)
* [Install](#install)
* [Tests](#tests)
* [License](#license)
* [More](#more)

## Support us

[![A clue·access project](https://raw.githubusercontent.com/clue-access/clue-access/main/clue-access.png)](https://github.com/clue-access/clue-access)

*This project is currently under active development,
you're looking at a temporary placeholder repository.*

The code is available in early access to my sponsors here: https://github.com/clue-access/reactphp-tsv

Do you sponsor me on GitHub? Thank you for supporting sustainable open-source, you're awesome! ❤️ Have fun with the code! 🎉

Seeing a 404 (Not Found)? Sounds like you're not in the early access group. Consider becoming a [sponsor on GitHub](https://github.com/sponsors/clue) for early access. Check out [clue·access](https://github.com/clue-access/clue-access) for more details.

This way, more people get a chance to take a look at the code before the public release.

## Quickstart example

TSV (Tab-Separated Values) is a very simple text-based format for storing a
large number of (uniform) records, such as a list of temperature records or log
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

## TSV format

TSV (Tab-Separated Values) is a very simple text-based format for storing a
large number of (uniform) records, such as a list of temparature records or log
entries.

```
name    birthday    ip
Alice   2017-01-01  1.1.1.1
Carol   2006-01-01  2.1.1.1
Dave    1995-01-01  3.1.1.1
```

While this may look somewhat trivial, this simplicity comes at a price. TSV is
limited to untyped, two-dimensional data, so there's no standard way of storing
any nested structures or to differentiate a boolean value from a string or
integer.

While TSV may look somewhat similar to CSV (Comma-Separated Values or less
commonly Character-Separated Values), it is more than just a small variation.

* TSV always uses a tab stop (`\t`) as a delimiter between fields, CSV uses a
  comma (`,`) by default, but some applications use variations such as a
  semicolon (`;`) or other application-dependent characters (this is
  particularly common for systems in Europe (and elsewhere) that use a comma as
  decimal separator).
* TSV always uses field names in the first row, CSV allows for optional field
  names (which is application-dependent).
* TSV always uses the same number of fields for all rows, CSV allows for rows
  with different number of fields (though this is rarely used).
* CSV requires quoting
* CSV supports newlines and thus requires more advanced parsing rules
* MIME type CSV is text/csv and for TSV text/tab-separated-values.
* TSV is defined in a [simple document](https://www.iana.org/assignments/media-types/text/tab-separated-values),
  while CSV is defined in a dedicated [RFC 4180](https://tools.ietf.org/html/rfc4180).
  However many applications started using some CSV-variant long before this
  standard was defined, so parsing rules differ somewhat between implementations.

TSV files are commonly limited to only ASCII characters for best interoperability.
However, many legacy TSV files often use ISO 8859-1 encoding or some other
variant. Newer TSV files are usually best saved as UTF-8 and may thus also
contain special characters from the Unicode range. The text-encoding is usually
application-dependent, so your best bet would be to convert to (or assume) UTF-8
consistently.

Despite its shortcomings, TSV is widely used and this is unlikely to change any
time soon. In particular, TSV is a very common export format for a lot of tools
to interface with spreadsheet processors (such as Excel, Calc etc.). This means
that TSV is often used for historical reasons and using TSV to store structured
application data is usually not a good idea nowadays – but exporting to TSV for
known applications continues to be a very reasonable approach.

As an alternative, if you want to process structured data in a more modern
JSON-based format, you may want to use [clue/reactphp-ndjson](https://github.com/clue/reactphp-ndjson)
to process newline-delimited JSON (NDJSON) files (`.ndjson` file extension).

```json
{"name":"Alice","age":30,"comment":"Yes, I like cheese"}
{"name":"Bob","age":50,"comment":"Hello\nWorld!"}
```

## Usage

### TsvDecoder

The `TsvDecoder` (parser) class can be used to make sure you only get back
complete, valid TSV elements when reading from a stream.
It wraps a given
[`ReadableStreamInterface`](https://github.com/reactphp/stream#readablestreaminterface)
and exposes its data through the same interface, but emits the TSV elements
as parsed values instead of just chunks of strings:

```
name    age
Alice   20
Carol   30
```

```php
$stdin = new React\Stream\ReadableResourceStream(STDIN);
$stream = new Clue\React\Tsv\TsvDecoder($stdin);

$stream->on('data', function ($data) {
    // data is a parsed element from the TSV stream
    // line 1: $data = array('name' => 'Alice', 'age' => '20');
    // line 2: $data = array('name' => 'Carol', 'age' => '30');
    var_dump($data);
});
```

ReactPHP's streams emit chunks of data strings and make no assumption about their lengths.
These chunks do not necessarily represent complete TSV elements, as an
element may be broken up into multiple chunks.
This class reassembles these elements by buffering incomplete ones.

Accordingly, the `TsvDecoder` limits the maximum buffer size (maximum line
length) to avoid buffer overflows due to malformed user input. Usually, there
should be no need to change this value, unless you know you're dealing with some
unreasonably long lines. It accepts an additional argument if you want to change
this from the default of 64 KiB:

```php
$stream = new Clue\React\Tsv\TsvDecoder($stdin, 64 * 1024);
```

If the underlying stream emits an `error` event or the plain stream contains
any data that does not represent a valid TSV stream,
it will emit an `error` event and then `close` the input stream:

```php
$stream->on('error', function (Exception $error) {
    // an error occurred, stream will close next
});
```

If the underlying stream emits an `end` event, it will flush any incomplete
data from the buffer, thus either possibly emitting a final `data` event
followed by an `end` event on success or an `error` event for
incomplete/invalid TSV data as above:

```php
$stream->on('end', function () {
    // stream successfully ended, stream will close next
});
```

If either the underlying stream or the `TsvDecoder` is closed, it will forward
the `close` event:

```php
$stream->on('close', function () {
    // stream closed
    // possibly after an "end" event or due to an "error" event
});
```

The `close(): void` method can be used to explicitly close the `TsvDecoder` and
its underlying stream:

```php
$stream->close();
```

The `pipe(WritableStreamInterface $dest, array $options = array(): WritableStreamInterface`
method can be used to forward all data to the given destination stream.
Please note that the `TsvDecoder` emits decoded/parsed data events, while many
(most?) writable streams expect only data chunks:

```php
$stream->pipe($logger);
```

For more details, see ReactPHP's
[`ReadableStreamInterface`](https://github.com/reactphp/stream#readablestreaminterface).

### TsvEncoder

The `TsvEncoder` (serializer) class can be used to make sure anything you write to
a stream ends up as valid TSV elements in the resulting TSV stream.
It wraps a given
[`WritableStreamInterface`](https://github.com/reactphp/stream#writablestreaminterface)
and accepts its data through the same interface, but handles any data as complete
TSV elements instead of just chunks of strings:

```php
$stdout = new React\Stream\WritableResourceStream(STDOUT);
$stream = new Clue\React\Tsv\TsvEncoder($stdout);

$stream->write(array('name' => 'Alice', 'age' => '20'));
$stream->write(array('name' => 'Carol', 'age' => '30'));
```

```
name    age
Alice   20
Carol   30
```

If the underlying stream emits an `error` event or the given data contains
any data that can not be represented as a valid TSV stream,
it will emit an `error` event and then `close` the input stream:

```php
$stream->on('error', function (Exception $error) {
    // an error occurred, stream will close next
});
```

If either the underlying stream or the `TsvEncoder` is closed, it will forward
the `close` event:

```php
$stream->on('close', function () {
    // stream closed
    // possibly after an "end" event or due to an "error" event
});
```

The `end(mixed $data = null): void` method can be used to optionally emit
any final data and then soft-close the `TsvEncoder` and its underlying stream:

```php
$stream->end();
```

The `close(): void` method can be used to explicitly close the `TsvEncoder` and
its underlying stream:

```php
$stream->close();
```

For more details, see ReactPHP's
[`WritableStreamInterface`](https://github.com/reactphp/stream#writablestreaminterface).

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org/).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This project does not yet follow [SemVer](https://semver.org/).
This will install the latest supported version:

While in [early access](#support-us), you first have to manually change your
`composer.json` to include these lines to access the supporters-only repository:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/clue-access/reactphp-tsv"
        }
    ]
}
```

Then install this package as usual:

```bash
$ composer require clue/reactphp-tsv:dev-main
```

This project aims to run on any platform and thus does not require any PHP
extensions and supports running on legacy PHP 5.3 through current PHP 8+.
It's *highly recommended to use the latest supported PHP version* for this project.

# Tests

To run the test suite, you first need to clone this repo and then install all
dependencies [through Composer](https://getcomposer.org/):

```bash
$ composer install
```

To run the test suite, go to the project root and run:

```bash
$ vendor/bin/phpunit
```

## License

This project is released under the permissive [MIT license](LICENSE).

> Did you know that I offer custom development services and issuing invoices for
  sponsorships of releases and for contributions? Contact me (@clue) for details.

## More

* If you want to learn more about processing streams of data, refer to the documentation of
  the underlying [react/stream](https://github.com/reactphp/stream) component.

* If you want to process a more common text-based format,
  you may want to use [clue/reactphp-csv](https://github.com/clue/reactphp-csv)
  to process Comma-Separated Values (CSV) files (`.csv` file extension).

* If you want to process structured data in a more modern JSON-based format,
  you may want to use [clue/reactphp-ndjson](https://github.com/clue/reactphp-ndjson)
  to process newline-delimited JSON (NDJSON) files (`.ndjson` file extension).

* If you want to process compressed TSV files (`.tsv.gz` file extension)
  you may want to use [clue/reactphp-zlib](https://github.com/clue/reactphp-zlib)
  on the compressed input stream before passing the decompressed stream to the TSV decoder.

* If you want to create compressed TSV files (`.tsv.gz` file extension)
  you may want to use [clue/reactphp-zlib](https://github.com/clue/reactphp-zlib)
  on the resulting TSV encoder output stream before passing the compressed
  stream to the file output stream.
