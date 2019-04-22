import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';

require('@fullcalendar/core/main.css');
require('@fullcalendar/daygrid/main.css');
require('@fullcalendar/list/main.css');

var calendarEl = document.getElementById('calendar');

if(calendarEl) {
  document.addEventListener('DOMContentLoaded', function() {

    var calendarType = calendarEl.getAttribute('data-calendar-type');
    var eventsUrl = '/events/json';
    var addEventUrl = '/events/add';
    var btnRight = 'dayGridMonth,listMonth';

    if(calendarType == 'guild') {
      var guildSlug = calendarEl.getAttribute('data-guild-slug');
      eventsUrl = '/guilds/'+guildSlug+'/events/json';
      addEventUrl = '/guilds/'+guildSlug+'/events/add';
      btnRight = 'dayGridMonth,listMonth,addEventButton';
    }

    var addBtn = calendarEl.getAttribute('data-add-event');

    var calendar = new Calendar(calendarEl, {
      plugins: [ interactionPlugin, dayGridPlugin, listPlugin ],
      customButtons: {
        addEventButton: {
          text: addBtn,
          click: function() {
            window.location.href = addEventUrl;
          }
        }
      },
      header: {
        left: 'prev,next today',
        center: 'title',
        right: btnRight
      },
      navLinks: true,
      eventLimit: true,
      events: {
        url: eventsUrl,
        color: '#b8810e'
      },
      eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        meridiem: false,
        hour12: false
      }
    });

    calendar.render();
  });
}
