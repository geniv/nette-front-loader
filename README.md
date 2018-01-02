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
"geniv/nette-front-loader": ">=1.0.0"
```

require:
```json
"php": ">=5.6.0",
"nette/nette": ">=2.4.0"
```

Include in application
----------------------
neon configure:
```neon
# front loader
frontLoader:
#   debugger: false
#   productionMode: true
    dir: %wwwDir%
    css:    # files without extension
        - css/global
        - "https://fonts.googleapis.com"
        front:  # source
            - css/styles1
            - css/styles2
    js:     # files without extension
        - js/global
        - "https://cdnjs.com"
        front:  # source
            - js/script1
            - js/script2
    tagDev: '.'
    tagProd: '.min.'
    envProd: 'production'
    modifyTimeVar: 'mt'
    indentation: "    "
    compile:
        inputDir: %wwwDir%/../vendor/geniv
        outputFileScss: %wwwDir%/../assets/scss/vendors/composer-components/composer-components.scss
        outputFileJs: %wwwDir%/../assets/js/composer-components.js
        exclude:
            - CookieBar.scss
            - CookieBar.js
```

`if productionMode is not defined or set null then loader will be automatic detect production mode`

`developmet` or `stage` environment is development (`tagDev`) settings 

`production` environment is production (`tagProd`) settings 

neon configure extension:
```neon
extensions:
    frontLoader: FrontLoader\Bridges\Nette\Extension
```

usage:
```php
use FrontLoader\FrontLoader;

protected function createComponentFrontLoader(FrontLoader $frontLoader)
{
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
    {control frontLoader:css, 'front'}
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
    {control frontLoader:js, 'front'}
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
