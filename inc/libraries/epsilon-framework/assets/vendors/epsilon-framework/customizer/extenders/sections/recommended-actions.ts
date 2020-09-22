declare var wp: any;

import { EpsilonSectionRecommended } from '../../sections/recommended-actions';

/**
 * Recommended Section Constructor
 */
wp.customize.sectionConstructor[ 'epsilon-section-recommended-actions' ] = wp.customize.Section.extend( {
  ready: function() {
    new EpsilonSectionRecommended( this );
  },
  attachEvents: function() {
  },
  isContextuallyActive: function() {
    return true;
  }
} );
