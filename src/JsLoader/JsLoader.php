<?php

namespace FrontLoader\JsLoader;

use FrontLoader\FrontLoader;
use Nette\FileNotFoundException;
use Nette\InvalidArgumentException;


/**
 * Class JsLoader
 *
 * @author  geniv
 * @package FrontLoader\JsLoader
 */
class JsLoader extends FrontLoader
{

    /**
     * JsLoader constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);

        $this->files = $parameters['js'];
        $this->type ='js';
        $this->templatePath = __DIR__ . '/JsLoader.latte';
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
