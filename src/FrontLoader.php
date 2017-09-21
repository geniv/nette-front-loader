<?php

namespace FrontLoader;

use Exception;
use Nette\Application\UI\Control;
use Tracy\ILogger;


/**
 * Class FrontLoader
 *
 * @author  geniv, inspired by Petr Gräf
 * @package FrontLoader
 */
class FrontLoader extends Control
{
    /** @var array */
    private $parameters;
    /** @var null|ILogger */
    private $logger;
    /** @var array */
    private $files = [];


    /**
     * FrontLoader constructor.
     *
     * @param array        $parameters
     * @param ILogger|null $logger
     * @throws Exception
     */
    public function __construct(array $parameters, ILogger $logger = null)
    {
        parent::__construct();

        // pokud parametr table neexistuje
        if (!isset($parameters['dir'])) {
            throw new Exception('Parameters dir is not defined in configure! (dir: %wwwDir%)');
        }

        $this->parameters = $parameters;
        $this->logger = $logger;
    }


    /**
     * Select valid files.
     *
     * @param $files
     * @param $type
     * @return array
     */
    private function processFiles($files, $type)
    {
        $parameters = $this->parameters;
        $path = $parameters['dir'] . '/';
        // separe last path
        $dir = basename($path);

        // process array
        return array_map(function ($item) use ($type, $parameters, $dir, $path) {
            $name = $item . ($parameters['productionMode'] ? $parameters['tagProd'] : $parameters['tagDev']) . $type;

            if (substr($name, 0, 4) == 'http') {    // detect url
                return $item;
            } else if (file_exists($path . $name)) {    // detect file
                return $dir . '/' . $name . '?mt=' . filemtime($path . $name);
            } else {
                if ($this->logger && $parameters['productionMode']) {
                    $this->logger->log('File: "' . $path . $name . '" does not exist!', ILogger::WARNING);
                }
                echo '<!-- file ' . $name . ' not exist! -->';
            }
        }, $files);
    }


    /**
     * Render valid files.
     *
     * @param $files
     * @param $type
     * @return string
     */
    private function renderFiles($files, $type)
    {
        switch ($type) {
            case 'css':
                $format = '<link rel="stylesheet" href="%s">';
                break;

            case 'js':
                $format = '<script type="text/javascript" src="%s"></script>';
                break;
        }

        // process files
        return implode(PHP_EOL, array_map(function ($item) use ($format) {
            return sprintf($format, $item);
        }, $files));
    }


    /**
     * Magic method.
     *
     * @param $name
     * @param $args
     * @return mixed|void
     */
    public function __call($name, $args)
    {
        // if not onAnchor
        if (!in_array($name, ['onAnchor'])) {
            // load type
            $type = strtolower(substr($name, 6));
            // if type exist
            if (isset($this->parameters[$type])) {
                // load files of type
                $typeFiles = $this->parameters[$type];

                // global files
                $globalTypeFiles = [];
                array_walk($typeFiles, function ($item, $key) use (&$globalTypeFiles) {
                    if (is_int($key)) {
                        $globalTypeFiles[] = $item;
                    }
                });
                $globalFiles = $this->processFiles($globalTypeFiles, $type);

                // source files
                $files = [];
                if (isset($args[0])) {
                    $source = $args[0];
                    if (isset($typeFiles[$source])) {
                        $files = $this->processFiles($typeFiles[$source], $type);
                    }
                }

                // merge global+source, filter null and select unique files
                $files = array_unique(array_filter(array_merge($globalFiles, $files)));
                // transfer for tracy
                $this->files[$type] = $files;

                echo $this->renderFiles($files, $type);
            }
        }
    }


    /**
     * Get files for tracy.
     *
     * Use in Panel::getPanel().
     *
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }
}
