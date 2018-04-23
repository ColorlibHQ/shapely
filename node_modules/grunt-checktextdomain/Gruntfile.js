/* jshint -W099 */
/*
 * grunt-checktextdomain
 * https://github.com/stephenharris/grunt-checktextdomain
 *
 * Copyright (c) 2013 Stephen Harris
 * Licensed under the MIT license.
 */

'use strict';

//For convenience store keywards in global
var keywords = [
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

module.exports = function(grunt) {
	
  // Project configuration.
  grunt.initConfig({
    jshint: {
      all: [
        'Gruntfile.js',
        'tasks/*.js',
        '<%= nodeunit.tests %>',
      ],
      options: {
        jshintrc: '.jshintrc',
      },
    },

    // Before generating any new files, remove any previously-created files.
    clean: {
      tests: ['test/tmp'],
    },
    
    copy: {
    	tests: {
    		files: [
    		  //includes files within path
    	      //{expand: true, src: ['test/fixtures/*'], dest: 'test/tmp', filter: 'isFile'},
    	      // makes all src relative to cwd
    	      {expand: true, cwd: 'test/fixtures', src: ['**'], dest: 'test/tmp/'},
    	    ]
    	  }
    },

    // Configuration to be run (and then tested).
    checktextdomain: {
    	correct_domain: {
    		options:{
    			force: true,
    			text_domain: 'my-domain',
    			create_report_file: true,
    			keywords: keywords
        	},
        	files: [{
        		src: ['test/tmp/correct-domain.php'],
        		expand: true,
        	}],
		},
    	missing_domain: {
    		options:{
    			force: true,
    			text_domain: 'my-domain',
    			create_report_file: true,
    			keywords: keywords
        	},
        	files: [{
        		src: ['test/tmp/missing-domain.php'],
        		expand: true,
        	}],
		},
    	missing_domain_ignore_missing: {
    		options:{
    			force: true,
    			text_domain: 'my-domain',
    			report_missing: false,
    			create_report_file: true,
    			keywords: keywords
        	},
        	files: [{
        		src: ['test/tmp/missing-domain.php'],
        		expand: true,
        	}],
		},
    	incorrect_domain_autocomplete: {
    		options:{
    			force: true,
    			text_domain: 'my-domain',
    			correct_domain: true,
    			create_report_file: true,
    			keywords: keywords
        	},
        	files: [{
        		src: ['test/tmp/incorrect-domain-autocorrect.php'],
        		expand: true,
        	}],
		},
    	variable_domain_autocomplete: {
    		options:{
    		    force: true,
    			text_domain: 'my-domain',
    			correct_domain: true,
    			create_report_file: true,
    			keywords: keywords
        	},
        	files: [{
        		src: ['test/tmp/variable-domain-autocorrect.php'],
        		expand: true,
        	}],
		},
    	plurals: {
    		options:{
    		    force: true,
    			text_domain: 'my-domain',
    			create_report_file: true,
    			keywords: keywords
        	},
        	files: [{
        		src: ['test/tmp/plurals.php'],
        		expand: true,
        	}],
		}
    },

    // Unit tests.
    nodeunit: {
      tests: ['test/*_test.js'],
    },

  });

  // Actually load this plugin's task(s).
  grunt.loadTasks('tasks');

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-nodeunit');

  // Whenever the "test" task is run, first clean the "tmp" dir, then copy in the futures, then run this
  // plugin's task(s), then test the result.
  grunt.registerTask('test', ['clean', 'copy', 'checktextdomain', 'nodeunit']);

  // By default, lint and run all tests.
  grunt.registerTask('default', ['jshint', 'test']);

};
