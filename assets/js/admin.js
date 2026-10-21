/* اسکریپت‌های پنل ادمین: ردیف‌های تکرارشونده */
(function () {
  'use strict';

  var uid = 1000;

  function containerFor(name) {
    return document.querySelector('[data-repeater="' + name + '"]');
  }

  document.querySelectorAll('[data-rep-add]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var name = btn.getAttribute('data-rep-add');
      var box = containerFor(name);
      if (!box) return;
      var rows = box.querySelectorAll('.rep-row');
      var src = rows.length ? rows[rows.length - 1] : null;
      if (!src) return;
      var clone = src.cloneNode(true);
      uid += 1;
      var idx = uid;
      clone.querySelectorAll('input, select, textarea').forEach(function (field) {
        if (field.name) {
          field.name = field.name.replace(/\[\d+\]/g, '[' + idx + ']');
        }
        if (field.type !== 'button') {
          field.value = '';
        }
      });
      box.appendChild(clone);
      var first = clone.querySelector('input, textarea');
      if (first) first.focus();
    });
  });

  document.addEventListener('click', function (e) {
    var del = e.target.closest ? e.target.closest('.rep-del') : null;
    if (!del) return;
    var row = del.closest('.rep-row');
    if (row) row.remove();
  });
})();
