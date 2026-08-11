/*========= About Theme =========*/

Theme Name: Shapely
Theme URI: https://colorlib.com/wp/themes/shapely/
Version: 1.2.20

Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 7.4

Author: Colorlib
Author URI: https://colorlib.com
License: GNU General Public License v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
-------------------------------------------------------
Shapely theme, Copyright 2016-2026 colorlib.com
Shapely WordPress theme is distributed under the terms of the GNU GPL
Shapely is based on Underscores http://underscores.me/, (C) 2012-2025 Automattic, Inc.
-------------------------------------------------------

== Description ==

Shapely is a powerful and versatile one page WordPress theme with pixel perfect design and outstanding functionality. It is by far the most advanced free WordPress theme available today with loads of unmatched customization options. This theme comes with several homepage widgets that can be used to add portfolio, testimonials, parallax sections, your product or service information, call for action and much more.

Shapely supports most free and premium WordPress plugins such as WooCommerce, Jetpack, Kali Forms, Gravity Forms, Contact Form 7, Yoast SEO, Google Analytics by Yoast and much more.

This theme is the best suited for business, landing page, portfolio, ecommerce, store, local business,  personal websites but can be tweaked to be used as blog, magazine or any other awesome website while highlighting its unique one page setup. This is going to be the last WordPress theme you will ever want to use because it is so much better than anything you have seen. We promise.

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

For more detailed Shapely theme setup please read documentation: https://colorlib.com/wp/support/shapely/

== Translation ==
Theme can be translated directly on https://translate.wordpress.org/projects/wp-themes/shapely without relying on the .po file. All the translatable strings are pulled automatically. For more info please check this link https://make.wordpress.org/polyglots/handbook/tools/glotpress-translate-wordpress-org/

== Frequently Asked Questions ==

= Does this theme support any plugins? =

Shapely includes support for Infinite Scroll, Portfolio, Testimonials, Tiled Galleries for Jetpack.

= Long menus =

Shapely theme does not support long menus.

= Front page template not displaying content =

This page template is used to create the Parallax homepage from our demo : https://colorlib.com/shapely/ . it does not output any content added in the backend editor and we recommend using only the [Shapely] tagged widgets.

== Credits ==

* Based on Underscores https://underscores.me/, (C) 2012-2017 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* normalize.css https://necolas.github.io/normalize.css/, (C) 2012-2017 Nicolas Gallagher and Jonathan Neal, [MIT](http://opensource.org/licenses/MIT)
* TGM Plugin Activation - https://tgmpluginactivation.com/, 2011, Thomas Griffin [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* Bootstrap v3.3.6 (https://getbootstrap.com), Copyright 2011-2014 Twitter, Inc. Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
* Font Awesome 6 by @davegandy - https://fontawesome.io - @fontawesome
	License - https://fontawesome.io/license (Font: SIL OFL 1.1, CSS: MIT License)
