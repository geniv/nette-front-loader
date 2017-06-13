<?php

namespace FrontLoader\CssLoader;

use FrontLoader\FrontLoader;
use Nette\Application\UI\Control;
use Nette\FileNotFoundException;
use Nette\InvalidArgumentException;


/**
 * Class CssLoader
 *
 * @author  geniv
 * @package FrontLoader\CssLoader
 */
class CssLoader extends FrontLoader
{

    /**
     * CssLoader constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);

        $this->files = $parameters['css'];
        $this->templatePath = __DIR__ . '/CssLoader.latte';
    }


    /**
     * @param string $filePath
     * @param string $file
     */
    private function addToTemplateData($filePath, $file)
    {
        $version = filemtime($filePath);
        $this->templateData[] = $file . '?v=' . $version;
    }


    /**
     * @param $file
     *
     * @throws FileNotFoundException
     */
    private function sendFileNotFoundException($file)
    {
        if (!$this->isProduction) {
            throw new FileNotFoundException('File ' . $file . ' not found.');
        }
    }
}
