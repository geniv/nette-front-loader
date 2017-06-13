Front loader
============
for CSS &amp; JS loader

Include in application
----------------------
neon configure:
```neon
frontLoader:
    productionMode: true
    dir: %wwwDir%
    css:    # files without extension
#        - css/global
        front:  # source
            - css/styles1
            - css/styles2
#            - css/styles2.less
    js:     # files without extension
#        - js/global
        front:  # source
            - js/script1
            - js/script2
#            async:
#                - js/async-script
#    json:
#        - json/global
#        front:
#            - json/file
#            - http://google.cz/api/file.json
#            dev:
#                - http://google.cz/api/file-dev.json
#            prod:
#                - http://google.cz/api/file-prod.json
    tagDev: ''
    tagProd: '.min.'
    extJs: js
    extCss: css
```

neon configure extension:
```neon
extensions:
    frontLoader: FrontLoader\Bridges\Nette\Extension
```

/** @var FrontLoader @inject */
public $jsControl;

/**
 * @return FrontLoader
 */
protected function createComponentFrontLoader(FrontLoader $frontLoader)
{
    return $this->frontLoader;
}

@layout.latte
{block head}
    {control frontLoader:css, 'front'}
{/block}

{block scripts}
    {control frontLoader:js, 'front'}
{/block}

presenter *.latte:
{*
{block head}
    {include parent}
{/block}
*}

{block content}
...
{/block}

{*
{block scripts}
    {include parent}
{/block}
*}
