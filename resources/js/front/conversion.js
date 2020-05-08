import axios from "axios";

const conversion = e => {
  e.preventDefault();
  const el = e.currentTarget;
  const slug = el.dataset.slug;
  axios.post(`/api/v1/click/${slug}`);

  const url = el.dataset.url;
  const features = 'noopener';
  window.open(url, null, features);
};

$('.js-click').on('click', conversion);
