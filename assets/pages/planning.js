import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
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
    const mode = calendarEl.dataset.mode || 'view'
    const toggleUrl = calendarEl.dataset.toggleUrl
    const csrfToken = calendarEl.dataset.csrfToken
    const input = calendarEl.dataset.inputId
        ? document.getElementById(calendarEl.dataset.inputId)
        : null
    const feedback = document.getElementById('planning-feedback')
    let availableDates = new Set()

    const markSelectedDay = (date) => {
        calendarEl.querySelectorAll('.fc-daygrid-day').forEach((day) => {
            day.classList.toggle('planning-day-selected', day.dataset.date === date)
        })
    }

    const selectDay = (date) => {
        if (!input || !availableDates.has(date)) {
            return
        }

        input.value = date
        input.dispatchEvent(new Event('change', { bubbles: true }))
        markSelectedDay(date)
    }

    const toggleDay = async (date) => {
        if (!toggleUrl || !csrfToken) {
            return
        }

        calendarEl.classList.add('planning-calendar--loading')

        try {
            const response = await fetch(toggleUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ date, _token: csrfToken })
            })
            const payload = await response.json()

            if (!response.ok) {
                throw new Error(payload.message || 'La disponibilité n’a pas pu être enregistrée.')
            }

            if (feedback) {
                feedback.textContent = payload.message
                feedback.className = 'alert alert-success rounded-4 mt-3 mb-0'
            }

            calendar.refetchEvents()
        } catch (error) {
            if (feedback) {
                feedback.textContent = error.message
                feedback.className = 'alert alert-danger rounded-4 mt-3 mb-0'
            }
        } finally {
            calendarEl.classList.remove('planning-calendar--loading')
        }
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [
            dayGridPlugin,
            interactionPlugin,
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
        selectable: mode !== 'view',

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

        eventsSet: function (events) {
            availableDates = new Set(events.map((event) => event.startStr.slice(0, 10)))

            if (input?.value) {
                markSelectedDay(input.value)
            }
        },

        dateClick: function (info) {
            if (mode === 'admin') {
                toggleDay(info.dateStr)
            } else if (mode === 'select') {
                selectDay(info.dateStr)
            }
        },

        eventClick: function (info) {
            const date = info.event.startStr.slice(0, 10)

            if (mode === 'admin') {
                toggleDay(date)
            } else if (mode === 'select') {
                selectDay(date)
            }
        },

        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        eventDidMount: function (info) {
            info.el.style.whiteSpace = 'normal'
            info.el.style.height = 'auto'
            info.el.style.cursor = mode === 'view' ? 'default' : 'pointer'

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

    if (input) {
        input.addEventListener('change', () => markSelectedDay(input.value))
    }
})
