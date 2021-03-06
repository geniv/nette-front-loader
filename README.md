Front loader
============
for CSS &amp; JS loader

Installation
------------
```sh
$ composer require geniv/nette-front-loader
```
or
```json
"geniv/nette-front-loader": "^1.2"
```

require:
```json
"php": ">=7.0",
"nette/application": ">=2.4",
"nette/utils": ">=2.4",
"latte/latte": ">=2.4",
"tracy/tracy": ">=2.4"
```

Include in application
----------------------
neon configure:
```neon
# front loader
frontLoader:
#   debugger: false
#   productionMode: true
#   developmentMode: false
    dir: %wwwDir%
    css:	# files without extension
        - css/global
        - static:css/global1
        - "https://fonts.googleapis.com"
        - "//fonts.googleapis.com"
        front:  # source
            - css/styles1
            - static:css/styles2
    js:		# files without extension
        - js/global
        - static:js/global1
        - "https://cdnjs.com"
        - "//cdnjs.com"
        front:  # source
            - js/script1
            - static:js/script2
    tagDev: '.'
    tagProd: '.min.'
    envDev: 'development'
    envProd: 'production'
    modifyTimeVar: 'mt'
```

`if productionMode is not defined or set null then loader will be automatic detect production mode`

`developmet` or `stage` environment is development (`tagDev`) settings (full css).

`production` environment is production (`tagProd`) settings (minimal css).

`compile` block work only `developmentMode`, other accept: `productionMode`.

Setting for load css/js file without change environment name begin keyword: `static:` like `http`.

#### each mode states:
- **development**:
    - _productionMode: false_
    - _developmentMode: true_

- **stage**:
    - _productionMode: false_
    - _developmentMode: false_

- **production**:
    - _productionMode: true_
    - _developmentMode: false_

neon configure extension:
```neon
extensions:
    frontLoader: FrontLoader\Bridges\Nette\Extension
```

usage:
```php
use FrontLoader\IFrontLoader;

protected function createComponentFrontLoader(IFrontLoader $frontLoader): IFrontLoader
{
    //$frontLoader->setFormat('css', '        <link rel="stylesheet" href="%s">');
    //$frontLoader->setFormat('js', '    <script type="text/javascript" src="%s"></script>');
    return $frontLoader;
}
```

@layout.latte
#### for CSS
```latte
{block head}
    {control frontLoader:css}
{/block}
```
or defined source
```latte
{block head}
    {control frontLoader:css 'front'}
{/block}
```

#### for JS
```latte
{block scripts}
    {control frontLoader:js}
{/block}
```
or defined source
```latte
{block scripts}
    {control frontLoader:js 'front'}
{/block}
```

other presenter *.latte:
```latte
{block title}...{/block}
{block description}...{/block}
{block slug}...{/block}

{block content}
...
{/block}

{block scripts}
    {include parent}
    <script src="source.js"></script>
{/block}

{block head}
    {include parent}
    <link rel="stylesheet" href="source.css">
{/block}
```
