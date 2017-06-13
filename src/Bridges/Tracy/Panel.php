<?php

namespace FrontLoader\Bridges\Tracy;

use FrontLoader\FrontLoader;
use Latte\Engine;
use Locale\Locale;
use Nette\Application\Application;
use Latte\MacroTokens;
use Latte\Parser;
use Latte\PhpWriter;
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
        return '<span title="Translator"><img width="16px" height="16px" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDEwMCAxMDAiIGhlaWdodD0iMTAwcHgiIHZlcnNpb249IjEuMSIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHdpZHRoPSIxMDBweCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGcgaWQ9IkxheWVyXzEiPjxnPjxwYXRoIGQ9Ik01MC44NTMsMjEuMzc5YzE1LjI3MiwwLjQ1OCwyNy4yODEsMTMuMTAyLDI2LjgyNiwyOC4yNjNjLTAuNDU1LDE1LjE0NC0xMy4yLDI3LjA1NC0yOC40NzMsMjYuNTk2ICAgIGMtMTUuMjU5LTAuNDU4LTI3LjI2OC0xMy4xMTEtMjYuODEzLTI4LjI1NUMyMi44NDgsMzIuODIxLDM1LjU5NCwyMC45MjEsNTAuODUzLDIxLjM3OXoiIGZpbGw9IiNGNTg1MzYiLz48L2c+PGc+PGRlZnM+PHBhdGggZD0iTTc3LjY3OSw0OS42NDJsLTAuMDcxLDIuMzgyYy0wLjQ1NSwxNS4xNDYtMTMuMjAxLDI3LjA1Ni0yOC40NzQsMjYuNTk4ICAgICBDMzMuODc1LDc4LjE2MywyMS44NjcsNjUuNTEsMjIuMzIxLDUwLjM2NGwwLjA3MS0yLjM4MmMtMC40NTQsMTUuMTQ0LDExLjU1NSwyNy43OTcsMjYuODEzLDI4LjI1NSAgICAgQzY0LjQ3OSw3Ni42OTUsNzcuMjI0LDY0Ljc4NSw3Ny42NzksNDkuNjQyeiIgaWQ9IlNWR0lEXzkxXyIvPjwvZGVmcz48Y2xpcFBhdGggaWQ9IlNWR0lEXzJfIi8+PGcgY2xpcC1wYXRoPSJ1cmwoI1NWR0lEXzJfKSIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAgICAiPjxwYXRoIGQ9Ik03Ni4xMjQsNTcuOTM3bC0wLjA3MSwyLjM4NGMtMC41ODgsMS42NjktMS4zMzcsMy4yNTktMi4yMjYsNC43NThsMC4wNzEtMi4zODQgICAgIEM3NC43ODcsNjEuMTk1LDc1LjUzNyw1OS42MDUsNzYuMTI0LDU3LjkzNyIgZmlsbD0iIzlENTAyNSIvPjxwYXRoIGQ9Ik03My44OTgsNjIuNjk0bC0wLjA3MSwyLjM4NGMtNC45NSw4LjM2My0xNC4yMjksMTMuODU2LTI0LjY5MywxMy41NDMgICAgIEMzMy44NzUsNzguMTYzLDIxLjg2Nyw2NS41MSwyMi4zMjEsNTAuMzY0bDAuMDcxLTIuMzgyYy0wLjQ1NCwxNS4xNDQsMTEuNTU1LDI3Ljc5NywyNi44MTMsMjguMjU1ICAgICBDNTkuNjcsNzYuNTUxLDY4Ljk0OCw3MS4wNiw3My44OTgsNjIuNjk0IiBmaWxsPSIjOUQ1MDI1Ii8+PHBhdGggZD0iTTc3LjY3OSw0OS42NDJsLTAuMDcxLDIuMzgyYy0wLjA0LDEuMzMyLTAuMTc1LDIuNjM1LTAuMzk5LDMuOTFsMC4wNzItMi4zODQgICAgIEM3Ny41MDQsNTIuMjc0LDc3LjYzOSw1MC45NzIsNzcuNjc5LDQ5LjY0MiIgZmlsbD0iIzlENTAyNSIvPjxwYXRoIGQ9Ik03Ny4yOCw1My41NWwtMC4wNzIsMi4zODRjLTAuMjY4LDEuNTA4LTAuNjUzLDIuOTc4LTEuMTU1LDQuMzg3bDAuMDcxLTIuMzg0ICAgICBDNzYuNjI2LDU2LjUyNyw3Ny4wMTIsNTUuMDYsNzcuMjgsNTMuNTUiIGZpbGw9IiM5RDUwMjUiLz48L2c+PC9nPjxwYXRoIGQ9Ik02Ny45OTMsMzkuNWgtOXYtMS43OTFsLTIuNzk1LDMuNTI1bC0xLjI4NS0xLjgxNkw0Mi4yMjksNDcuNWgxMC43NjR2LTEuOTIybDYsMi4yNDRWNDQuNWg5djloLTl2LTMuMzIyICAgbC02LDIuMjQ0VjUwLjVINDIuMjMxbDEyLjc5OSw4LjA4MWwxLjE2OC0xLjg3OGwyLjc5NSwzLjU4OFY1OC41aDl2OWgtOXYtMy41OThsLTYuMjE4LTEuNDk2bDAuODUtMS44MDVMMzcuOTkzLDUwLjQ4OFY1Ni41aC0xMCAgIHYtMTVoMTB2Ni4wMWwxNS43NDgtMTAuMTEybC0wLjk2NS0xLjgwNGw2LjIxNy0xLjQ5NlYzMC41aDlWMzkuNXoiIGZpbGw9IiNGRkZGRkYiLz48L2c+PC9zdmc+" />' .
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
//        $locale = $this->container->getByType(Locale::class);   // nacteni lokalizacni sluzby
//        $application = $this->container->getByType(Application::class);    // nacteni aplikace
//        $presenter = $application->getPresenter();  // nacteni presenteru
//
//        $translateMap = new TranslateMap;
//        // vyrazeni prekladu z @layout
//        $layoutLatte = dirname($presenter->template->getFile()) . '/../@layout.latte';
//        $layoutTranslate = (file_exists($layoutLatte) ? $this->extractFile($layoutLatte, $translateMap) : []);
//        // vytazeni prekladu z aktualniho souboru
//        $contentTranslate = ($presenter->template->getFile() ? $this->extractFile($presenter->template->getFile(), $translateMap) : []);
//
//        $params = [
//            // locales
//            'locales'          => $locale->getLocales(),
//            'localeCode'       => $locale->getCode(),
//            // translates
//            'translateLayout'  => $layoutTranslate,
//            'translateContent' => $contentTranslate,
//            'translateClass'   => get_class($this->translator),
//            'translateSearch'  => $this->translator->searchTranslate(array_merge($layoutTranslate, $contentTranslate)),   // vyhledani prekladu v driveru prekladace
//            'translatesMap'    => $translateMap->toArray(), // mapa umisteni prekladu
//        ];
//
//        $latte = new Engine;
//        return $latte->renderToString(__DIR__ . '/PanelTemplate.latte', $params);

        return '...';
    }
}
