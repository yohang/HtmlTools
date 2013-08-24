<?php

namespace HtmlTools\Tests;

use HtmlTools\Helpers;

/**
 *
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class HelpersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider headingDataProvider
     */
    public function testAddHeadingsId($expected, $string)
    {
        $this->assertSame($expected, Helpers::addHeadingsId($string));
    }

    public function headingDataProvider()
    {
        return array(
            array('<h1 id="test">Test</h1>', '<h1>Test</h1>'),
            array('<h1 id="test">Test</h1><h2 class="foo" id="bar">bar</h2>', '<h1>Test</h1><h2 class="foo">bar</h2>'),
            array('<h1 id="test">Test</h1><h1 id="test-2">Test</h1><h1 id="test-3">Test</h1>', '<h1>Test</h1><h1>Test</h1><h1>Test</h1>'),
            array('', ''),
            array('<p>test</p>', '<p>test</p>'),
            array('<ul><li>foo</li><li>bar</li></ul>', '<ul><li>foo<li>bar</ul>'),
        );
    }
}
