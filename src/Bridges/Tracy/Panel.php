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
        return '<span title="Front loader"><img width="16px" height="16px" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSIxMDI0IiB3aWR0aD0iNzY4IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Ik02NDAgNDQ4Yy00Ny42MjUgMC04OC42MjUgMjYuMzEyLTExMC42MjUgNjQuOTA2QzUyMy42MjUgNTEyLjUgNTE4IDUxMiA1MTIgNTEyYy0xMzEuMDYyIDAtMjU1LjQzOC05OS44NDQtMzAwLjgxMi0yMjMuNDM4QzIzOC40NjkgMjY1LjA5NDAwMDAwMDAwMDA1IDI1NiAyMzAuNzE5MDAwMDAwMDAwMDUgMjU2IDE5MmMwLTcwLjY1Ni01Ny4zNDQtMTI4LTEyOC0xMjhTMCAxMjEuMzQ0MDAwMDAwMDAwMDUgMCAxOTJjMCA0Ny4yMTkgMjUuODQ0IDg4LjA2MiA2NCAxMTAuMjgxVjcyMS43NUMyNS44NDQgNzQzLjkzOCAwIDc4NC43NSAwIDgzMmMwIDcwLjYyNSA1Ny4zNDQgMTI4IDEyOCAxMjhzMTI4LTU3LjM3NSAxMjgtMTI4YzAtNDcuMjUtMjUuODQ0LTg4LjA2Mi02NC0xMTAuMjVWNDkxLjQ2OUMyNzYuMTU2IDU4MC41IDM5Mi4zNzUgNjQwIDUxMiA2NDBjNi4zNzUgMCAxMS42MjUtMC40MzggMTcuMzc1LTAuNjI1QzU1MS41IDY3Ny44MTIgNTkyLjUgNzA0IDY0MCA3MDRjNzAuNjI1IDAgMTI4LTU3LjM3NSAxMjgtMTI4Qzc2OCA1MDUuMzQ0IDcxMC42MjUgNDQ4IDY0MCA0NDh6TTEyOCA4OTZjLTM1LjMxMiAwLTY0LTI4LjYyNS02NC02NCAwLTM1LjMxMiAyOC42ODgtNjQgNjQtNjQgMzUuNDA2IDAgNjQgMjguNjg4IDY0IDY0QzE5MiA4NjcuMzc1IDE2My40MDYgODk2IDEyOCA4OTZ6TTEyOCAyNTZjLTM1LjMxMiAwLTY0LTI4LjU5NC02NC02NHMyOC42ODgtNjQgNjQtNjRjMzUuNDA2IDAgNjQgMjguNTk0IDY0IDY0UzE2My40MDYgMjU2IDEyOCAyNTZ6TTY0MCA2NDBjLTM1LjMxMiAwLTY0LTI4LjYyNS02NC02NCAwLTM1LjQwNiAyOC42ODgtNjQgNjQtNjQgMzUuMzc1IDAgNjQgMjguNTk0IDY0IDY0QzcwNCA2MTEuMzc1IDY3NS4zNzUgNjQwIDY0MCA2NDB6Ii8+PC9zdmc+" />' .
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