* WP-Bootstrap-NavWalker licensed under the GPLv2 license (https://www.gnu.org/licenses/gpl-2.0.html)
* FlexSlider by WooThemes licensed under the GPLv2 license (https://www.gnu.org/licenses/gpl-2.0.html)
* jQuery Smooth Scroll, httpss://github.com/kswedberg/jquery-smooth-scroll. Copyright (c) 2015 Karl Swedberg. Licensed MIT(https://github.com/kswedberg/jquery-smooth-scroll/blob/master/LICENSE-MIT)
* jQuery Cloneya, Lisensed under [MIT](https://opensource.org/licenses/MIT)


== Changelog ==

= 1.2.21 =
* Fixed the theme's Font Awesome 6 never loading on sites running Elementor or any other plugin that registers the generic "font-awesome" handle. WordPress keeps only the first registration of a handle, so the theme's stylesheet was silently dropped and every fa-brands / fa-solid icon -- the social links, the search button, the menu and pagination arrows -- rendered as a blank box. It now uses its own "shapely-font-awesome" handle
* Fixed the customizer loading Font Awesome from .../shapely/inc/assets/css/..., a path that has never existed, so the customizer's own icons 404'd
* Fixed related posts on portfolio items returning the wrong posts when the portfolio taxonomies are not registered: wp_get_object_terms() returns a WP_Error there, and it was being passed straight into the query
* Regenerated languages/shapely.pot, which had been built before the text domain unification and was missing 86 translatable strings
* Added a version check to the build so style.css, readme.txt, changelog.txt and package.json can no longer drift apart (readme.txt had been left at 1.2.19)
* Fixed the coding-standards CI job, which had been failing to install PHPCS rather than actually checking anything

= 1.2.20 =
* PHP 8.5 compatibility: removed every runtime deprecation and warning raised by the theme
* Verified on WordPress 7.0.2 / PHP 8.5: zero PHP notices across all templates, the customizer, the welcome screen and the block editor
* Verified alongside the Shapely Companion plugin (1.2.10): homepage widget sections, parallax, features, call-to-action and contact all render with zero PHP notices
* Replaced the Grunt build with plain npm scripts (tools/*.mjs): eliminates all 39 npm audit findings (1 critical, 24 high), which came entirely from grunt-contrib-imagemin and grunt-contrib-compress
* Replaced JSHint and the deprecated JSCS with ESLint 10; added Stylelint 17 for CSS
* Fixed the release zip shipping developer files: CLAUDE.md, .scss sources, README.md, tsconfig.json, webpack.config.js and a nested package.json were all being published to users
* Fixed image optimisation never running: the Grunt task was pointed at assets/img/, which does not exist (the images are in assets/images/)
* languages/shapely.pot is now generated with WP-CLI and filtered to the shapely text domain, dropping 83 epsilon-framework strings that could never be translated through it and picking up 19 theme strings the old task missed
* Replaced the dead Travis CI config with GitHub Actions covering PHP 7.4 through 8.5
* package-lock.json and phpcs.ruleset.xml are now tracked instead of gitignored
* Fixed the "Requires at least" header reading "WordPress 6.4" instead of "6.4". The stray prefix made WordPress' minimum-version check pass on every release, so the theme could be activated on WordPress 4.9 where it would fatal
* Added the missing "Domain Path: /languages" header
* Corrected the theme tags: removed five surplus subject tags (WordPress.org allows three), dropped custom-menu, threaded-comments and translation-ready (retired in WordPress 4.6) and three-columns/four-columns (not actual content layouts), and added custom-header, grid-layout and wide-blocks, which the theme does support
* Rewrote the style.css description: shorter, factual, and without the two typos it had carried since 2016
* Moved the version headers out of the functions.php docblock; style.css is the only file WordPress reads them from and the two copies had drifted
* Aligned readme.txt's Theme URI, Author and version fields with style.css
* Featured-image links in the blog, search and portfolio listings now carry an accessible name; when an attachment has no alt text WordPress emits alt="", which left those links unnamed
* Fixed FlexSlider and OwlCarousel calling jQuery event aliases (.blur/.focus/.resize) and jQuery.type that jQuery 4 removes
* Fixed a duplicate id="main" and a duplicate role="main" landmark on the search, 404 and portfolio archive templates
* Fixed pagination arrows using an invalid <icon> element, leaving the prev/next links with no accessible name
* Fixed the back-to-top link, related-posts carousel arrows and related-posts thumbnails having no accessible name
* Fixed the search results template hardcoding a placeholder image, ignoring the placeholder customizer settings
* Fixed an empty author byline linking to a bare /author/ URL on posts with no valid author
* Fixed pagination nesting a <nav> landmark inside another <nav>
* Fixed fatal-prone breadcrumb code that read an array offset on `false` and an undefined variable when Rank Math was active without Yoast
* Fixed `strcasecmp(): Passing null` deprecation in the nav walker that fired once per menu item on every page load
* Fixed "Attempt to read property on null" in the excerpt and thumbnail helpers when no post is in scope
* Security: escaped unescaped portfolio term names, thumbnail URLs in `style` attributes, header nav style, and nav-menu fallback markup
* Security: rebuilt the search form so it no longer reflects filtered `<form>` attributes into the page
* Fixed excerpts splitting multibyte characters mid-glyph and blanking out on single-word content
* Fixed category/archive widget counts mangling any name or URL containing a closing parenthesis
* Fixed brand icons (Twitter/X, Facebook, GitHub, Dribbble, Vimeo) rendering blank under Font Awesome 6
* Fixed the missing `fa-folder-open-o` post-category icon, removed in Font Awesome 5+
* Fixed the RSS social icon rendering blank by using the solid rather than the brands font family
* Social links now expose an accessible name; the label span was previously rendered empty
* Fixed the "Skip to content" link never moving keyboard focus (`element.trigger()` on a DOM node)
* Fixed the header search panel closing on focus; its handlers targeted ids the form never rendered
* Fixed plugin detection failing on installs with a relocated `wp-content` directory
* Fixed lazy loading being stripped from listing images by an outdated kses allowlist
* Fixed the Builder/portfolio archive missing its Bootstrap `.row` wrapper
* Fixed the wide blog layout ignoring the placeholder-image customizer settings
* Fixed malformed `</<div>` markup in the Home page template
* Replaced `get_page_by_title()`, deprecated in WordPress 6.2, in the demo importer
* Forward compatibility with jQuery 4: replaced removed event shorthands and shimmed the aliases FlexSlider still uses
* Theme class is now a singleton; it was instantiated three times, registering every admin hook and the Epsilon framework three times over
* Assets now carry the theme version so browsers pick up CSS/JS changes after an update
* Customizer CSS is attached via `wp_add_inline_style()` instead of a raw `wp_head` style block
* Added `responsive-embeds` and `align-wide` block editor support
* Removed dead code: duplicate `require` block and a plugin-callback loop that never had callbacks to run

= 1.2.17 =
* Added a new customizer setting to control placeholder images for posts without featured images
* Added an option to upload custom placeholder images
* Added a global setting to show/hide categories across all blog layouts
* Improved customizer UI with clearer section headings for blog hero image
* Fixed issue with WP_Customize_Control class loading
* Added custom CSS for better visual separation in customizer settings
* Fixed compatibility issues with WordPress 6.8

= 1.2.16 =
* Added accessibility improvements for dropdown menu arrows
* Fixed dropdown menu arrow display for submenu items
* Improved menu item spacing for items with children
* Updated Font Awesome to version 6
* Enhanced CSS for better dropdown menu visibility
* Fixed navigation menu styling for mobile devices
* Improved theme documentation in README.md
* Added Git submodule information and setup instructions
* Added support for Kali Forms plugin
* Fixed custom excerpt function to properly handle content
* Enhanced thumbnail handling with fallback placeholder images
* Improved theme customizer with selective refresh support
* Updated styling for buttons and form elements
* Fixed header text color customization
* Added responsive design improvements

= 1.2.4 =
* Implemented milestone: https://github.com/puikinsh/shapely/milestone/8?closed=1

= 1.2.3 =
* Implemented milestone: https://github.com/puikinsh/shapely/milestone/7?closed=1

= 1.2.2 =
* Implemented milestone: https://github.com/puikinsh/shapely/milestone/6?closed=1

= 1.2.0 =

* Implemented milestone https://github.com/puikinsh/shapely/milestone/4

= 1.1.6 - June 15 2017 =

* Implemented milestone https://github.com/puikinsh/shapely/milestone/2

= 1.1.5 - June 9 2017 =

* Added French translation thanks to Michaël Crofte

= 1.1.0 - Jan 3 2017 =

* Major upgrade to comply with WordPress.org guidelines and best practices.

= 1.0.5 - Sept 3 2016 =

* Improved theme translation

= 1.0.4 - Sept 3 2016 =

* Removed deprecated tags

= 1.0.3 - Sept 3 2016 =

* Small bug fixes and improvements

= 1.0.2 - April 15 2016 =

* Fixed parse error for older version of PHP.
* Fixed error when mobile menu was appearing along regular menu between 990px - 991px.

= 1.0.1 - March 30 2016 =

* Renamed JavaScript file to get rid of not found error.
* Updated Smooth Scroll to 1.7.2

= 1.0.0 - March 26 2016 =
* Initial release

Stable tag: 1.2.21
