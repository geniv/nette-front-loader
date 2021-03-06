<?php declare(strict_types=1);

namespace FrontLoader;

use Exception;
use Nette\Application\UI\Control;
use stdClass;
use Tracy\ILogger;


/**
 * Class FrontLoader
 *
 * @author  geniv, inspired by Petr Gräf
 * @package FrontLoader
 */
class FrontLoader extends Control implements IFrontLoader
{
    // type static script
    const
        TYPE_HTTP = 'http',
        TYPE_HTTP_SHORT = '//',
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
    /** @var array */
    private $formats = [
        'css' => '        <link rel="stylesheet" href="%s">',
        'js'  => '    <script type="text/javascript" src="%s"></script>',
    ];


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

        $this->parameters = $parameters;
        $this->logger = $logger;
    }


    /**
     * Is url.
     *
     * @param string $url
     * @return bool
     */
    public static function isUrl(string $url): bool
    {
        return substr($url, 0, 4) == self::TYPE_HTTP || substr($url, 0, 2) == self::TYPE_HTTP_SHORT;
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

        // process array
        return array_map(function ($item) use ($type, $parameters, $dir, $path) {
            $name = $item . ($parameters['productionMode'] ? $parameters['tagProd'] : $parameters['tagDev']) . $type;
            $staticName = $this->getStaticName($item . '.' . $type);

            if (self::isUrl($name)) {
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
                return '';
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
     * Render valid files.
     *
     * @param array  $files
     * @param string $type
     * @return string
     */
    private function renderFiles(array $files, string $type): string
    {
        /** @var stdClass $baseUrl */
        $baseUrl = ($this->getTemplate()->baseUrl ?? '') . '/';
        // process files
        return implode(PHP_EOL, array_map(function ($item) use ($baseUrl, $type) {
            // insert baseUrl only for file
            return sprintf($this->formats[$type], (self::isUrl($item) ? '' : $baseUrl) . $item);
        }, $files));
    }


    /**
     * Set format.
     *
     * @param string $type
     * @param string $tag
     */
    public function setFormat(string $type, string $tag)
    {
        $this->formats[$type] = $tag;
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
