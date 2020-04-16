require('./bootstrap');
require('./conversion');

$('.articles .popover-thumbnail').popover({
  html: true,
  trigger: 'hover',
  delay: { show: 500, hide: 100 },
  content() {
    return `<img class="img-fluid" src="${this.dataset.src}">`
  }
})
