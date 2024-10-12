<?php

namespace Drupal\bouncing_jaw\Form;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for file and name sets.
 *
 * @todo Is it better to store the entity_type.manager service or
 *   the fileStorage object?
 */
class BouncingJawConfigForm extends ConfigFormBase {


  /**
   * The file usage interface.
   *
   * @var \Drupal\file\FileUsage\FileUsageInterface
   */
  protected $fileUsage;

  /**
   * The file system interface.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs BouncingJaw Plugin object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\file\FileUsage\FileUsageInterface $fileUsage
   *   The file usage interface.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger factory.
   */
  public function __construct(
    ConfigFactoryInterface $configFactory,
    EntityTypeManagerInterface $entityTypeManager,
    FileUsageInterface $fileUsage,
    LoggerChannelFactoryInterface $loggerFactory,
  ) {
    parent::__construct($configFactory);
    $this->entityTypeManager = $entityTypeManager;
    $this->fileUsage = $fileUsage;
    $this->loggerFactory = $loggerFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('file.usage'),
      $container->get('logger.factory'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['bouncing_jaw.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bouncing_jaw_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('bouncing_jaw.settings');

    // Container for tab elements.
    $form['tabs'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'face_group',
    ];

    $form['face_group'] = [
      '#group' => 'tabs',
      '#type' => 'details',
      '#title' => $this->t('Face'),
    ];
    $form['clips_group'] = [
      '#group' => 'tabs',
      '#type' => 'details',
      '#title' => $this->t('Clips'),
    ];

    $form['face_info'] = [
      '#group' => 'face_group',
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'face-info-wrapper',
        ],
      ],
    ];

    if ($config->get('face_file')) {
      $imageFileId = $config->get('face_file');
      $fileStorage = $this->entityTypeManager->getStorage('file');
      $imageUri = $imageFileId ? $fileStorage->load($imageFileId)->createFileUrl(TRUE) : '';
      $form['face_info']['face_image'] = [
        '#type' => 'markup',
        '#title' => "",
        '#markup' => "<img src='$imageUri' /><canvas class='jaw_rectangle'>jaw_rectanlge</canvas><div class='jaw-image'></div>",
        '#allowed_tags' => array_merge(Xss::getHtmlTagList(), ['canvas', 'div', 'img']),
      ];
    }

    $form['face_info']['face_file'] = [
      '#type' => 'managed_file',
      '#upload_location' => 'public://bouncing_jaw/face/',
      '#multiple' => FALSE,
      '#description' => $this->t('Allowed extensions: gif png jpg jpeg'),
      '#title' => $this->t('Upload a face image file'),
      '#default_value' => [$config->get('face_file')],
      '#upload_validators' => [
        'file_validate_is_image' => [],
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => [25600000],
      ],
    ];
    $form['face_info']['button_text'] = [
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
      '#type' => 'textfield',
      '#title' => $this->t('Play button text'),
      '#required' => TRUE,
      '#default_value' => [$config->get('button_text') ?? 'Play a clip'],
      '#size' => 25,
      '#maxlength' => 254,
    ];
    $form['face_info']['jaw_left'] = [
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#title' => 'jaw left side %',
      '#required' => TRUE,
      '#default_value' => [$config->get('jaw_left') ?? 30],
      '#size' => 3,
    ];
    $form['face_info']['jaw_top'] = [
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#title' => 'jaw top side %',
      '#required' => TRUE,
      '#default_value' => [$config->get('jaw_top') ?? 60],
      '#size' => 3,
    ];
    $form['face_info']['jaw_width'] = [
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#title' => 'jaw width %',
      '#required' => TRUE,
      '#default_value' => [$config->get('jaw_width') ?? 40],
      '#size' => 3,
    ];
    $form['face_info']['jaw_height'] = [
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#title' => 'jaw height %',
      '#required' => TRUE,
      '#default_value' => [$config->get('jaw_height') ?? 40],
      '#size' => 3,
    ];
    $form['face_info']['jaw_move'] = [
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#title' => 'jaw move %',
      '#required' => TRUE,
      '#default_value' => [$config->get('jaw_move') ?? 10],
      '#size' => 3,
    ];
    $form['face_info']['test_jaw'] = [
      '#type' => 'inline_template',
      '#template' => '<button type="button" class="btn btn-sm btn-default btn-test-jaw" data-dismiss="modal">Test jaw move</button>',
    ];

    $form['#tree'] = TRUE;
    $form['sets'] = [
      '#group' => 'clips_group',
      '#type' => 'container',
      '#prefix' => '<div id="sets-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['sets']['help'] = [
      '#type' => 'item',
      '#title' => $this->t('Quote sets'),
      '#markup' => $this->t('Quote is a combination of required audio file and option subtitle file'),
    ];

    $sets = $form_state->get('sets');
    $config_sets = $config->get('sets');

    // If form_state sets is empty, try using config sets count.
    if ($sets === NULL) {
      $sets = $config_sets ? count($config_sets) : 1;
      $form_state->set('sets', $sets);
    }

    for ($i = 0; $i < $sets; $i++) {
      // Set title includes the name or count and default to open.
      $form['sets'][$i] = [
        '#type' => 'details',
        '#title' => $this->t('Quote set') . ' ' . ($config_sets[$i]['name'] ?? $i + 1),
        '#open' => empty($config_sets[$i]['name']),
      ];
      $form['sets'][$i]['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Name'),
        '#default_value' => $config_sets[$i]['name'],
        '#size' => 10,
      ];
      $form['sets'][$i]['audio_file'] = [
        '#type' => 'managed_file',
        '#upload_location' => 'public://bouncing_jaw/sounds/',
        '#multiple' => FALSE,
        '#description' => $this->t('Allowed extensions: ogg mp4'),
        '#title' => $this->t('Upload an audio file'),
        '#default_value' => [$config_sets[$i]['audio_file']],
        '#upload_validators' => [
          'file_validate_extensions' => ['ogg mp4'],
          'file_validate_size' => [25600000],
        ],
      ];
    }

