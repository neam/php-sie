<?php
namespace document;

use sie\document\Renderer;

class RendererTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitGuy
     */
    protected $guy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testReplacesInputOfTheWrongEncodingWithQuestionmark()
    {

        $renderer = new Renderer();
        $renderer->add_line("Hello ☃", [1]);
        $output = $renderer->render();

        $this->assertEquals("#Hello ? 1\n", $output);

    }

}
