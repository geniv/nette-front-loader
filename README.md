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

internal dependency:
```json
"nette/nette": ">=2.4.0"
```

Include in application
----------------------
neon configure:
```neon
# front loader
frontLoader:
    productionMode: true
    dir: %wwwDir%
    css:    # files without extension
        - css/global
        front:  # source
            - css/styles1
            - css/styles2
    js:     # files without extension
        - js/global
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
{block frontLoaderCss}
    {include parent}
{/block}

{block content}
...
{/block}

{block frontLoaderJs}
    {include parent}
{/block}
```
