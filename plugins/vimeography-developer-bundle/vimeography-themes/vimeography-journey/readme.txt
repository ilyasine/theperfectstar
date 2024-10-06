=== Vimeography Journey ===
Tags: vimeo
Requires at least: 3.8
Tested up to: 6.0
Stable tag: 2.2.2
License: GPL-2.0

The easiest way to create beautiful Vimeo galleries on your WordPress site.

== Description ==

Journey is a beautiful thumbnail slider coupled with descriptions, titles and playcounts.

Make your gallery stand out with our custom themes!
[http://vimeography.com/themes](http://vimeography.com/themes "vimeography.com/themes")

For the latest updates, follow us!
[http://twitter.com/vimeography](http://twitter.com/vimeography "twitter.com/vimeography")

== Installation ==

1. Upload `vimeography-journey.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= Help! My theme doesn't look right! =

Okay, deep breath. More than likely, it is another plugin causing this issue. See if you can pinpoint which one by disabling your plugins, one by one, and really determining if you need it. If that task sounds daunting, try disabling plugins that are used for photo galleries, minifying scripts, widgets, or otherwise alter your blog's appearance.

= Can I change the look of my Vimeography theme? =

Heck yeah! Use the appearance editor to change your theme's style so that it matches your site perfectly.

== Changelog ==

= 2.2.2 =
* Fix google fonts issue
* Updating build process for all themes
* Blueprint bugfixes

= 2.1.1 =
* Adds default font size to search box
* Bump compatibility to WordPress 5.5

= 2.1 =
* [New] Show number of search results in message after query is performed.
* Update theme dependencies to latest versions
* Add `fitvidsignore` attribute to player to prevent fitvids intervention
* Add support for loading "unlisted" videos in Pro
* Improve error message with link to docs on video load error
* Bump compatibility to WordPress 5.4
* Update markup for latest rc-slider compatibility

= 2.0.7 =
* [Tweak] Thumbnails are now lazy loaded by default
* [Tweak] Hide the spinner if a search returns no results
* [Tweak] Unload the video player before loading a new video in it

= 2.0.6 =
* [Fix] Introduce fallback for when source video downloads are unavailable
* [Fix] Navigate to the current window pathname instead of root
* [Tweak] Allow player settings to be configured via Vimeography Pro
* [Tweak] Upgrade to Vimeo.js 2.6.x

= 2.0.5 =
* [Fix] Mangle double let declaration errors in Safari 10

= 2.0.4 =
* [Fix] Update Vimeography Blueprint helpers for player padding
* [Fix] Rebuild for IE11 compatibility

= 2.0.3 =
* [New] Add placeholder text to searchbox
* [Fix] Adds postcss-cssnext to theme build process
* [Fix] Corrects videos per page bug when paging through search results

= 2.0.2 =
* [Fix] Add rendering compatibility for Microsoft Edge 16

= 2.0.1 =
* Switch to new Download Link component from Vimeography Blueprint
* Switch to new Thumbnail Mixin from Vimeography Blueprint
* Adds an :alt tag to thumbnail images

= 2.0 =
* Rewrote the Journey theme for Vimeography 2.0 compatibility

= 1.2.3 =
* Theme is now loaded as soon as the plugin class is instantiated.
* Ensure fullscreen is exited during playlisting.

= 1.2.2 =
* Added schema.org markup for improved SEO
* This update also helps make sure your site doesn't run into errors if the Vimeography plugin is deactivated.

= 1.2.1 =
* Rewrote javascript file for using multiple galleries on a page
* Minified CSS and JS files for quicker gallery loading
* Added better SEO markup to the gallery

= 1.2 =
* Allow video downloads for Vimeo Pro members using Vimeography Pro
* Not a Vimeo Pro member? You're missing out. Learn more at http://vimeography.com/vimeo-pro
* Check out all Vimeography Pro features at http://vimeography.com/pro

= 1.1 =
* Added support for Vimeography Pro Playlists
* Added a control for the playbar visibility [PRO]
* Switched the thumbnail overlay hover to pure CSS

= 1.0.7 =
* Fixed an issue with thumbnail overlay resizing
* Ensure that there is no bottom margin on thumbnails

= 1.0.6 =
* Fixed an issue that prevented CSS customizations from loading properly

= 1.0.5 =
* Improved responsive nature of Journey theme.
* Fixed an issue where the Video Title may not cover the entire thumbnail.
* Cleaned up and organized the theme assets.

= 1.0.4 =
* Fixed an issue that caused double spacing on the first video description
* Corrected the aspect ratio for video thumbnails

= 1.0.3 =
* Added thumbnail sizing controls for Vimeography Pro

= 1.0.2 =
* Updated paging controls for Vimeography Pro
* Reduced filesize
* Updated testing stats

= 1.0.1 =
* Fixed an issue where the scrollbar would sometimes not initialize on pageload.
* Improved compatibility with Vimeography Pro.

= 1.0 =
* Converted to plugin.

== Upgrade Notice ==
= 2.0 =
Requires Vimeography 2.0
