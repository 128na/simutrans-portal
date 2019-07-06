/**
 * フロント機能用
 */

require('./files/bootstrap');
require('./files/conversion');
require('./files/multi_dropdown');

import LazyLoad from "vanilla-lazyload";

new LazyLoad({
    elements_selector: ".lazy"
});
