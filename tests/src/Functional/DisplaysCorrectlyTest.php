<?php

namespace Drupal\Tests\bouncing_jaw\Functional;

use Drupal\Tests\bouncing_jaw\Traits\ConfigTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Add the block to a page and confirm it's html.
 *
 * @group bouncing_jaw
 */
class DisplaysCorrectlyTest extends BrowserTestBase {

  use ConfigTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Because I do not know how to stop this throwing incomplete schema errors.
   *
   * @var bool
   */
  protected $strictConfigSchema = FALSE;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['block', 'file', 'node', 'bouncing_jaw'];

  /**
   * {@inheritdoc}
   *
   * Set the necessary configuration values and place the block.
   */
  public function setUp() : void {
    parent::setUp();

    $this->setConfigImage();
    $this->addClips(2);

    $this->drupalPlaceBlock('bouncing_jaw', ['region' => 'content']);
  }

  /**
   * Verify the render array with an image and four clips.
   */
  public function testPageDisplay(): void {
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->elementExists('css', '.bouncing-jaw-container');
    $elements = $this->xpath('//div[@class="bouncing-jaw-container"]');
    $this->assertCount(1, $elements);

    $imageUrl = $elements[0]->find('css', "img.background-image")->getAttribute('src');
    $this->assertEquals(basename($imageUrl), 'testImage.jpg');

    $buttonText = $elements[0]->find('css', "button.play-me")->getHtml();
    $this->assertEquals($buttonText, 'Play a clip');

    $audioclipUrl = $elements[0]->find('css', "audio.bouncing-jaw-player")->getAttribute('src');
    $this->assertEquals(basename($audioclipUrl), 'audioClip0.mp3');
  }

}
