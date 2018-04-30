<?php declare(strict_types=1);

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
    // type static script
    const
        TYPE_HTTP = 'http',
        TYPE_STATIC = 'static:';

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
     * @param string $dir
     * @param string $file
     * @return string
     */
    private function getFilePath(string $dir, string $file): string
    {
        return substr(realpath($file), strlen(realpath($dir . '/..')) + 1);
    }


    /**
     * Select valid files.
     *
     * @param array  $files
     * @param string $type
     * @return array
     */
    private function processFiles(array $files, string $type): array
    {
        $parameters = $this->parameters;
        $path = $parameters['dir'] . '/';
        // separe last path
        $dir = basename($path);

        if ($parameters['developmentMode'] && $parameters['compile']) {
            // compile on case debug + define compile block
            switch ($type) {
                case 'css':
                    $scss = '// vendor files scss' . PHP_EOL;
                    foreach (Finder::findFiles('*.scss')->from($parameters['compile']['inputDir']) as $file) {
                        if (isset($parameters['compile']['exclude']) ? !in_array(basename($file), $parameters['compile']['exclude']) : true) {
                            $name = $this->getFilePath($parameters['dir'], $file->getPathname());
                            $scss .= PHP_EOL . PHP_EOL . PHP_EOL . '// source file: ' . $name . PHP_EOL;
                            $scss .= file_get_contents($file->getPathname());
                            $this->vendorFiles[$type][] = $name;
                        }
                    }

                    if (file_put_contents($parameters['compile']['outputFileScss'], $scss)) {
                        @chmod($parameters['compile']['outputFileScss'], 0777);
                        $this->vendorOutputFiles[$type] = $this->getFilePath($parameters['dir'], $parameters['compile']['outputFileScss']);
                    }
                    break;

                case 'js':
                    $js = '// vendor files js' . PHP_EOL;
                    foreach (Finder::findFiles('*.js')->from($parameters['compile']['inputDir']) as $file) {
                        if (isset($parameters['compile']['exclude']) ? !in_array(basename($file), $parameters['compile']['exclude']) : true) {
                            $name = $this->getFilePath($parameters['dir'], $file->getPathname());
                            $js .= PHP_EOL . PHP_EOL . PHP_EOL . '// source file: ' . $name . PHP_EOL;
                            $js .= file_get_contents($file->getPathname());
                            $this->vendorFiles[$type][] = $name;
                        }
                    }

                    if (file_put_contents($parameters['compile']['outputFileJs'], $js)) {
                        @chmod($parameters['compile']['outputFileJs'], 0777);
                        $this->vendorOutputFiles[$type] = $this->getFilePath($parameters['dir'], $parameters['compile']['outputFileJs']);
                    }
                    break;
            }
        }

        // process array
        return array_map(function ($item) use ($type, $parameters, $dir, $path) {
            $name = $item . ($parameters['productionMode'] ? $parameters['tagProd'] : $parameters['tagDev']) . $type;
            $staticName = $this->getStaticName($item . '.' . $type);

            if (substr($name, 0, 4) == self::TYPE_HTTP) {
                // detect static http
                return $item;
            } else if (substr($name, 0, 7) == self::TYPE_STATIC && file_exists($path . $staticName)) {
                // detect static file
                return $dir . '/' . $staticName . '?' . $parameters['modifyTimeVar'] . '=' . filemtime($path . $staticName);
            } else if (file_exists($path . $name)) {
                // detect file
                return $dir . '/' . $name . '?' . $parameters['modifyTimeVar'] . '=' . filemtime($path . $name);
            } else {
                // switch static file
                if (substr($name, 0, 7) == self::TYPE_STATIC) {
                    $name = $this->getStaticName($item . '.' . $type);
                }

                if ($this->logger && $parameters['productionMode']) {
                    $this->logger->log('File: "' . $path . $name . '" does not exist!', ILogger::WARNING);
                }
                echo '<!-- file ' . $name . ' not exist! -->' . PHP_EOL;
            }
        }, $files);
    }


    /**
     * Get static name.
     *
     * @param string $name
     * @return string
     */
    private function getStaticName(string $name): string
    {
        return (string) substr($name, 7);
    }


    /**
     * Render valid files.
     *
     * @param array  $files
     * @param string $type
     * @return string
     */
    private function renderFiles(array $files, string $type): string
    {
        $indentation = (is_array($this->parameters['indentation']) ? (isset($this->parameters['indentation'][$type]) ? $this->parameters['indentation'][$type] : '') : $this->parameters['indentation']);
        $format = '';
        switch ($type) {
            case 'css':
                $format = $indentation . '<link rel="stylesheet" href="%s">';
                break;

            case 'js':
                $format = $indentation . '<script type="text/javascript" src="%s"></script>';
                break;
        }

        // process files
        return implode(PHP_EOL, array_map(function ($item) use ($format) {
            return sprintf($format, $item);
        }, $files));
    }


    /**
     * __call.
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

                echo $this->renderFiles($files, $type) . PHP_EOL;
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
    public function getFiles(): array
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
    public function getVendorFiles(): array
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
    public function getVendorOutputFiles(): array
    {
        return $this->vendorOutputFiles;
    }
}
