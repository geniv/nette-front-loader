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
    css:    # files without extension - dev = .css / prod = .min.css
        - css/styles
    js:     # files without extension - dev = .js / prod = .min.js
        - js/script
```

neon configure extension:
```neon
extensions:
    frontLoader: FrontLoader\Bridges\Nette\Extension
```


/**
 * @var CssControl
 * @inject
 */
public $cssControl;

/**
 * @var JsControl
 * @inject
 */
public $jsControl;


/**
 * @return CssControl
 */
protected function createComponentCssLoader(CssLoader $cssLoader)
{
    return $this->cssControl;
}

/**
 * @return JsControl
 */
protected function createComponentJsLoader(JsLoader $jsLoader)
{
    return $this->jsControl;
}

@layout.latte
{block head}
    {control cssControl, 'front'}
{/block}

{block scripts}
    {control jsControl, 'front'}
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
