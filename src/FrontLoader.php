<?php

namespace FrontLoader;

use Nette\Application\UI\Control;


/**
 * Class FrontLoader
 *
 * @package FrontLoader
 */
class FrontLoader extends Control
{

    public function __construct(array $parameters)
    {
        parent::__construct();

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


    public function render($source)
    {
        if (!array_key_exists($source, $this->data)) {
            throw new InvalidArgumentException('$source "' . $source . '" not exist.');
        }

        if (!array_key_exists('files', $this->data[$source])) {
            throw new InvalidArgumentException($this->data[$source] . ' - missing files config.');
        }

        if (!is_array($this->data[$source]['files'])) {
            throw new InvalidArgumentException($this->data[$source] . ' - files is not array.');
        }

        foreach ($this->data[$source]['files'] as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === self::PATH_EXTENSION) {
                $filePath = $this->wwwDir . '/' . $file;
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
                    $this->addToTemplateData($filePath, $file . $extension);
                } elseif (file_exists($filePath2)) {
                    $this->addToTemplateData($filePath2, $file . $extension2);
                } else {
                    $this->sendFileNotFoundException($file);
                }
            }
        }

        $this->template->data = $this->templateData;
        $this->template->setFile($this->templatePath);
        $this->template->render();
    }
}
