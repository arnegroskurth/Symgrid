
# Symgrid (pre-alpha)

Symgrid is a Symfony data grid bundle.

![screenshot](https://github.com/arnegroskurth/Symgrid/blob/master/Resources/doc/screenshots/example.png?raw=true)

## Features

- Supports Doctrine Entities (Entity), QueryBuilder (QueryBuilder) and Array (Array) data sources
- Built in column types: Alias, Bool, Currency, Date, DateTime, Numeric and String
- Custom column types definable via Callbacks
- Multiple values collapsible to single column (e.g. comma-separated)
- Automatic column type detection
- Exportable to CSV, HTML, Excel and PDF
- Mass actions
- Row actions
- Generic filters on built in column types
- Column aggregations respecting filters
- Automatic Ajax refresh on filter(s) change
- Row ordering
- Pagination
- JavaScript API for registration of event listeners
- Translation for all labels, column, mass- and row-action titles


## Installation ##

### Step 1: Download Symgrid using Composer

Symgrid is available on [Packagist](https://packagist.org/packages/arne-groskurth/symgrid) and can therefore be installed via Composer:

```bash
$ composer require arne-groskurth/symgrid
```

### Step 2: Enable the bundle

The SymgridBundle along with the FontAwesomeBundle needs to be registered on the kernel:

```php
// app/AppKernel.php

public function registerBundles() {

    $bundles = array(
        // ...
        new ArneGroskurth\Symgrid\ArneGroskurthSymgridBundle(),
        new Bmatzner\FontAwesomeBundle\BmatznerFontAwesomeBundle()
    );
}
```

### Step 3: Install assets

```bash
$ php app/console assets:install --symlink
```

Two stylesheets are available afterwards:
- layout.css: Functional stylings taking care e.g. of the visibility of the loading indicator
- style.css: Bundled grid style as seen on the screenshot above

It is recommended to include both styles and modify them in the applications stylings:

```html
<link rel="stylesheet" href="{{ asset('bundles/arnegroskurthsymgrid/layout.css') }}" />
<link rel="stylesheet" href="{{ asset('bundles/arnegroskurthsymgrid/style.css') }}" />
```

### Done!

## Getting started

After the installation Symgrid is available as a service and can be configured e.g. inside a controller.

### Basic entity-based grid configuration in controller:
```php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use ArneGroskurth\Symgrid\Grid\DataSource\EntityDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function testAction() {

        // Construct grid, set an entity as data source and activate bundled grid style
        $grid = $this->get('arnegroskurth_symgrid.grid')
            ->from(User::class)
            ->useDefaultStyle()
        ;

        // Return grid response or fall back to given template
        return $grid->getResponse('view.html.twig', array(
            'grid' => $grid
        ));
    }
}
```

### Symgrid rendering in Twig template
```twig
{{ symgrid(grid) }}
```

## Documentation

See the [table of contents](https://github.com/arnegroskurth/Symgrid/blob/master/Resources/doc/toc.md).

## License ##

The MIT License (MIT)

Copyright (c) 2016 Arne Groskurth

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.