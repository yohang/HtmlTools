<?php

namespace HtmlTools;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\CssSelector\CssSelectorConverter;

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
    public static function addHeadingsId($html, $headingSelector = 'h1, h2, h3, h4, h5, h6', $addLink = false)
    {
        if (!$html) {
            return '';
        }
        $document = new \DOMDocument();
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new \DOMXPath($document);

        // Search heading tags
        $ids = array();
        foreach ($xpath->query(self::cssToXPath($headingSelector)) as $node) {
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
            if ($addLink) {
                $link = $document->createElement('a', '#');
                $link->setAttribute('href', '#'.$node->getAttribute('id'));
                $link->setAttribute('class', 'anchor');
                $node->appendChild($link);
            }
        }

        // Remove \DomDocument's extra tags (doctype, html, body). Yeah, that's (a bit) ugly.
        return preg_replace('#.*<html><body>(.*)</body></html>.*#is', '\1', $document->saveHTML());
    }

    /**
     * Build a Table Of Content
     *
     * @static
     * @param string $html html string
     * @return array
     */
    public static function buildTOC($html)
    {
        if (!$html) {
            return array();
        }

        $html = static::addHeadingsId($html, 'h1, h2, h3, h4, h5, h6');

        $document = new \DOMDocument();
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new \DOMXPath($document);

        $toc = array();
        $h1 = $h2 = $h3 = $h4 = $h5 = $h5 = $h6 = 0;
        foreach ($xpath->query(self::cssToXPath('h1, h2, h3, h4, h5, h6')) as $node) {
            $nodeName = $node->nodeName;
            $title = $node->nodeValue;
            $id = $node->getAttribute('id');
            switch ($nodeName) {
                case 'h1':
                    $toc[++$h1] = array('title' => $title, 'id' => $id, 'children' => array());
                break;

                case 'h2':
                    $toc = self::fixToc($toc, $nodeName, $h1, $h2, $h3, $h4, $h5, $h5);
                    $toc[$h1]['children'][++$h2] = array('title' => $title, 'id' => $id, 'children' => array());
                break;

                case 'h3':
                    $toc = self::fixToc($toc, $nodeName, $h1, $h2, $h3, $h4, $h5, $h5);
                    $toc[$h1]['children'][$h2]['children'][++$h3] = array('title' => $title, 'id' => $id, 'children' => array());
                break;

                case 'h4':
                    $toc = self::fixToc($toc, $nodeName, $h1, $h2, $h3, $h4, $h5, $h5);
                    $toc[$h1]['children'][$h2]['children'][$h3]['children'][++$h4] = array('title' => $title, 'id' => $id, 'children' => array());
                break;

                case 'h5':
                    $toc = self::fixToc($toc, $nodeName, $h1, $h2, $h3, $h4, $h5, $h5);
                    $toc[$h1]['children'][$h2]['children'][$h3]['children'][$h4]['children'][++$h5] = array('title' => $title, 'id' => $id, 'children' => array());
                break;

                case 'h6':
                    $toc = self::fixToc($toc, $nodeName, $h1, $h2, $h3, $h4, $h5, $h5);
                    $toc[$h1]['children'][$h2]['children'][$h3]['children'][$h4]['children'][$h5]['children'][++$h6] = array('title' => $title, 'id' => $id, 'children' => array());
                break;
            }
        }

        return $toc;
    }

    private static function fixToc($toc, $level, $h1, $h2, $h3, $h4, $h5)
    {
        if (!isset($toc[$h1])) {
            $toc[$h1] = array('title' => null, 'id' => null, 'children' => array());
        }
        if ('h2' == $level) {
            return $toc;
        }

        if (!isset($toc[$h1]['children'][$h2])) {
            $toc[$h1]['children'][$h2] = array('title' => null, 'id' => null, 'children' => array());
        }
        if ('h3' == $level) {
            return $toc;
        }

        if (!isset($toc[$h1]['children'][$h2]['children'][$h3])) {
            $toc[$h1]['children'][$h2]['children'][$h3] = array('title' => null, 'id' => null, 'children' => array());
        }
        if ('h4' == $level) {
            return $toc;
        }

        if (!isset($toc[$h1]['children'][$h2]['children'][$h3]['children'][$h4])) {
            $toc[$h1]['children'][$h2]['children'][$h3]['children'][$h4] = array('title' => null, 'id' => null, 'children' => array());
        }
        if ('h5' == $level) {
            return $toc;
        }

        if (!isset($toc[$h1]['children'][$h2]['children'][$h3]['children'][$h4]['children'][$h5])) {
            $toc[$h1]['children'][$h2]['children'][$h3]['children'][$h4]['children'][$h5] = array('title' => null, 'id' => null, 'children' => array());
        }

        return $toc;
    }

    /**
     * @param string $css
     *
     * @return string
     */
    private static function cssToXPath($css)
    {
        if (class_exists('Symfony\Component\CssSelector\CssSelector')) {
            return CssSelector::toXPath($css);
        }

        return (new CssSelectorConverter)->toXPath($css);
    }
}

