FullCalendar.globalLocales.push(function () {
  'use strict';

  var alllang = {
    code: 'all',
    week: {
      dow: 0, // Sunday is the first day of the week.
      doy: 6, // The week that contains Jan 1st is the first week of the year.
    },
     buttonHints: {
      prev: global_lang_previous,
      next: global_lang_next,
      today: global_lang_today,
    },
    buttonText: {
      prev: global_lang_previous,
      next: global_lang_next,
      today: global_lang_today,
      month: global_lang_month,
      week: global_lang_week,
      day: global_lang_day,
      list: global_lang_week,
    },
    weekText: global_lang_week,
    allDayText: global_lang_all_day,
    moreLinkText: function(n) {
      return '+ ' + global_lang_more
    },
    noEventsText: global_lang_no_event_found
  };

  return alllang;

}());

