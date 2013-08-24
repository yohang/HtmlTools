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
        if (!$html) {
            return '';
        }
        $document = new \DOMDocument();
        $document->loadHTML($html);
        $xpath = new \DOMXPath($document);

        // Search heading tags
        $ids = array();
        foreach ($xpath->query(CssSelector::toXPath($headingSelector)) as $node) {
            // If they don't have an id, find an unique one
            if (!$node->hasAttribute('id')) {
                $id = Inflector::urlize($node->textContent);
                if (array_key_exists($id, $ids)) {
                    $ids[$id] += 1;
                    $id .= '-'.$ids[$id];
                } else {
                    $ids[$id] = 1;
                }
                $node->setAttribute('id', $id);
            }
        }

        // Remove \DomDocument's extra tags (doctype, html, body). Yeah, that's (a bit) ugly.
        return preg_replace('#.*<html><body>(.*)</body></html>.*#is', '\1', $document->saveHTML());
    }
}
