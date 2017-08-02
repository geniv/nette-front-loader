<?php

namespace FrontLoader\Bridges\Nette;

use FrontLoader\Bridges\Tracy\Panel;
use FrontLoader\FrontLoader;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package FrontLoader\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'debugger'       => true,
        'productionMode' => null,   // default null => automatic mode
        'dir'            => null,
        'css'            => [],
        'js'             => [],
        'tagDev'         => '.',
        'tagProd'        => '.min.',
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        // if is set then manual set value
        if (!isset($config['productionMode'])) {
            $config['productionMode'] = $builder->parameters['productionMode'];
        }

        // definition loader
        $builder->addDefinition($this->prefix('default'))
            ->setClass(FrontLoader::class, [$config]);

        // definition panel
        $builder->addDefinition($this->prefix('panel'))
            ->setClass(Panel::class);
    }


    /**
     * Before Compile.
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        if ($config['debugger']) {
            // add tracy panel
            $builder->getDefinition($this->prefix('default'))
                ->addSetup('?->register(?)', [$this->prefix('@panel'), '@self']);
        }
    }
}
