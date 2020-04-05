require('./files/bootstrap');
require('./files/multi_dropdown');
require('./files/conversion');

import LazyLoad from "vanilla-lazyload";

new LazyLoad({
    elements_selector: ".lazy"
});
