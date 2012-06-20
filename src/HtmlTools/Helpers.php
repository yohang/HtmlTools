<?php

namespace HtmlTools;

use Symfony\Component\CssSelector\CssSelector;

class Helpers
{
    /**
     * Add ids to headings tags based on their content
     *
     * @static
     * @param string $html html string
     * @param string $headingSelector CSS selector
     * @return mixed modified
     */
    public static function addHeadingsId($html, $headingSelector = 'h1, h2, h3, h4, h5, h6')
    {
        $document = new \DOMDocument();
        $document->loadHTML($html);
        $xpath = new \DOMXPath($document);
        $ids = array();

        // Search heading tags
        foreach ($xpath->query(CssSelector::toXPath($headingSelector)) as $node) {
            // If they don't have an id, find an unique one
            if (!$node->hasAttribute('id')) {
                $baseId = $id = Inflector::urlize($node->textContent);
                $i  = 1;
                while (in_array($id, $ids)) {
                    $id = $baseId . '-' . ($i++);
                }
                $ids[] = $id;
                $node->setAttribute('id', $id);
            } else {
                $ids[] = $node->getAttribute('id');
            }
        }

        // Remove \DomDocument's extra tags (doctype, html, body). Yeah, that's (a bit) ugly.
        return preg_replace('#.*<html><body>(.*)</body></html>.*#is', '\1', $document->saveHTML());
    }
}
