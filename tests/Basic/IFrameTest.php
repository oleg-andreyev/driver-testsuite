<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

final class IFrameTest extends TestCase
{
    /**
     * @dataProvider iFrameDataProvider
     */
    public function testIFrame(string $iframeIdentifier, string $elementSelector, string $elementContent): void
    {
        $this->getSession()->visit($this->pathTo('/iframe.html'));
        $webAssert = $this->getAssertSession();

        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Main window div text', $el->getText());

        $this->getSession()->switchToIFrame($iframeIdentifier);

        $el = $webAssert->elementExists('css', $elementSelector);
        $this->assertSame($elementContent, $el->getText());

        $this->getSession()->switchToIFrame();

        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Main window div text', $el->getText());
    }

    /**
     * @return array{0: string, 1: string, 2: string}[]
     */
    public static function iFrameDataProvider(): array
    {
        return [
            'by name' => ['subframe_by_name', '#text', 'iFrame div text'],
            'by id' => ['subframe_by_id', '#foobar', 'Some accentués characters'],
        ];
    }
}
