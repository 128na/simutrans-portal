import axios from "axios";

const api = {
    click: slug => axios.post(`/api/v1/click/${slug}`),
};

const action = {
    conversion: e => {
        const el = e.currentTarget;
        const slug = el.dataset.slug;
        api.click(slug);

        const url = el.dataset.url;
        const features = 'noopener';
        window.open(url, null, features);
    }
};

$('.js-click').on('click', action.conversion);
