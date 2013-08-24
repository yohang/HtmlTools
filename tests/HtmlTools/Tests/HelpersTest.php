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
    public function testAddHeadingsId($data, $pattern)
    {
        $this->assertRegExp($pattern, Helpers::addHeadingsId($data));
    }

    public function headingDataProvider()
    {
        return array(
            array('<h1>Test</h1>', '#^<h1 id="test-[0-9a-f]+">Test</h1>$#'),
            array('<h1>Test</h1><h2 class="foo">bar</h2> ', '#^<h1 id="test-[0-9a-f]+">Test</h1><h2 class="foo" id="bar-[0-9a-f]+">bar</h2>$#'),
            array('', '#^$#'),
            array('<p>test', '#^<p>test</p>$#'),
            array('<ul><li>foo<li>bar</ul>', '#^<ul><li>foo</li><li>bar</li></ul>$#')
        );
    }
}
