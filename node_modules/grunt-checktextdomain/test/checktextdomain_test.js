/*jshint node:true */
'use strict';

var grunt = require('grunt');

/*
  ======== A Handy Little Nodeunit Reference ========
  https://github.com/caolan/nodeunit

  Test methods:
    test.expect(numAssertions)
    test.done()
  Test assertions:
    test.ok(value, [message])
    test.equal(actual, expected, [message])
    test.notEqual(actual, expected, [message])
    test.deepEqual(actual, expected, [message])
    test.notDeepEqual(actual, expected, [message])
    test.strictEqual(actual, expected, [message])
    test.notStrictEqual(actual, expected, [message])
    test.throws(block, [error], [message])
    test.doesNotThrow(block, [error], [message])
    test.ifError(value)
*/

exports.textdomain = {
  setUp: function(done) {
    // setup here if necessary
    done();
  },
  
  incorrect_domain_autocorrect: function(test) {
      test.expect(2);
      
      var target = 'incorrect_domain_autocomplete';
      
      //There are 14 missing domains
      var actual = JSON.parse( grunt.file.read( '.' + target + ".json" ) );
      test.equal( actual["test/tmp/incorrect-domain-autocorrect.php"].length, 14, '14 errors to report' );
      
      //Test corrected file
      var corrected = grunt.file.read( "test/tmp/incorrect-domain-autocorrect.php" );
      var expected = grunt.file.read( 'test/expected/incorrect-domain-autocorrect.php' );
      test.equal( corrected, expected, 'Domain should have been corrected' );
      
      //Clean up: Delete report file
      grunt.file.delete( '.' + target + ".json" );
      
      test.done();
      
  },
  
  variable_domain_autocorrect: function(test) {
      test.expect(2);
      
      var target = 'variable_domain_autocomplete';
      
      //There are 14 missing domains
      var actual = JSON.parse( grunt.file.read( '.' + target + ".json" ) );
      test.equal( actual["test/tmp/variable-domain-autocorrect.php"].length, 14, '14 errors to report' );
      
      //Test corrected file
      var corrected = grunt.file.read( "test/tmp/variable-domain-autocorrect.php" );
      var expected = grunt.file.read( 'test/expected/variable-domain-autocorrect.php' );
      test.equal( corrected, expected, 'Domain should have been corrected' );
      
      //Clean up: Delete report file
      grunt.file.delete( '.' + target + ".json" );
      
      test.done();
  },
  
  missing_domain: function(test) {  
      test.expect(1);
      
      var target = 'missing_domain';
      
      //There are 14 missing domains
      var actual = JSON.parse( grunt.file.read( '.' + target + ".json" ) );
      test.equal( actual["test/tmp/missing-domain.php"].length, 14, '14 errors to report' );
      
      //Clean up: Delete report file
      grunt.file.delete( '.' + target + ".json" );
      
      test.done();
  },
  
  missing_domain_ignore_missing: function(test) {  
      test.expect(1);
      
      var target = 'missing_domain_ignore_missing';
      
      var actual = JSON.parse( grunt.file.read( '.' + target + ".json" ) );
      
      //File only has missing domain, which we are nor reporting. There should be no errors.
      test.equal( actual["test/tmp/missing-domain.php"].length, 0, 'No errors reported' );
      
      //Clean up: Delete report file
      grunt.file.delete( '.' + target + ".json" );
      
      test.done();
  },
  
  correct_domain: function(test) {  
      test.expect(1);
      
      var target = 'correct_domain';
      
      var actual = JSON.parse( grunt.file.read( '.' + target + ".json" ) );
      
      //File is correct, should report no errors
      test.equal( actual["test/tmp/correct-domain.php"].length, 0, 'No errors reported' );
      
      //Clean up: Delete report file
      grunt.file.delete( '.' + target + ".json" );
      
      test.done();
  },
  
  
  plurals: function(test) {  
      test.expect(1);
      
      var target = 'plurals';
      var actual = JSON.parse( grunt.file.read( '.' + target + ".json" ) );
      
      //File is correct, should report no errors
      test.equal( actual["test/tmp/plurals.php"].length, 0, 'No errors reported' );
      
      //Clean up: Delete report file
      //grunt.file.delete( '.' + target + ".json" );
      
      test.done();
  },
  
};
