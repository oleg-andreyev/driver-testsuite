<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

class ContentTest extends TestCase
{
    public function testOuterHtml()
    {
        $this->getSession()->visit($this->pathTo('/outer-html.html'));

        $element = $this->getAssertSession()->elementExists('css', '.travers');

        $this->assertEquals(
            "<div class=\"travers\">
    <div class=\"sub\">el1</div>
    <div class=\"sub\">el2</div>
    <div class=\"sub\">
        <a href=\"some_url\">some <strong>deep</strong> url</a>
    </div>
</div>",
            $element->getOuterHtml()
        );
    }

    public function testDumpingEmptyElements()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $element = $this->getAssertSession()->elementExists('css', '#empty');

        $this->assertEquals(
            'An empty <em></em> tag should be rendered with both open and close tags.',
            trim($element->getHtml())
        );
    }

    /**
     * @dataProvider getAttributeDataProvider
     */
    public function testGetAttribute($attributeName, $attributeValue)
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $element = $this->getSession()->getPage()->findById('attr-elem['.$attributeName.']');

        $this->assertNotNull($element);
        $this->assertSame($attributeValue, $element->getAttribute($attributeName));
    }

    public function getAttributeDataProvider()
    {
        return array(
            array('with-value', 'some-value'),
            array('without-value', ''),
            array('with-empty-value', ''),
            array('with-missing', null),
        );
    }

    public function testJson()
    {
        $this->getSession()->visit($this->pathTo('/json.php'));
        $this->assertStringContainsString(
            '{"key1":"val1","key2":234,"key3":[1,2,3]}',
            $this->getSession()->getPage()->getContent()
        );
    }

    public function testHtmlDecodingNotPerformed()
    {
        $session = $this->getSession();
        $webAssert = $this->getAssertSession();
        $session->visit($this->pathTo('/html_decoding.html'));
        $page = $session->getPage();

        $span = $webAssert->elementExists('css', 'span');
        $input = $webAssert->elementExists('css', 'input');

        $expectedHtml = '<span custom-attr="&amp;">some text</span>';
        $this->assertStringContainsString($expectedHtml, $page->getHtml(), '.innerHTML is returned as-is');
        $this->assertStringContainsString($expectedHtml, $page->getContent(), '.outerHTML is returned as-is');

        $this->assertEquals('&', $span->getAttribute('custom-attr'), '.getAttribute value is decoded');
        $this->assertEquals('&', $input->getAttribute('value'), '.getAttribute value is decoded');
        $this->assertEquals('&', $input->getValue(), 'node value is decoded');
    }
}