    $form['sets']['actions'] = [
      '#type' => 'actions',
    ];
    $form['sets']['actions']['add_set'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add another set'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addMoreCallback',
        'wrapper' => 'sets-wrapper',
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
    ];

    $form['#attached']['library'][] = 'bouncing_jaw/admin_page';

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $faceInfo = $form_state->getValue('face_info');
    $faceImageId = $faceInfo['face_file'][0] ?? 0;
    $this->config('bouncing_jaw.settings')
      ->set('face_file', $faceImageId)
      ->set('jaw_left', $faceInfo['jaw_left'])
      ->set('jaw_top', $faceInfo['jaw_top'])
      ->set('jaw_width', $faceInfo['jaw_width'])
      ->set('jaw_height', $faceInfo['jaw_height'])
      ->set('jaw_move', $faceInfo['jaw_move'])
      ->set('button_text', $faceInfo['button_text'])
      ->save();

    /* Load the object of the file by it's fid */
    $fileStorage = $this->entityTypeManager->getStorage('file');

    $file = $fileStorage->load($faceImageId);
    /* Set the status flag permanent of the file object */
    $file->setPermanent();
    $file->save();
    $this->fileUsage->add($file, 'bouncing_jaw', 'file', $file->id());
    $values = $form_state->getValue('sets');
    // Prepare an array to store cleaned values.
    $clean_values = [];
    foreach ($values as $id => $set) {
      // Skip any 'actions' or 'help' elements.
      if (!in_array($id, ['actions', 'help'])) {

        if ($set['audio_file']) {
          $audioFileIndex = $set['audio_file'][0];
          /* Load the object of the file by it's fid */
          $file = $fileStorage->load($audioFileIndex);

          /* Set the status flag permanent of the file object */
          $file->setPermanent();
          $file->save();
          $this->fileUsage->add($file, 'bouncing_jaw', 'file', $file->id());
        }
        $clean_values[] = [
          'name' => $set['name'],
          'audio_file' => $set['audio_file'][0],
        ];

      }
    }

    // Save the cleaned values to configuration.
    $this->config('bouncing_jaw.settings')
      ->set('sets', $clean_values)
      ->save();

    parent::submitForm($form, $form_state);

    // Add a drupal message to inform the user of the save.
    $message = $this->t('The configuration has been updated.');
    $logger = $this->loggerFactory->get('bouncing_jaw');
    $logger->notice($message);
  }

  /**
   * Implements submitForm.
   *
   * Submit handler for add_set.
   *
   * Increments the form_state number of sets and calls a rebuild to show the
   * additonal set group.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $sets = $form_state->get('sets') + 1;
    $form_state->set('sets', $sets);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Implements AjaxCallback.
   *
   * Ajax callback to redraw the form that has been incremented by addOne().
   *
   * @return array
   *   Current $form values.
   */
  public function addMoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['sets'];
  }

}
