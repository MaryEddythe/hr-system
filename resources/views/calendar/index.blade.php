@extends('layouts.app')
@section('title', 'Calendar')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<style>
    .calendar-header {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .calendar-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 1.5rem;
        align-items: start;
    }
    .calendar-panel,
    .calendar-agenda {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .calendar-panel {
        padding: 1.25rem;
        min-width: 0;
    }
    .calendar-agenda {
        padding: 1.25rem;
    }
    .agenda-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding-bottom: 0.85rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .legend-list {
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }
    .legend-item {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        font-size: 0.88rem;
        color: #475569;
        font-weight: 600;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex: 0 0 auto;
        margin-top: 0.3rem;
    }
    #calendar {
        min-height: 700px;
    }
    .fc {
        color: #1f2937;
        font-family: inherit;
    }
    .fc .fc-toolbar {
        gap: 0.8rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .fc .fc-toolbar-title {
        color: #0f172a;
        font-size: 1.35rem;
        font-weight: 700;
    }
    .fc .fc-button-primary {
        background: #0066cc;
        border-color: #0066cc;
        border-radius: 5px;
        box-shadow: none;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-size: 0.75rem;
    }
    .fc .fc-button-primary:hover,
    .fc .fc-button-primary:focus {
        background: #0052a3;
        border-color: #0052a3;
        box-shadow: none;
    }
    .fc .fc-daygrid-day-number,
    .fc .fc-col-header-cell-cushion {
        color: #334155;
        text-decoration: none;
        font-weight: 700;
    }
    .fc .fc-day-today {
        background: #eff6ff;
    }
    .fc-event {
        border: none;
        border-radius: 4px;
        padding: 2px 5px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .fc-event-title {
        white-space: normal;
    }
    @media (max-width: 1024px) {
        .calendar-shell {
            grid-template-columns: 1fr;
        }
        .calendar-agenda {
            order: -1;
        }
    }
    @media (max-width: 768px) {
        #calendar {
            min-height: 620px;
        }
        .calendar-panel,
        .calendar-agenda {
            padding: 1rem;
        }
        .fc .fc-toolbar {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="page-header">
    <div class="calendar-header">
        <div class="page-title">Calendar</div>
        <div class="page-subtitle">Monthly view for travel orders, events, birthdays, and tasks.</div>
    </div>
</div>

<div class="calendar-shell">
    <div class="calendar-panel">
        <div id="calendar"></div>
    </div>

    <aside class="calendar-agenda">
        <div class="agenda-title">Calendar Types</div>
        <div class="legend-list">
            <div class="legend-item"><span class="legend-dot" style="background:#2563eb"></span>Travel Orders</div>
            <div class="legend-item"><span class="legend-dot" style="background:#16a34a"></span>Events</div>
            <div class="legend-item"><span class="legend-dot" style="background:#db2777"></span>Birthdays</div>
            <div class="legend-item"><span class="legend-dot" style="background:#f59e0b"></span>Tasks</div>
        </div>
    </aside>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();
        const pad = (value) => String(value).padStart(2, '0');
        const dateFor = (day) => `${year}-${pad(month + 1)}-${pad(day)}`;

        const calendarEvents = [
            {
                title: 'Travel Order: Field Visit',
                start: dateFor(4),
                end: dateFor(6),
                color: '#2563eb',
                extendedProps: { type: 'Travel Order' }
            },
            {
                title: 'Monthly HR Meeting',
                start: dateFor(10),
                color: '#16a34a',
                extendedProps: { type: 'Event' }
            },
            {
                title: 'Birthday: Sample Employee',
                start: dateFor(15),
                color: '#db2777',
                extendedProps: { type: 'Birthday' }
            },
            {
                title: 'Submit Payroll Documents',
                start: dateFor(21),
                color: '#f59e0b',
                extendedProps: { type: 'Task' }
            }
        ];

        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: today,
            height: 'auto',
            nowIndicator: true,
            dayMaxEvents: 3,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            events: calendarEvents
        });

        calendar.render();
    });
</script>
@endsection
