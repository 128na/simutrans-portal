const $modal = $('#add-bookmark');
const $name = $modal.find('#from-item-name');
const $type = $modal.find('#from-bookark-item-type');
const $id = $modal.find('#from-bookark-item-id');

$('.js-add-bookmark').on('click', e => {
  const dataset = e.currentTarget.dataset;

  $name.text(dataset.name);
  $type.val(dataset.type);
  $id.val(dataset.id);

  $modal.modal('show');
});
