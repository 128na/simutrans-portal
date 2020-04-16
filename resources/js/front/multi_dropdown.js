// 多層ドロップダウン対応
$('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
  const $target = $(e.currentTarget);
  if (!$target.next().hasClass('show')) {
    $target.parents('.dropdown-menu').first().find('.show').removeClass("show");
  }
  var $subMenu = $target.next(".dropdown-menu");
  $subMenu.toggleClass('show');

  $target.parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
    $('.dropdown-submenu .show').removeClass("show");
  });
  return false;
});
