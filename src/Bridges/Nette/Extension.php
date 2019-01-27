<?php declare(strict_types=1);

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
        'debugger'        => true,
        'productionMode'  => null,   // default null => automatic mode
        'developmentMode' => null,   // default null => automatic mode
        'dir'             => null,
        'css'             => [],
        'js'              => [],
        'tagDev'          => '.',
        'tagProd'         => '.min.',
        'envDev'          => 'development',   // environment production
        'envProd'         => 'production',   // environment production
        'modifyTimeVar'   => 'mt',
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
            $config['productionMode'] = $builder->parameters['environment'] == $config['envProd'];  // automatic detect production environment
        }

        // if is set then manual set value
        if (!isset($config['developmentMode'])) {
            $config['developmentMode'] = $builder->parameters['environment'] == $config['envDev'];  // automatic detect development environment
        }

        // definition loader
        $builder->addDefinition($this->prefix('default'))
            ->setFactory(FrontLoader::class, [$config]);

        // define panel
        if ($config['debugger']) {
            $panel = $builder->addDefinition($this->prefix('panel'))
                ->setFactory(Panel::class);

            // linked panel to tracy
            $builder->getDefinition('tracy.bar')
                ->addSetup('addPanel', [$panel]);
        }
    }
}
