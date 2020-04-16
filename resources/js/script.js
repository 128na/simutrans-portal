require('./files/bootstrap');
require('./files/multi_dropdown');
require('./files/conversion');

import LazyLoad from "vanilla-lazyload";

new LazyLoad({
  elements_selector: ".lazy"
});

$('.articles .popover-thumbnail').popover({
  html: true,
  trigger: 'hover',
  delay: { show: 500, hide: 100 },
  content() {
    return `<img class="img-fluid" src="${this.dataset.src}">`
  }
})
