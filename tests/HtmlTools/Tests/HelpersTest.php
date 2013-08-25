<?php

namespace HtmlTools\Tests;

use HtmlTools\Helpers;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class HelpersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider headingDataProvider
     */
    public function testAddHeadingsIdWithoutLink($expected, $string)
    {
        $this->assertSame($expected, Helpers::addHeadingsId($string));
    }

    public function headingDataProvider()
    {
        return array(
            array('<h1 id="test">Test</h1>', '<h1>Test</h1>'),
            array('<h1 id="test">Test</h1><h2 class="foo" id="bar">bar</h2>', '<h1>Test</h1><h2 class="foo">bar</h2>'),
            array('<h1 id="test">Test</h1><h1 id="test-2">Test</h1><h1 id="test-3">Test</h1>', '<h1>Test</h1><h1>Test</h1><h1>Test</h1>'),
            array('<h1 id="how-does-this-lib-scale">How does this lib scale ?</h1>', '<h1>How does this lib scale ?</h1>'),
            array('<h1 id="is-it-utf8-ye-it-is">&iexcl;&iquest; Is it UTF8 ?! &yacute;&euro;$ it is</h1>', '<h1>¡¿ Is it UTF8 ?! ý€$ it is</h1>'),
            array('', ''),
            array('<p>test</p>', '<p>test</p>'),
            array('<ul><li>foo</li><li>bar</li></ul>', '<ul><li>foo<li>bar</ul>'),
        );
    }

    public function testAddHeadingsIdWithLink()
    {
        $document = <<<EOL
<h1>Test</h1>
<h2 class="foo">bar</h2>
<h3 id="custom">Test</h3>
EOL;
        $expected = <<<EOL
<h1 id="test">Test<a href="#test" class="anchor">#</a></h1>
<h2 class="foo" id="bar">bar<a href="#bar" class="anchor">#</a></h2>
<h3 id="custom">Test<a href="#custom" class="anchor">#</a></h3>
EOL;
        $this->assertSame($expected, Helpers::addHeadingsId($document, 'h1, h2, h3, h4, h5, h6', true));
    }

    public function testBuildTOC()
    {
        $document = <<<EOL
<h1>1.0.0.0.0.0</h1>
<h2>1.1.0.0.0.0</h2>
<h2>1.2.0.0.0.0</h2>
<h3>1.2.1.0.0.0</h3>
<h3>1.2.2.0.0.0</h3>
<h4>1.2.2.1.0.0</h4>
<h5>1.2.2.1.1.0</h5>
<h6>1.2.2.1.1.1</h6>
<h1>Another Title</h1>
EOL;
        $expected = array(
            1 => array(
                'title' => '1.0.0.0.0.0',
                'id' => '1-0-0-0-0-0',
                'childs' => array(
                    1 => array(
                        'title' => '1.1.0.0.0.0',
                        'id' => '1-1-0-0-0-0',
                        'childs' => array(),
                    ),
                    2 => array(
                        'title' => '1.2.0.0.0.0',
                        'id' => '1-2-0-0-0-0',
                        'childs' => array(
                            1 => array(
                                'title' => '1.2.1.0.0.0',
                                'id' => '1-2-1-0-0-0',
                                'childs' => array(),
                            ),
                            2 => array(
                                'title' => '1.2.2.0.0.0',
                                'id' => '1-2-2-0-0-0',
                                'childs' => array(
                                    1 => array(
                                        'title' => '1.2.2.1.0.0',
                                        'id' => '1-2-2-1-0-0',
                                        'childs' => array(
                                            1 => array(
                                                'title' => '1.2.2.1.1.0',
                                                'id' => '1-2-2-1-1-0',
                                                'childs' => array(
                                                    1 => array(
                                                        'title' => '1.2.2.1.1.1',
                                                        'id' => '1-2-2-1-1-1',
                                                        'childs' => array(),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            2 => array(
                'title' => 'Another Title',
                'id' => 'another-title',
                'childs' => array(
                )
            ),
        );
        $this->assertSame($expected, Helpers::buildTOC($document));
    }

    public function testBuildTOCWithWeirdDOM()
    {
        $document = <<<EOL
<h2>h2</h2>
<h3>h3</h3>
<h2>h2</h2>
<h4>h4</h4>
<h3>h3</h3>
EOL;
        $expected = array(
            0 => array(
                // H1 supposed to be here
                'title' => null,
                'id' => null,
                'childs' => array(
                    1 => array(
                        'title' => 'h2',
                        'id' => 'h2',
                        'childs' => array(
                            1 => array(
                                'title' => 'h3',
                                'id' => 'h3',
                                'childs' => array(),
                            ),
                        ),
                    ),
                    2 => array(
                        'title' => 'h2',
                        'id' => 'h2-2',
                        'childs' => array(
                            1 => array(
                                // H2 supposed to be here
                                'title' => null,
                                'id' => null,
                                'childs' => array(
                                    1 => array(
                                        'title' => 'h4',
                                        'id' => 'h4',
                                        'childs' => array(),
                                    ),
                                ),
                            ),
                            2 => array(
                                'title' => 'h3',
                                'id' => 'h3-2',
                                'childs' => array(),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSame($expected, Helpers::buildTOC($document));
    }
}
