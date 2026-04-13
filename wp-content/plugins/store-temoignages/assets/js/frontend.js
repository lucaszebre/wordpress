(function ($) {
  'use strict';

  $(document).ready(function () {
    var $cards = $('.st-card');
    if ( ! $cards.length ) return;

    $cards.each(function (i) {
      var $c = $(this);
      $c.css({ opacity: 0, transform: 'translateY(20px)', transition: 'opacity .4s ease, transform .4s ease' });
      setTimeout(function () {
        $c.css({ opacity: 1, transform: 'translateY(0)' });
      }, i * 80);
    });
  });

}(jQuery));
