<?php

namespace HtmlTools\Extension\Twig;

use HtmlTools\Helpers;

class HtmlToolsExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'add_headings_id' => new \Twig_Filter_Method($this, array($this, 'addHeadingsId'), array('is_safe' => array('html'))),
        );
    }

    public function addHeadingsId($html)
    {
        return Helpers::addHeadingsId($html);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'html_tools';
    }
}
