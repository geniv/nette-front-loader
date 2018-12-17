<?php declare(strict_types=1);

namespace FrontLoader;


/**
 * Interface IFrontLoader
 *
 * @author  geniv
 * @package FrontLoader
 */
interface IFrontLoader
{

    /**
     * Get files.
     *
     * Use in Panel::getPanel().
     *
     * @return array
     */
    public function getFiles(): array;


    /**
     * Get vendor files.
     *
     * Use in Panel::getPanel().
     *
     * @return array
     */
    public function getVendorFiles(): array;


    /**
     * Get vendor output files.
     *
     * Use in Panel::getPanel().
     *
     * @return array
     */
    public function getVendorOutputFiles(): array;
}
