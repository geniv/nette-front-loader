<?php

namespace FrontLoader\Bridges\Tracy;

use FrontLoader\FrontLoader;
use Latte\Engine;
use Nette\DI\Container;
use Nette\SmartObject;
use Tracy\Debugger;
use Tracy\IBarPanel;


/**
 * Class Panel
 *
 * @author  geniv
 * @package FrontLoader\Bridges\Tracy
 */
class Panel implements IBarPanel
{
    use SmartObject;

    /** @var FrontLoader front loader from DI */
    private $frontLoader;
    /** @var Container container from DI */
    private $container;


    /**
     * Panel constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * Register to Tracy.
     *
     * @param FrontLoader $frontLoader
     */
    public function register(FrontLoader $frontLoader)
    {
        $this->frontLoader = $frontLoader;
        Debugger::getBar()->addPanel($this);
    }


    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        return '<span title="Front loader">' .
            '<svg height="1024" viewBox="0 0 768 1024" width="768" xmlns="http://www.w3.org/2000/svg"><path d="M640 448c-47.625 0-88.625 26.312-110.625 64.906C523.625 512.5 518 512 512 512c-131.062 0-255.438-99.844-300.812-223.438C238.469 265.09400000000005 256 230.71900000000005 256 192c0-70.656-57.344-128-128-128S0 121.34400000000005 0 192c0 47.219 25.844 88.062 64 110.281V721.75C25.844 743.938 0 784.75 0 832c0 70.625 57.344 128 128 128s128-57.375 128-128c0-47.25-25.844-88.062-64-110.25V491.469C276.156 580.5 392.375 640 512 640c6.375 0 11.625-0.438 17.375-0.625C551.5 677.812 592.5 704 640 704c70.625 0 128-57.375 128-128C768 505.344 710.625 448 640 448zM128 896c-35.312 0-64-28.625-64-64 0-35.312 28.688-64 64-64 35.406 0 64 28.688 64 64C192 867.375 163.406 896 128 896zM128 256c-35.312 0-64-28.594-64-64s28.688-64 64-64c35.406 0 64 28.594 64 64S163.406 256 128 256zM640 640c-35.312 0-64-28.625-64-64 0-35.406 28.688-64 64-64 35.375 0 64 28.594 64 64C704 611.375 675.375 640 640 640z"/></svg>' .
            'Front loader' .
            '</span>';
    }


    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        $params = [
            'files'             => $this->frontLoader->getFiles(),
            'vendorFiles'       => $this->frontLoader->getVendorFiles(),
            'vendorOutputFiles' => $this->frontLoader->getVendorOutputFiles(),
        ];

        $latte = new Engine;
        return $latte->renderToString(__DIR__ . '/PanelTemplate.latte', $params);
    }
}
