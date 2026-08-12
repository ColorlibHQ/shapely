/**
 * ESLint flat config.
 *
 * Replaces both .jshintrc and .jscsrc — JSCS was deprecated years ago and told
 * users to migrate to ESLint ("JSCS has merged with ESLint").
 *
 * Only theme-authored JavaScript is linted. The bundled third-party libraries
 * (FlexSlider, OwlCarousel, parallax) are minified vendor code and are ignored.
 */
import js from '@eslint/js';
import globals from 'globals';

export default [
  {
    ignores: [
      'node_modules/**',
      'vendor/**',
      'build/**',
      'assets/js/**/*.min.js',
      'assets/js/owl-carousel/**',
      'assets/js/dev/**',
    ],
  },
  js.configs.recommended,
  {
    files: ['assets/js/**/*.js'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'script',
      globals: {
        ...globals.browser,
        jQuery: 'readonly',
        wp: 'readonly',
        Masonry: 'readonly',
        WPUrls: 'readonly',
        Vimeo: 'readonly',
        // Localized by wp_localize_script() in functions.php / the companion plugin.
        ShapelyAdminObject: 'readonly',
        ShapelyBuilder: 'readonly',
        shapelyWelcomeScreenObject: 'readonly',
        shapelyWelcomeScreenCustomizerObject: 'readonly',
      },
    },
    rules: {
      // Carried over from the old .jshintrc so the house style is unchanged.
      curly: 'error',
      eqeqeq: ['error', 'allow-null'],
      quotes: ['error', 'single', { avoidEscape: true }],
      'no-caller': 'error',
      'no-eval': 'error',
      'no-implied-eval': 'error',
      'no-irregular-whitespace': ['error', { skipStrings: true }],
      'no-unused-vars': ['warn', { args: 'none' }],
      // jQuery plugins are feature-detected before use; don't flag the guards.
      'no-undef': 'error',
    },
  },
];
