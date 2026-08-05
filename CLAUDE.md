# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Shapely — a free one-page **WordPress theme** by Colorlib, distributed on WordPress.org and licensed GPLv3. It is a PHP theme based on Underscores (`_s`), not a static HTML template: the frontend stack is Bootstrap 3.3.7 + jQuery + Font Awesome 6.4.2, and `style.css` (5k+ lines) is hand-edited with **no SCSS pipeline** at the theme level.

## Commands

```bash
npm ci                # install the pinned toolchain

npm run build         # produce shapely.zip (runs i18n:check first via prebuild)
npm run verify        # lint everything + text domain check — the pre-commit gate
npm run lint          # eslint + stylelint + php -l, in parallel
npm run lint:fix      # auto-fix what eslint/stylelint can
npm run i18n          # regenerate languages/shapely.pot (needs WP-CLI on PATH)
npm run i18n:check    # verify every gettext call uses an allowed text domain
npm run images        # report optimisable PNG/JPEG; add -- --write to apply
```

Grunt was removed in 1.2.20. All 39 npm audit findings (1 critical) came from
`grunt-contrib-imagemin` and `grunt-contrib-compress`; the replacement tree has
zero. `jscs` was dropped because it is deprecated and tells users to migrate to
ESLint. The build scripts live in `tools/` and are plain Node ESM.

PHP coding standards still run through Composer/PHPCS:

```bash
phpcs -p -s . --standard=./phpcs.ruleset.xml --extensions=php
```

CI is GitHub Actions (`.github/workflows/ci.yml`): `php -l` across PHP 7.4-8.5, PHPCompatibility, ESLint, Stylelint, the text domain check, and a build that uploads shapely.zip as an artifact. The old `.travis.yml` was deleted -- it targeted PHP 5.4-7.1 and had not run since Travis withdrew free OSS builds.

There is no test suite in-repo. Development requires a real WordPress install — symlink or copy this directory into `wp-content/themes/shapely/`. CI is Travis (legacy, PHP 5.4–7.1) plus a CodeQL JavaScript workflow.

Release 1.2.20 was verified against a live **WordPress 7.0.2 / PHP 8.5.3** install (Local). Static analysis alone missed several real defects on this theme — a duplicate `id="main"`, an invalid `<icon>` element, and two vendored libraries calling jQuery aliases that jQuery 4 removes were all only found by loading real pages. When changing templates or JS, load the site and check the browser console and `WP_DEBUG_LOG`; do not rely on `php -l` and grep alone.

## Versioning

The version lives in **four** places, all currently in sync at 1.2.20: `style.css` header (authoritative for WordPress), `readme.txt`, `changelog.txt`, and `package.json`. `set_tags.sh` (Travis) tags releases from `package.json`'s version. When bumping, update all four and add a `changelog.txt` entry (mirrored into `readme.txt`'s `== Changelog ==` section).

## Architecture

### Bootstrapping

`functions.php` requires each `inc/*.php` file exactly once, near the top. Every function is still wrapped in `if ( ! function_exists( … ) ) :` and every class in `if ( ! class_exists( … ) ) :` so child themes can override them — keep new files in `inc/` following that pattern.

`Shapely` (`inc/class-shapely.php`) is a singleton: get it via `Shapely::get_instance()`, never `new Shapely()`. It is instantiated once, on `init`, from `functions.php`. Its constructor returns early unless `is_admin() || is_customize_preview()`.

`SHAPELY_VERSION` is defined at the top of `functions.php` from the `style.css` header and must be passed as the `$ver` argument to every `wp_enqueue_style`/`wp_enqueue_script` call for theme-owned assets.

### Customizer (the theme's real UI)

Nearly all theme behavior is driven by `get_theme_mod()` reads scattered across templates. The chain:

