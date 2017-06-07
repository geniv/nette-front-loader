<?php

namespace FrontLoader\Bridges\Nette;

use FrontLoader\Bridges\Tracy\Panel;
use FrontLoader\CssLoader\CssLoader;
use FrontLoader\JsLoader\JsLoader;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * nette extension pro zavadeni jazykove sluzby jako rozsireni
 *
 * @author  geniv
 * @package Translator\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array vychozi hodnoty */
    private $defaults = [
        'source' => 'DevNull',
        'table'  => null,
        'path'   => null,
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

        // definice css loaderu
        $builder->addDefinition($this->prefix('loader.css'))
            ->setClass(CssLoader::class,[$config]);

        // definice js loaderu
        $builder->addDefinition($this->prefix('loader.js'))
            ->setClass(JsLoader::class,[$config]);

        // definice panelu
//        $builder->addDefinition($this->prefix('panel'))
//            ->setClass(Panel::class);
    }


    /**
     * Before Compile.
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
//TODO ma cenu na to montovat panel?
        // pripojeni panelu do tracy
//        $builder->getDefinition($this->prefix('default'))
//            ->addSetup('?->register(?)', [$this->prefix('@panel'), '@self']);
    }
}
