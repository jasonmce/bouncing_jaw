<?php

namespace Drupal\Tests\bouncing_jaw\Traits;

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\TestTools\Random;

/**
 * Test trait to set bouncing_jaw test configuration.
 */
trait ConfigTrait {

  /**
   * Create an image and update config to use it as the face_file.
   */
  protected function setConfigImage() : void {
    $jawFile = $this->createJawImage();

    $config = $this->container->get('config.factory')->getEditable(
      'bouncing_jaw.settings'
    );

    $config->set('face_file', $jawFile->id())
      ->set('jaw_left', 10)
      ->set('jaw_top', 20)
      ->set('jaw_width', 30)
      ->set('jaw_height', 40)
      ->set('jaw_move', 50)
      ->set('button_text', 'Play a clip')
      ->save();

    $this->assertEquals($config->get('face_file'), $jawFile->id());
    $this->nid = $jawFile->id();
  }

  /**
   * Wrapper for calling addClip() multiple times.
   *
   * @param int $count
   *   Number of additional clips to add to the configuration.
   */
  protected function addClips(int $count) {
    for ($i = 0; $i < $count; $i++) {
      $this->addClip();
    }
  }

  /**
   * Add a fake sound clip set.
   */
  protected function addClip() : void {
    $config = $this->container->get('config.factory')->getEditable(
      'bouncing_jaw.settings'
    );

    $sets = $config->get('sets');
    $newSetIndex = is_array($sets) ? count($sets) : 0;
    $data = 'empty audio file ' . $newSetIndex;
    $filename = 'audioClip' . $newSetIndex . '.mp3';
    $directory = 'public://bouncing_jaw/clips';

    $fileSystem = $this->container->get('file_system');
    $fileSystem->prepareDirectory($directory, FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $file_uri = $fileSystem->saveData($data, $directory . '/' . $filename);

    $file = File::create([
      'filename' => basename($file_uri),
      'uri' => $file_uri,
      'status' => 1,
      'uid' => 1,
    ]);
    $file->save();

    $audioFileId = $file->id();
    $sets[$newSetIndex] = [
      'name' => Random::machineName(),
      'audio_file' => $audioFileId,
    ];

    $config->set('sets', $sets)->save();
  }

  /**
   * Create a 500x500 jpg file at TEMPDIR/testImage.jpg.
   */
  protected function createJawImage() {
    $fileSystem = $this->container->get('file_system');
    $tempDirectory = $fileSystem->getTempDirectory();

    $image = imagecreate(500, 500);
    try {
      imagejpeg($image, $tempDirectory . '/testImage.jpg');
    }
    catch (\Exception $e) {
      throw new \Exception('You have no permission to create files in this directory:' . $e);
    }

    $filepath = $tempDirectory . '/testImage.jpg';
    $directory = 'public://bouncing_jaw/face';

    $fileSystem->prepareDirectory($directory, FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $fileSystem->copy($filepath, $directory . '/' . basename($filepath), FileSystemInterface::EXISTS_REPLACE);

    $file = File::create([
      'filename' => basename($filepath),
      'uri' => $directory . '/' . basename($filepath),
      'status' => 1,
      'uid' => 1,
    ]);
    $file->save();

    return $file;
  }

}
