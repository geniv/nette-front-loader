<?php

namespace FrontLoader\JsLoader;

use FrontLoader\FrontLoader;
use Nette\FileNotFoundException;
use Nette\InvalidArgumentException;


/**
 * Class JsLoader
 *
 * @package FrontLoader\JsLoader
 */
class JsLoader extends FrontLoader
{
    const PATH_EXTENSION = 'js';

    /**
     * @var string
     */
    private $wwwDir;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var bool
     */
    private $isProduction;

    /**
     * @var array
     */
    private $templateData = array();


    /**
     * JsLoader constructor.
     *
     * @param string $wwwDir
     * @param array  $data
     * @param string $environment
     */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);


        $this->wwwDir = $wwwDir;
        $this->data = $data;

        $this->isProduction = false;
        if ($environment === 'production') {
            $this->isProduction = true;
        }

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