1. `inc/customizer.php` (~1500 lines) registers the `shapely_main_options` panel and its sections/controls.
2. `inc/libraries/epsilon-framework/` — a **vendored** third-party customizer framework (Macho Themes) supplying `Epsilon_Control_Toggle`, `Epsilon_Control_Slider`, `Epsilon_Section_Pro`, `Epsilon_Section_Recommended_Actions`. `Shapely::init_epsilon()` passes a whitelist of controls/sections to load. Classes resolve through `class-epsilon-autoloader.php`, which maps `Foo_Bar` → `class-foo-bar.php` across a fixed directory list.
3. `shapely_get_theme_options()` in `inc/extras.php` builds a CSS string from those theme mods. `shapely_enqueue_theme_options_css()` buffers it and hands it to `wp_add_inline_style( 'shapely-style', … )`, so it rides along with the main stylesheet rather than being echoed straight into `wp_head`.

So **adding a color/typography option means two edits**: a control in `inc/customizer.php` *and* a selector block in `shapely_get_theme_options()`. Sanitizers (`shapely_sanitize_checkbox`, `shapely_sanitize_layout`, …) live at the bottom of `customizer.php`.

The Epsilon JS bundles (`assets/js/epsilon-framework-*.js`) are webpack output from TypeScript/Vue sources in `inc/libraries/epsilon-framework/assets/vendors/` — do not hand-edit the bundles; rebuild with `npm run build` **inside `inc/libraries/epsilon-framework/`** (its own webpack setup, unrelated to the theme's `npm run build`).

### Layout resolution

`shapely_get_layout_class()` (`inc/extras.php`) is the single source of truth for `full-width` / `no-sidebar` / `sidebar-left` / `sidebar-right`. It reads the assigned page-template slug first, then falls back to theme mods (`single_post_layout_template`, `blog_layout_template`, `projects_layout_template`, `single_project_layout_template`). Templates echo the result as a class on `#primary` and branch on it to call `get_sidebar()`. `shapely_show_sidebar()` is a separate gate that also honours the `site_layout` post meta.

Blog listings (`index.php`, `archive.php`) dispatch on the `blog_layout_view` theme mod to `template-parts/layouts/blog-{grid,large-image,large-image-grid}.php`, which in turn loop over `template-parts/content-*.php`.

### Widget-driven pages

- `page-templates/template-home.php` ("Home Page") renders **nothing but** the `sidebar-home` widget area. The actual homepage sections (portfolio, testimonials, parallax, call-to-action) are widgets shipped by the separate **shapely-companion** plugin, not by this repo.
- `page-templates/template-widget.php` ("Builder Page") renders a per-page widget area `shapely-{post_name}`. `Shapely_Builder` (`inc/class-shapely-builder.php`) queries all pages using that template, registers one sidebar per page on `widgets_init` (priority 20), caches the id list in the `shapely_builder_sidebars` transient, and uses `sidebars_widgets` filtering to keep `shapely-page-content`/`shapely-page-title` widgets out of non-builder areas.

`header.php` opens `#content`, `.content-area` and `#main`; `footer.php` closes them — edit the pair together. Both bypass the loop and call helpers from `inc/extras.php` (`shapely_get_header_logo()`, `shapely_header_menu()`, `shapely_top_callout()`, `shapely_footer_callout()`, `shapely_social_icons()`).

### Other integrations

- **WooCommerce**: theme support in `functions.php`, `woocommerce.php` as the wrapper, `woocommerce/product-searchform.php` as the only template override. The `shop-sidebar` widget area registers only when `shapely_is_woocommerce_activated()`. `woocommerce/` is excluded from phpcs.
- **Jetpack**: `inc/jetpack.php` plus `archive-jetpack-portfolio.php` / `single-jetpack-portfolio.php` for the portfolio CPT (masonry is enqueued only when that CPT exists).
- **Welcome screen / recommended plugins**: `inc/libraries/welcome-screen/` + `inc/class-shapely-notify-system.php` drive the "Import Demo Content" flow and the recommended-plugins list defined in `Shapely::$recommended_plugins`.

### Frontend JS

`assets/js/shapely-scripts.js` is a single jQuery IIFE handling sticky header, mobile toggle, smooth scroll, `data-background` image holders, FlexSlider/OwlCarousel init, video backgrounds, and the header search widget. It reads `ShapelyAdminObject.sticky_header`, localized in `shapely_scripts()`.

Every optional third-party plugin (FlexSlider, OwlCarousel, imagesLoaded, YTPlayer, Vimeo, vide) is feature-detected before use, and anything derived from `window.location.hash` is wrapped in `try`/`catch` — a user-supplied hash reaches a jQuery selector there. Keep both habits.

`assets/js/jquery-compat.js` restores the `.bind()`/`.unbind()`/`.delegate()`/`.undelegate()` aliases that jQuery 4 removes and the bundled FlexSlider still calls. It is a guarded no-op on jQuery 3.x and is enqueued as a dependency of the `flexslider` handle. **Do not use jQuery event shorthands** (`.mouseover()`, `.focusout()`, `.resize()`, …) in theme JS — they are removed in jQuery 4; use `.on()`. Unminified upstream sources for flexslider/parallax/smooth-scroll sit in `assets/js/dev/` (reference only — the minified files at `assets/js/` are what is enqueued).

## Conventions

- Prefix everything `shapely_` / `Shapely_`; wrap functions in `if ( ! function_exists() ) :` so child themes can override them.
- Font Awesome 6 splits families: brand glyphs (`fa-github`, `fa-x-twitter`, …) need `fa-brands`, everything else uses `fa`/`fa-solid`. A brand icon rendered with plain `fa` shows a blank box. FA4 `-o` outline suffixes no longer exist. The bundled build is 6.4.2, so icons added later (e.g. `fa-bluesky`, 6.6) are unavailable.
- PHP follows WordPress-Core via `phpcs.ruleset.xml` (plus `PHPCompatibility`; `node_modules/` and `woocommerce/` excluded). Newer files open with an `ABSPATH` guard.
- JS follows `.jshintrc`: `es3`, single quotes, mandatory curly braces, `eqeqeq`. New localized objects must be added to its `globals` whitelist or jshint fails.
- Text domain is `shapely` (`epsilon-framework` is also allowed by checktextdomain). Translations live in `languages/`.
- Supported floor: WordPress 6.4 / PHP 7.4, tested to WordPress 6.8 / PHP 8.4 (stated in `style.css`, `readme.txt`, and `functions.php`).

## Repo quirks worth knowing

- `README.md` documents a git-submodule workflow and `setup.sh` / `setup.bat` — **none of that exists**: `.gitmodules` is empty, no submodules are registered, and the Epsilon framework is vendored directly in `inc/libraries/`.
- `.gitignore` used to list `Gruntfile.js`, `.jshintrc`, `.travis.yml`, `phpcs.ruleset.xml` and `package-lock.json`. The first three are gone; the last two are now deliberately tracked (`npm ci` needs the lockfile, and the phpcs ruleset is real config).
- The zip builder in `tools/build-zip.mjs` is deny-by-default: a file ships unless `EXCLUDE` matches it. The old Grunt copy task was allow-everything-then-subtract, which is how `CLAUDE.md` and a nested `package.json` ended up inside released zips.
- The vendored `inc/libraries/` code is third-party but **not** a submodule, so it is edited in place when it blocks a PHP/WordPress upgrade. Two such patches exist: optional-before-required parameters in `class-epsilon-control-section-repeater.php`, and `get_page_by_title()` (deprecated in WP 6.2) in `class-epsilon-welcome-screen.php`.
- `Shapely_Notify_System` overrides the vendored `check_plugin_is_installed()`/`check_plugin_is_active()` because the parent hardcodes `ABSPATH . 'wp-content/plugins/'` and breaks on relocated content directories.
- `layouts/content-sidebar.css` and `layouts/sidebar-content.css` are leftovers from Underscores and are not enqueued anywhere.
