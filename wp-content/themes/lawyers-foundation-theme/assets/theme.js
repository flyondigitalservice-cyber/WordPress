(function () {
  var btn = document.querySelector('.menu-btn');
  var nav = document.getElementById('site-nav');
  if (btn && nav) {
    btn.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
  // Enrolment fields are not needed for law students.
  var type = document.querySelector('select[name="member_type"]');
  if (type) {
    var toggle = function () {
      var student = type.value === 'student';
      ['enrolment_no', 'enrolment_year', 'court'].forEach(function (n) {
        var el = document.querySelector('[name="' + n + '"]');
        if (el && el.closest('.field')) el.closest('.field').style.display = student ? 'none' : '';
      });
    };
    type.addEventListener('change', toggle);
    toggle();
  }
})();
