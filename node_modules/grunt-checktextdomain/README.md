# grunt-checktextdomain

> Check your code for missing or incorrect text-domain in gettext functions

While this plug-in was created for development of WordPress plug-ins & themes it should work well with any php-written code base. While the functions normally meant by *gettext functions* (e.g. the native `gettext()`, `ngettext()`) do not allow you to pass a text domain, some platforms - such as WordPress - provide functions which do (.e.g `__()`, `_n()`). This plug-in is intended for *those* functions: to ensure that a domain is given and that is it matches the desired domain specificed in your `Gruntfile.js`.

This plug-in was inspired by the command line tool [add-textdomain](http://develop.svn.wordpress.org/trunk/tools/i18n/add-textdomain.php).

## Getting Started
This plugin requires Grunt `~0.4.1`

If you haven't used [Grunt](http://gruntjs.com/) before, be sure to check out the [Getting Started](http://gruntjs.com/getting-started) guide, as it explains how to create a [Gruntfile](http://gruntjs.com/sample-gruntfile) as well as install and use Grunt plugins. Once you're familiar with that process, you may install this plugin with this command:

```shell
npm install grunt-checktextdomain --save-dev
```

Once the plugin has been installed, it may be enabled inside your Gruntfile with this line of JavaScript:

```js
grunt.loadNpmTasks('grunt-checktextdomain');
```

## The "checktextdomain" task

### Important: Before you start

For the task to run you need to specify:

1. **Text domain(s)** - a string or array of valid text domain(s)
2. **Keywords** - gettext functions, along with a specification indicating where to look for the text domain


#### Keyword specifications
This task extends the original [keyword specification](http://www.gnu.org/software/gettext/manual/html_node/xgettext-Invocation.html) to indicate where to look for the text domain. The default specification is of the form

``` 
    [function name]:[argument-specifier],[argument-specifier],...
```
where an argument specificier, `[argument-specifier]`, is of the form

 - `[number]` - indicating that this argument is a translatable string
 - `[number]c` - indicating that this argument is a context specifier


For example:

 - `gettext` - the translated string is the first argument of `gettext()`
 - `ngettext:1,2` -  the translated strings are arguments 1 and 2 of of `ngettext()`
 - `pgettext:1c,2` -  argument 1 is a context specifier and the translated string is argument 2 of `pgettext()`


This task requires an additional argument specifier (in fact this is the only required one): `[number]d` - indicating that the argument is a domain specifier. For example:

 - `__:1,2d` - the translated string is the first argument of `__()` and the domain is the second argument
 - `_n:1,2,4d` -  the translated strings are arguments 1 and 2 of `_n()` and the fourth is the domain specifier.
 - `_nx:1,2,3c,5d` -  the translated strings are arguments 1 and 2 of `_nx()`, the third is a context specifier and the fifth is the domain specifier.


#### Example keyword specifications (WordPress)

```
keywords: [
	'__:1,2d',
	'_e:1,2d',
	'_x:1,2c,3d',
	'esc_html__:1,2d',
	'esc_html_e:1,2d',
	'esc_html_x:1,2c,3d',
	'esc_attr__:1,2d', 
	'esc_attr_e:1,2d', 
	'esc_attr_x:1,2c,3d', 
	'_ex:1,2c,3d',
	'_n:1,2,4d', 
	'_nx:1,2,4c,5d',
	'_n_noop:1,2,3d',
	'_nx_noop:1,2,3c,4d'
];
```

### Overview
In your project's Gruntfile, add a section named `checktextdomain` to the data object passed into `grunt.initConfig()`.

```js
grunt.initConfig({
  checktextdomain: {
    options: {
      // Task-specific options go here.
    },
    files: {
      // Files to target go here
    },
  },
})
```

### Options

#### text_domain
Type: `String`|`Array`

Must be provided. A text domain (or an array of text domains) indicating the domains to check against.

#### keywords
Type: `Array`

An array of keyword specifications to look for. See above section for details & examples.

#### report_missing
Type: `Bool`
Default value: `true`

Whether to report use of keywords without a domain being passed.

#### report_variable_domain
Type: `Bool`
Default value: `true`

Whether to report use of keywords with a variable being used as the domain.

#### correct_domain
Type: `Bool`
Default value: `false`

Whether to automatically correct incorrect domains. Please note that this does **not** add in missing domains, and can **only** be used when one text domain is supplied. This will also correct instances where a variable, rather than string is used as a text doman, **unless** you set `report_variable_domain` to `false`.

#### create_report_file
Type: `Bool`
Default value: `false`

Create a hidden `.[target].json` file with reported errors.

#### force

Type: `Bool`
Default value: `false`

Set force to true to report text domain errors but not fail the task

### Usage Examples

This is a typical set-up for WordPress development. The only thing specific to WordPress here is the keywords list.

```js
    checktextdomain: {
	  standard{
         options:{
			text_domain: 'my-domain', //Specify allowed domain(s)
			keywords: [ //List keyword specifications
				'__:1,2d',
				'_e:1,2d',
				'_x:1,2c,3d',
				'esc_html__:1,2d',
				'esc_html_e:1,2d',
				'esc_html_x:1,2c,3d',
				'esc_attr__:1,2d', 
				'esc_attr_e:1,2d', 
				'esc_attr_x:1,2c,3d', 
				'_ex:1,2c,3d',
				'_n:1,2,4d', 
				'_nx:1,2,4c,5d',
				'_n_noop:1,2,3d',
				'_nx_noop:1,2,3c,4d'
			]
		},
		files: [{
			src: ['**/*.php'], //all php 
			expand: true,
		}],
	  }
    },
```


## Contributing
In lieu of a formal styleguide, take care to maintain the existing coding style. Add unit tests for any new or changed functionality. Lint and test your code using [Grunt](http://gruntjs.com/).

## Release History
* *1.0.1* - 21st June 2016 - Loose Grunt peer dependency to support Grunt 1.0.0
* *1.0.0* - **Potential breaking change:** Grunt now aborts on error (`grunt.fail.warn`). Thanks to @alexVauch. [#4](https://github.com/stephenharris/grunt-checktextdomain/pull/4). Added `force` task to configure this behaviour.
* *0.1.1* - Fix bug where functions are used to pass value to gettext functions.
* *0.1.0* - Initial release
