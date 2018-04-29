<?php declare(strict_types=1);

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
    public function getTab(): string
    {
        return '<span title="Front loader">' .
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 896 896" width="16" height="16"><path d="M704 384c-47.625 0-88.625 26.312-110.625 64.906C587.625 448.5 582 448 576 448c-131.062 0-255.438-99.844-300.812-223.438C302.469 201.094 320 166.719 320 128 320 57.344 262.656 0 192 0S64 57.344 64 128c0 47.219 25.844 88.062 64 110.281V657.75c-38.156 22.188-64 63-64 110.25 0 70.625 57.344 128 128 128s128-57.375 128-128c0-47.25-25.844-88.062-64-110.25V427.469C340.156 516.5 456.375 576 576 576c6.375 0 11.625-.438 17.375-.625C615.5 613.812 656.5 640 704 640c70.625 0 128-57.375 128-128 0-70.656-57.375-128-128-128zM192 832c-35.312 0-64-28.625-64-64 0-35.312 28.688-64 64-64 35.406 0 64 28.688 64 64 0 35.375-28.594 64-64 64zm0-640c-35.312 0-64-28.594-64-64s28.688-64 64-64c35.406 0 64 28.594 64 64s-28.594 64-64 64zm512 384c-35.312 0-64-28.625-64-64 0-35.406 28.688-64 64-64 35.375 0 64 28.594 64 64 0 35.375-28.625 64-64 64z"/></svg>' .
            'Front loader' .
            '</span>';
    }


    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel(): string
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
