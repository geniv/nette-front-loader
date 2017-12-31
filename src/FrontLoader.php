<?php

namespace FrontLoader;

use Exception;
use Nette\Application\UI\Control;
use Nette\Utils\Finder;
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
    /** @var array */
    private $vendorFiles = [];
    /** @var array */
    private $vendorOutputFiles = [];


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
            throw new Exception('Parameter dir is not defined in configure! (dir: %wwwDir%)');
        }

        if ($parameters['compile']) {
            if (!isset($parameters['compile']['inputDir']) || !isset($parameters['compile']['outputFileScss']) || !isset($parameters['compile']['outputFileJs'])) {
                throw new Exception('Parameter inputDir|outputFileScss|outputFileJs is not defined in configure!');
            }
        }

        $this->parameters = $parameters;
        $this->logger = $logger;
    }


    /**
     * Get file path.
     *
     * @param $dir
     * @param $file
     * @return bool|string
     */
    private function getFilePath($dir, $file)
    {
        return substr(realpath($file), strlen(realpath($dir . '/..')) + 1);
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

        if (!$parameters['productionMode'] && $parameters['compile']) {
            // compile on case debug||stage + define compile block
            switch ($type) {
                case 'css':
                    $scss = '// vendor files scss' . PHP_EOL;
                    foreach (Finder::findFiles('*.scss')->from($parameters['compile']['inputDir']) as $file) {
                        if (isset($parameters['compile']['exclude']) ? !in_array(basename($file), $parameters['compile']['exclude']) : true) {
                            $name = $this->getFilePath($parameters['dir'], $file);
                            $scss .= PHP_EOL . PHP_EOL . PHP_EOL . '// source file: ' . $name . PHP_EOL;
                            $scss .= file_get_contents($file);
                            $this->vendorFiles[$type][] = $name;
                        }
                    }

                    if (file_put_contents($parameters['compile']['outputFileScss'], $scss) && chmod($parameters['compile']['outputFileScss'], 0777)) {
                        $this->vendorOutputFiles[$type] = $this->getFilePath($parameters['dir'], $parameters['compile']['outputFileScss']);
                    }
                    break;

                case 'js':
                    $js = '// vendor files js' . PHP_EOL;
                    foreach (Finder::findFiles('*.js')->from($parameters['compile']['inputDir']) as $file) {
                        if (isset($parameters['compile']['exclude']) ? !in_array(basename($file), $parameters['compile']['exclude']) : true) {
                            $name = $this->getFilePath($parameters['dir'], $file);
                            $js .= PHP_EOL . PHP_EOL . PHP_EOL . '// source file: ' . $name . PHP_EOL;
                            $js .= file_get_contents($file);
                            $this->vendorFiles[$type][] = $name;
                        }
                    }

                    if (file_put_contents($parameters['compile']['outputFileJs'], $js) && chmod($parameters['compile']['outputFileJs'], 0777)) {
                        $this->vendorOutputFiles[$type] = $this->getFilePath($parameters['dir'], $parameters['compile']['outputFileJs']);
                    }
                    break;
            }
        }

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
                echo '<!-- file ' . $name . ' not exist! -->' . PHP_EOL;
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
     * Get files.
     *
     * Use in Panel::getPanel().
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }


    /**
     * Get vendor files.
     *
     * Use in Panel::getPanel().
     *
     * @return array
     */
    public function getVendorFiles()
    {
        return $this->vendorFiles;
    }


    /**
     * Get vendor output files.
     *
     * Use in Panel::getPanel().
     *
     * @return array
     */
    public function getVendorOutputFiles()
    {
        return $this->vendorOutputFiles;
    }
}
