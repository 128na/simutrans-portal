import axios from 'axios';

const sendEvent = eventName => {
  // eslint-disable-next-line no-undef
  if (gtag) {
    const data = {
      event_category: 'click',
      event_label: decodeURI(location.pathname),
      non_interaction: true
    };
    // eslint-disable-next-line no-undef
    gtag('event', eventName, data);
  }
};

const conversion = e => {
  e.preventDefault();
  const el = e.currentTarget;
  const slug = el.dataset.slug;
  axios.post(`/api/v1/click/${slug}`);
  sendEvent('link');

  const url = el.dataset.url;
  const features = 'noopener';
  window.open(url, null, features);
};
const download = () => sendEvent('download');

$('.js-click').on('click', conversion);
$('.js-download').on('click', download);
