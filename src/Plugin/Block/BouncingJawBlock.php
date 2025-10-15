<?php

namespace Drupal\bouncing_jaw\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Bouncing Jaw Block.
 *
 * Block caching is tied to the settings for this module.
 *
 * @todo Is it better to store the entity_type.manager service or
 *   the fileStorage object?
 *
 * @Block(
 *   id = "bouncing_jaw",
 *   admin_label = @Translation("Bouncing jaw block"),
 *   category = @Translation("Custom"),
 * )
 */

class BouncingJawBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The file system interface.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Implements BlockBase::create().
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Constructs BouncingJaw Plugin object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    LoggerChannelFactoryInterface $loggerFactory,
    ConfigFactoryInterface $configFactory,
    EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerFactory = $loggerFactory;
    $this->configFactory = $configFactory;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->configFactory->get('bouncing_jaw.settings');
    $logger = $this->loggerFactory->get('bouncing_jaw');

    $imageFileId = $config->get('face_file');
    if (empty($imageFileId)) {
      $logger->warning("Trying to display a Bouncing Jaw block without selecting an image");
      return [];
    }

    $fileStorage = $this->entityTypeManager->getStorage('file');
    $imageUrl = $imageFileId ? $fileStorage->load($imageFileId)->createFileUrl(TRUE) : '';
    if (empty($imageUrl)) {
      $logger->warning("Trying to display a Bouncing Jaw block with a broken image");
      return [];
    }

    $sets = $config->get('sets');
    if (empty($sets)) {
      $logger->warning("Trying to display a Bouncing Jaw block without any quote sets");
      return [];
    }

    $tracks = [];
    foreach ($sets as $set) {
      if ($set['audio_file']) {
        $tracks[] = $fileStorage->load($set['audio_file'])->createFileUrl(TRUE);
      }
    }
    if (empty($tracks)) {
      $logger->warning("Trying to display a Bouncing Jaw block without any valid sound files ");
      return [];
    }

    $backgroundX = $config->get('jaw_left') + ($config->get('jaw_width') / 2);
    $backgroundY = $config->get('jaw_top') + ($config->get('jaw_height'));
    $jawImageHeight = $config->get('jaw_height') + ($config->get('jaw_move'));

    return [
      '#theme' => 'bouncing_jaw',
      '#imageUrl' => $imageUrl,
      '#backgroundLeft' => $backgroundX,
      '#backgroundTop' => $backgroundY,
      '#jawImageHeight' => $jawImageHeight,
      '#jawImageWidth' => $config->get('jaw_width'),
      '#jawImageTop' => $config->get('jaw_top'),
      '#jawImageLeft' => $config->get('jaw_left'),
      '#buttonText' => $config->get('button_text'),
      '#tracks' => $tracks,
      '#attached' => [
        'drupalSettings' => [
          'bouncingJaw' => [
            'tracks' => $tracks,
            'jawMovement' => $config->get('jaw_move'),
          ],
        ],
      ],
      '#cache' => [
        'tags' => [
          'config:bouncing_jaw.settings',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    // Example form element: a text field for configuring the block title.
    $form['block_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Block title'),
      '#description' => $this->t('Enter a title for this block.'),
      '#default_value' => $config['block_title'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save the custom block configuration.
    $this->setConfigurationValue('block_title', $form_state->getValue('block_title'));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // Provide a default configuration for this block, if necessary.
    return [
      'block_title' => '',
    ] + parent::defaultConfiguration();
  }

}
