declare var require: any;
declare var wp: any;
import * as $ from 'jquery';

import { EpsilonNotices } from './notices/notices';

jQuery( document ).ready( function() {
  let notices = new EpsilonNotices();
  notices.init();
} );
