<?php

namespace Drupal\Tests\bouncing_jaw\Functional;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\bouncing_jaw\Traits\ConfigTrait;

/**
 * Provide an image without clips, verify the resulting render array is empty.
 *
 * @group bouncing_jaw
 */
class EmptyImageTest extends KernelTestBase {

  use ConfigTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules needed for the test.
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
   * Only add clips.
   */
  public function setUp() : void {
    parent::setUp();

    // For managed files.
    $this->installConfig('file');
    $this->installEntitySchema('file');

    $this->addClips(4);
  }

  /**
   * Verify the render array is empty.
   */
  public function testRenderArray(): void {
    $block = \Drupal::service('plugin.manager.block')->createInstance('bouncing_jaw_block', []);
    $render = $block->build();
    $this->assertEmpty($render);
  }

}
