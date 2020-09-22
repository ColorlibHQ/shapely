declare var wp: any;

import { EpsilonSectionDoubled } from '../../sections/section-doubled';

/**
 * Doubled Section Constructor
 */
wp.customize.sectionConstructor[ 'epsilon-section-doubled' ] = wp.customize.Section.extend( {
  ready: function() {
    const section = this;
    new EpsilonSectionDoubled( section );
  },
  attachEvents: function() {
  },
  isContextuallyActive: function() {
    return true;
  }
} );
