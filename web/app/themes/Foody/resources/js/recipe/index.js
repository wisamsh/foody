/**
 * Created by moveosoftware on 6/27/18.
 */

window.scroller = require('../common/scroll-progress');

require('./recipe');
require('./foody-calculator');

calculator('.recipe-ingredients-container li:not(.free-text-ingredients) .amount');