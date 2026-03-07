import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import frLocale from '@fullcalendar/core/locales/fr'
import '../styles/planning.scss'

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar')

    if (!calendarEl) {
        return
    }

    const eventsUrl = calendarEl.dataset.eventsUrl

    const calendar = new Calendar(calendarEl, {
        plugins: [
            dayGridPlugin,
            timeGridPlugin,
            listPlugin
        ],

        locale: frLocale,
        firstDay: 1,
        initialView: 'dayGridMonth',

        height: 'auto',
        contentHeight: 'auto',
        expandRows: true,
        fixedWeekCount: false,
        dayMaxEventRows: false,
        dayMaxEvents: false,
        navLinks: false,
        selectable: false,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },

        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            list: 'Liste'
        },

        events: eventsUrl,

        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        eventDidMount: function (info) {
            info.el.style.whiteSpace = 'normal'
            info.el.style.height = 'auto'
            info.el.style.cursor = 'default'

            const titleEl = info.el.querySelector('.fc-event-title')
            if (titleEl) {
                titleEl.style.whiteSpace = 'normal'
                titleEl.style.lineHeight = '1.2'
            }

            const mainEl = info.el.querySelector('.fc-event-main')
            if (mainEl) {
                mainEl.style.whiteSpace = 'normal'
                mainEl.style.padding = '10px'
            }
        }
    })

    calendar.render()
})