declare var require: any;
declare var wp: any;
import * as $ from 'jquery';

import { EpsilonColorSchemesPreviewer } from './color-schemes/color-schemes';
import { EpsilonTypographyPreviewer } from './typography/typography';
import { EpsilonSectionEditorPreviewer } from './section-editor/section-editor';
import { EpsilonPartialRefresh } from './partial-refresh/partial-refresh';

wp.customize.bind( 'preview-ready', function() {
  new EpsilonColorSchemesPreviewer();
  new EpsilonTypographyPreviewer();
  new EpsilonSectionEditorPreviewer();
  new EpsilonPartialRefresh();
} );
