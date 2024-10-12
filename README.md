## INTRODUCTION

The Bouncing Jaw module provides a block that displays a face with a bouncing
jaw animation when it plays sound files uploaded in the module's admin
configuration page.

This module currently provides ONE configuration page that affects all
instances of the Bouncing Jaw, since I only use it for one instance.
Changing this to a per-block configuration would be trivial, but does not
fit my personal MVP.

## REQUIREMENTS

This module requires jQuery to handle the user interactions.

## INSTALLATION

Add the following repository to your composer.json
```
  {
      "type": "vcs",
      "url":  "https://github.com/jasonmce/bouncing_jaw.git"
  }
```
Include it on your site with
```
composer require jasonmce/bouncing_jaw
```

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION
Go to Configuration > Media > Bouncing Jaw, at /admin/config/media/bouncing_jaw
- Open the "Face" tab and Upload a face image
  Use "Save Configuration" to reload the page with the jaw image
  Select the jaw rectangle.
    Using keyboard up & down arrows in the jaw position fields
    moves the red jaw rectangle real time.
- Open the "Clips" tab and add quotes.
  Use "Save Configuration" to store your changes.
Go to Structure Block layout, /admin/structure/block
- Add the "Bouncing Jaw block" to a region
  Hit "Save blocks"
Enjoy!

## Why
This is a passion project I have been meaning to do for a while.  I created my
first "Magic Jason-Ball" over 10 years ago using jPlayer as a block of jQuery
embedded directly into the Drupal 7 homepage source of www.JasonMcEachen.com.
I later moved it to a block of embedded  jQuery in my Drupal 9 site.  It was
sufficient, but I realized it would make an easy enough custom block module.

Things I got to freshen up on while doing this:
- Admin config form
  - Using a canvas and javascript interactions
  - Multiple sets of configuration structures
  - Form validation
  - Veritcal tabs
  - Phpunit involving complex config schemas
  - Service dependency injection in blocks

### References
https://web.archive.org/web/20131231171500/http://jasonmceachen.com/

## MAINTAINERS

Current maintainers for Drupal 9, 10, 11:

- Jason McEachen (JasonMcE) - https://www.drupal.org/u/jasonmce
