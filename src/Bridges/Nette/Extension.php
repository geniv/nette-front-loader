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
        'envProd'        => 'production',   // environment production
        'modifyTimeVar'  => 'mt',
        'indentation'    => '    ',
        'compile'        => [],
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
            $config['productionMode'] = $builder->parameters['environment'] == $config['envProd'];    // automatic detect production environment
        }

        // definition loader
        $builder->addDefinition($this->prefix('default'))
            ->setFactory(FrontLoader::class, [$config]);

        // definition panel
        $builder->addDefinition($this->prefix('panel'))
            ->setFactory(Panel::class);
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
