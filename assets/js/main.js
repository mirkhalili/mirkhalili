/* اسکریپت‌های سایت عمومی — بدون وابستگی */
(function () {
  'use strict';

  /* ورود تدریجی */
  var io = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          en.target.classList.add('is-in');
          io.unobserve(en.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
  );
  document.querySelectorAll('[data-reveal]').forEach(function (el) {
    io.observe(el);
  });

  /* ساعت تهران */
  var fmt = new Intl.DateTimeFormat('fa-IR', {
    timeZone: 'Asia/Tehran',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  });
  function tick() {
    var t = fmt.format(new Date());
    document.querySelectorAll('[data-clock]').forEach(function (el) {
      el.textContent = t;
    });
  }
  tick();
  setInterval(tick, 1000);

  /* منوی موبایل */
  var btn = document.getElementById('menu-btn');
  var menu = document.getElementById('mobile-menu');
  if (btn && menu) {
    var bars = btn.querySelectorAll('.menu-bar');
    var open = false;
    var setOpen = function (v) {
      open = v;
      menu.classList.toggle('hidden', !v);
      menu.classList.toggle('flex', v);
      btn.setAttribute('aria-expanded', String(v));
      document.body.style.overflow = v ? 'hidden' : '';
      bars[0].style.transform = v ? 'translateY(3.5px) rotate(45deg)' : '';
      bars[1].style.transform = v ? 'translateY(-3.5px) rotate(-45deg)' : '';
    };
    btn.addEventListener('click', function () {
      setOpen(!open);
    });
    menu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        setOpen(false);
      });
    });
  }

  /* پیش‌نمایش شناور فهرست کارها (دسکتاپ) */
  var preview = document.getElementById('hover-preview');
  if (preview && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    var img = preview.querySelector('img');
    var raf = 0;
    var move = function (e) {
      var x = e.clientX;
      var y = e.clientY;
      if (!raf) {
        raf = requestAnimationFrame(function () {
          preview.style.transform = 'translate(calc(' + x + 'px - 50%), calc(' + y + 'px - 50%))';
          raf = 0;
        });
      }
    };
    document.querySelectorAll('.work-row').forEach(function (row) {
      row.addEventListener('pointerenter', function (e) {
        img.src = row.getAttribute('data-cover') || img.src;
        preview.classList.add('is-on');
        move(e);
      });
      row.addEventListener('pointermove', move);
      row.addEventListener('pointerleave', function () {
        preview.classList.remove('is-on');
      });
    });
  }
})();
