<?php

namespace Drupal\Tests\bouncing_jaw\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\bouncing_jaw\Traits\ConfigTrait;

/**
 * Test that the play button advances the audio stream and playlist.
 *
 * @group bouncing_jaw
 */
class PlayTest extends WebDriverTestBase {

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
   */
  public function setUp(): void {
    parent::setUp();

    $this->setConfigImage();
    $this->addClips(2);

    $this->drupalPlaceBlock('bouncing_jaw', ['region' => 'content']);
  }

  public function testPlay() {
    $this->drupalGet('<front>');
    // $assert_session = $this->assertSession();

    // $assert_session->buttonExists('Show row weights')->press();


    // $page = $this->getSession()->getPage();

    // $jawContainer = $page->find('css', 'div.bouncing-jaw-container');
    // $this->assertNotEmpty($jawContainer);

    // $playButton = $page->findButton('Play a clip');
    // $this->assertNotEmpty($playButton);

    // $audioElement = $page->find('css', 'audio');
    // $this->assertNotEmpty($audioElement);

    // $audioSrc = $audioElement->getAttribute('src');
    // $this->assertEquals('audioClip0.mp3', $audioSrc);
    // $playButton->click();

    // $this->assertEquals('audioClip1.mp3', $audioElement->getAttribute('src'));
  }

  // /**
  //  * Assert that the search suggestions contain a given string with given input.
  //  *
  //  * @param string $search
  //  *   The string to search for.
  //  * @param string $contains
  //  *   Some HTML that is expected to be within the suggestions element.
  //  */
  // protected function assertSuggestionContains($search, $contains) {
  //   $this->resetSearch();
  //   $page = $this->getSession()->getPage();
  //   $page->fillField('admin-toolbar-search-input', $search);
  //   $this->getSession()->getDriver()->keyDown('//input[@id="admin-toolbar-search-input"]', ' ');
  //   $page->waitFor(3, function () use ($page) {
  //     return ($page->find('css', 'ul.ui-autocomplete')->isVisible() === TRUE);
  //   });
  //   $suggestions_markup = $page->find('css', 'ul.ui-autocomplete')->getHtml();
  //   $this->assertStringContainsString($contains, $suggestions_markup);
  // }

  // /**
  //  * Assert that the search suggestions does not contain a given string.
  //  *
  //  * Assert that the search suggestions does not contain a given string with a
  //  * given input.
  //  *
  //  * @param string $search
  //  *   The string to search for.
  //  * @param string $contains
  //  *   Some HTML that is not expected to be within the suggestions element.
  //  */
  // protected function assertSuggestionNotContains($search, $contains) {
  //   $this->resetSearch();
  //   $page = $this->getSession()->getPage();
  //   $page->fillField('admin-toolbar-search-input', $search);
  //   $this->getSession()->getDriver()->keyDown('//input[@id="admin-toolbar-search-input"]', ' ');
  //   $page->waitFor(3, function () use ($page) {
  //     return ($page->find('css', 'ul.ui-autocomplete')->isVisible() === TRUE);
  //   });
  //   if ($page->find('css', 'ul.ui-autocomplete')->isVisible() === FALSE) {
  //     return;
  //   }
  //   else {
  //     $suggestions_markup = $page->find('css', 'ul.ui-autocomplete')->getHtml();
  //     $this->assertStringNotContainsString($contains, $suggestions_markup);
  //   }
  // }

  // /**
  //  * Search for an empty string to clear out the autocomplete suggestions.
  //  */
  // protected function resetSearch() {
  //   $page = $this->getSession()->getPage();
  //   // Empty out the suggestions.
  //   $page->fillField('admin-toolbar-search-input', '');
  //   $this->getSession()->getDriver()->keyDown('//input[@id="admin-toolbar-search-input"]', ' ');
  //   $page->waitFor(3, function () use ($page) {
  //     return ($page->find('css', 'ul.ui-autocomplete')->isVisible() === FALSE);
  //   });
  // }

  // /**
  //  * Checks that there is a link with the specified url in the admin toolbar.
  //  *
  //  * @param string $url
  //  *   The url to assert exists in the admin menu.
  //  *
  //  * @throws \Behat\Mink\Exception\ElementNotFoundException
  //  */
  // protected function assertMenuHasHref($url) {
  //   $this->assertSession()
  //     ->elementExists('xpath', '//div[@id="toolbar-item-administration-tray"]//a[contains(@href, "' . $url . '")]');
  // }

  // /**
  //  * Checks that there is no link with the specified url in the admin toolbar.
  //  *
  //  * @param string $url
  //  *   The url to assert exists in the admin menu.
  //  *
  //  * @throws \Behat\Mink\Exception\ExpectationException
  //  */
  // protected function assertMenuDoesNotHaveHref($url) {
  //   $this->assertSession()
  //     ->elementNotExists('xpath', '//div[@id="toolbar-item-administration-tray"]//a[contains(@href, "' . $url . '")]');
  // }

}
