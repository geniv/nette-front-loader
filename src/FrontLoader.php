<?php

namespace FrontLoader;

use Exception;
use Nette\Application\UI\Control;


/**
 * Class FrontLoader
 *
 * @author  geniv, inspired by Petr Gräf
 * @package FrontLoader
 */
class FrontLoader extends Control
{
    /** @var array */
    protected $parameters;

    /** @var string */
    protected $templatePath;

    /** @var array */
    protected $files;

    protected $type;
//TODO __call() !!! like configurator!


    /**
     * FrontLoader constructor.
     *
     * @param array $parameters
     * @throws Exception
     */
    public function __construct(array $parameters)
    {
        parent::__construct();

        // pokud parametr table neexistuje
        if (!isset($parameters['dir'])) {
            throw new Exception('Parameters dir is not defined in configure! (dir: wwwDir)');
        }


//        $parameters['productionMode']
//        $parameters['tagDev']
//        $parameters['tagProd']
//        $parameters['extJs']
//        $parameters['extCss']

        $this->parameters = $parameters;
    }


    /**
     * Set template path.
     *
     * @param string $path
     * @return $this
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
        return $this;
    }


    public function __call($name, $args)
    {
        if (!in_array($name, ['onAnchor'])) {   // except onAnchor
            $method = strtolower(substr($name, 6)); // nacteni jmena

            dump($this->parameters[$method]);

//            if (!isset($args[0])) {
//                throw new Exception('Nebyl zadany parametr identu.');
//            }
        }
    }


    public function render($source)
    {
        // is exist source in files
        if (!array_key_exists($source, $this->files)) {
            throw new Exception('Parameters css/js is not defined in configure! (table: xy)');
        }

        if (!is_array($this->data[$source]['files'])) {
//            throw new InvalidArgumentException($this->data[$source] . ' - files is not array.');
        }

        foreach ($this->data[$source]['files'] as $file) {

            if (pathinfo($file, PATHINFO_EXTENSION) === self::PATH_EXTENSION) {
                $filePath = $this->parameters['dir'] . '/' . $file;
                if (file_exists($filePath)) {
                    $this->addToTemplateData($filePath, $file);
                } else {
                    $this->sendFileNotFoundException($file);
                }
            } else {
                $extension = $this->isProduction ? '.min.' . self::PATH_EXTENSION : '.' . self::PATH_EXTENSION;
                $filePath = $this->wwwDir . '/' . $file . $extension;

                $extension2 = !$this->isProduction ? '.min.' . self::PATH_EXTENSION : '.' . self::PATH_EXTENSION;
                $filePath2 = $this->wwwDir . '/' . $file . $extension;

                if (file_exists($filePath)) {
//                    $this->addToTemplateData($filePath, $file . $extension);
                } elseif (file_exists($filePath2)) {
//                    $this->addToTemplateData($filePath2, $file . $extension2);
                } else {
//                    $this->sendFileNotFoundException($file);
                }
            }
        }

        $this->template->data = $this->templateData;
        $this->template->setFile($this->templatePath);
        $this->template->render();
    }
}
