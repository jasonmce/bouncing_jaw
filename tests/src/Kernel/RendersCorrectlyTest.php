<?php

namespace Drupal\Tests\bouncing_jaw\Functional;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\bouncing_jaw\Traits\ConfigTrait;

/**
 * Provide an image and clips, verify the resulting render array.
 *
 * @group bouncing_jaw
 */
class RendersCorrectlyTest extends KernelTestBase {

  use ConfigTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'block',
    'node',
    'bouncing_jaw',
    'file',
    'user',
  ];

  /**
   * {@inheritdoc}
   *
   * Set the necessary configuration values for the block to render.
   */
  public function setUp() : void {
    parent::setUp();

    // For managed files.
    $this->installConfig('file');
    $this->installEntitySchema('file');

    $this->setConfigImage();
    $this->addClips(4);
  }

  /**
   * Verify the render array with an image and four clips.
   */
  public function testRenderArray(): void {
    $block = \Drupal::service('plugin.manager.block')->createInstance('bouncing_jaw', []);
    $render = $block->build();

    $this->assertEquals('bouncing_jaw', $render['#theme']);

    $this->assertNotFalse(strpos($render['#imageUrl'], 'files/bouncing_jaw/face/testImage.jpg'));

    $this->assertEquals(25, $render['#backgroundLeft']);
    $this->assertEquals(60, $render['#backgroundTop']);
    $this->assertEquals(90, $render['#jawImageHeight']);
    $this->assertEquals(30, $render['#jawImageWidth']);
    $this->assertEquals(20, $render['#jawImageTop']);
    $this->assertEquals(10, $render['#jawImageLeft']);
    $this->assertEquals('Play a clip', $render['#buttonText']);
    $this->assertEquals(4, count($render['#tracks']));

    $this->assertEquals(4, count($render['#attached']['drupalSettings']['bouncingJaw']['tracks']));
    $this->assertEquals(50, $render['#attached']['drupalSettings']['bouncingJaw']['jawMovement']);

    $this->assertTrue(in_array('config:bouncing_jaw.settings', $render['#cache']['tags']));
  }

}
