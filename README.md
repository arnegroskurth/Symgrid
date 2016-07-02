
# Symgrid #

Symgrid is a Symfony data grid bundle inspired by the [APYDataGridBundle](https://github.com/APY/APYDataGridBundle).

## Features

(List includes some features that are not implemented yet)

- Supports Doctrine Entities (Entity), QueryBuilder (QueryBuilder) and Array (Array) data sources
- Built in column types: Alias, Bool, Currency, Date, DateTime, Numeric and String
- Custom column types definable via Callbacks
- Automatic column type detection
- Exportable to CSV, HTML, Excel and PDF
- Mass actions
- REST API on data source
- Generic filters on built in column types
- Automatic Ajax refresh on filter(s) change
- Row ordering
- Pagination
- Customizable twig templates
- Row actions


## Installation ##

### Step 1: Download Symgrid using Composer

Symgrid is available on [Packagist](https://packagist.org/packages/arne-groskurth/symgrid) and can therefore be installed via Composer:

```bash
composer require arne-groskurth/symgrid
```

### Step 2: Enable the bundle

The Symgrid bundle needs to be registered as Symfony bundle:

```php
<?php
// app/AppKernel.php

public function registerBundles() {

    $bundles = array(
        // ...
        new ArneGroskurth\Symgrid\ArneGroskurthSymgridBundle()
    );
}
```

### Done!

## License ##

The MIT License (MIT)

Copyright (c) 2016 Arne Groskurth

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.