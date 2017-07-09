Front loader
============
for CSS &amp; JS loader

```sh
$ composer require geniv/nette-front-loader
```
or
```json
"geniv/nette-front-loader": ">=1.0"
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
```

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
```latte
{block frontLoaderCss}
    {control frontLoader:css}
    or
    {control frontLoader:css, 'front'}
{/block}

{block frontLoaderJs}
    {control frontLoader:js}
    or
    {control frontLoader:js, 'front'}
{/block}
```

presenter *.latte:
```latte
{block content}
...
{/block}

{block frontLoaderCss}
    {include parent}
    <link rel="stylesheet" href="source.css">
{/block}

{block frontLoaderJs}
    {include parent}
    <script src="source.js"></script>
{/block}
```
