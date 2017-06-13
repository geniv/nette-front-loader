<?php

namespace FrontLoader\Bridges\Nette;

use FrontLoader\Bridges\Tracy\Panel;
//use FrontLoader\CssLoader\CssLoader;
use FrontLoader\FrontLoader;
//use FrontLoader\JsLoader\JsLoader;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * nette extension pro zavadeni front-loader sluzby jako rozsireni
 *
 * @author  geniv
 * @package FrontLoader\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array vychozi hodnoty */
    private $defaults = [
        'productionMode' => false,
        'dir'            => null,
        'css'            => [],
        'js'             => [],
        'tagDev'         => '',
        'tagProd'        => '.min.',
        'extJs'          => 'js',
        'extCss'         => 'css',
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        // vlozeni detekce produkcniho modu, pokud neni definovano
        if (!isset($config['productionMode'])) {
            $config['productionMode'] = $builder->parameters['productionMode'];
        }

        // definition loader
        $builder->addDefinition($this->prefix('default'))
            ->setClass(FrontLoader::class, [$config]);

        // definice css loaderu
//        $builder->addDefinition($this->prefix('loader.css'))
//            ->setClass(CssLoader::class, [$config]);

        // definice js loaderu
//        $builder->addDefinition($this->prefix('loader.js'))
//            ->setClass(JsLoader::class, [$config]);

        // definice panelu
        $builder->addDefinition($this->prefix('panel'))
            ->setClass(Panel::class);
    }


    /**
     * Before Compile.
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        // pripojeni panelu do tracy
        $builder->getDefinition($this->prefix('default'))
            ->addSetup('?->register(?)', [$this->prefix('@panel'), '@self']);
    }
}
