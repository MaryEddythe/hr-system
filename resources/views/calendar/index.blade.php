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
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 1.5rem;
        align-items: start;
    }
    .calendar-panel,
    .calendar-sidebar {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .calendar-panel {
        padding: 1.25rem;
        min-width: 0;
    }
    .calendar-sidebar {
        padding: 1.25rem;
    }
    .sidebar-section {
        margin-bottom: 1.5rem;
    }
    .sidebar-section:last-child {
        margin-bottom: 0;
    }
    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding-bottom: 0.85rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }
    .section-title:hover {
        color: #334155;
    }
    .section-toggle {
        font-size: 1.2rem;
        color: #64748b;
        transition: transform 0.2s;
    }
    .section-toggle.open {
        transform: rotate(180deg);
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
    .section-content {
        display: none;
    }
    .section-content.open {
        display: block;
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
    .success-message {
        background: #d1fae5;
        color: #065f46;
        padding: 0.75rem;
        border-radius: 4px;
        margin-bottom: 1rem;
        display: none;
        font-size: 0.85rem;
    }
    .success-message.show {
        display: block;
    }
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .calendar-header {
        flex: 1;
        min-width: 300px;
    }
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
        letter-spacing: 0.3px;
    }
    .btn-primary {
        background: #0066cc;
        color: white;
    }
    .btn-primary:hover {
        background: #0052a3;
    }
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-content {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header {
        padding: 1.75rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #64748b;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }
    .modal-close:hover {
        color: #0f172a;
    }
    .modal-body {
        padding: 1.75rem;
    }
    .modal-footer {
        padding: 1.75rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }
    .modal-form-group {
        margin-bottom: 1.25rem;
    }
    .modal-form-group label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    .modal-form-group input,
    .modal-form-group textarea,
    .modal-form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        font-family: inherit;
        font-size: 0.9rem;
        color: #111827;
        background: white;
    }
    .modal-form-group input:focus,
    .modal-form-group textarea:focus,
    .modal-form-group select:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }
    .modal-form-group textarea {
        resize: vertical;
        min-height: 80px;
    }
    .modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .modal-btn-primary {
        background: #0066cc;
        color: white;
    }
    .modal-btn-primary:hover {
        background: #0052a3;
    }
    .modal-btn-secondary {
        background: #e2e8f0;
        color: #334155;
    }
    .modal-btn-secondary:hover {
        background: #cbd5e1;
    }
    @media (max-width: 1024px) {
        .calendar-shell {
            grid-template-columns: 1fr;
        }
        .calendar-sidebar {
            order: -1;
        }
    }
    @media (max-width: 768px) {
        #calendar {
            min-height: 620px;
        }
        .calendar-panel,
        .calendar-sidebar {
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
    <button onclick="openEventModal()" class="btn btn-primary">+ Add Event</button>
</div>

<div class="calendar-shell">
    <div class="calendar-panel">
        <div id="calendar"></div>
    </div>

    {{--  --}}
</div>

<!-- Add Event Modal -->
<div class="modal-overlay" id="eventModalOverlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Create New Event</h2>
            <button class="modal-close" onclick="closeEventModal()">×</button>
        </div>
        <form id="modalEventForm">
            @csrf
            <div class="modal-body">
                <div class="modal-form-group">
                    <label for="modalEventTitle">Title</label>
                    <input type="text" id="modalEventTitle" name="title" required placeholder="Event title">
                </div>
                <div class="modal-form-group">
                    <label for="modalEventType">Event Type</label>
                    <select id="modalEventType" name="type" required>
                        <option value="">Select event type...</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label for="modalEventStartDate">Start Date</label>
                    <input type="date" id="modalEventStartDate" name="start_date" required>
                </div>
                <div class="modal-form-group">
                    <label for="modalEventEndDate">End Date</label>
                    <input type="date" id="modalEventEndDate" name="end_date" required>
                </div>
                <div class="modal-form-group">
                    <label for="modalEventDescription">Description</label>
                    <textarea id="modalEventDescription" name="description" placeholder="Event description"></textarea>
                </div>
                <div class="modal-form-group">
                    <label for="modalEventRemarks">Remarks</label>
                    <textarea id="modalEventRemarks" name="remarks" placeholder="Additional remarks"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-secondary" onclick="closeEventModal()">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-primary">Create Event</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    let calendar;

    // Color mapping for event types
    const typeColors = {
        'Travel Order': '#2563eb',
        'Event': '#16a34a',
        'Birthday': '#db2777'
    };

    // Open event modal
    function openEventModal() {
        document.getElementById('eventModalOverlay').classList.add('active');
    }

    // Close event modal
    function closeEventModal() {
        document.getElementById('eventModalOverlay').classList.remove('active');
        document.getElementById('modalEventForm').reset();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('eventModalOverlay');
        if (event.target == modal) {
            closeEventModal();
        }
    }

    // Fetch event types from database
    async function fetchEventTypes() {
        try {
            const response = await fetch('/api/events/types');
            return await response.json();
        } catch (error) {
            console.error('Error fetching event types:', error);
            return ['Travel Order', 'Event', 'Birthday'];
        }
    }

    // Populate event type dropdown
    async function populateEventTypeDropdown() {
        const select = document.getElementById('modalEventType');
        const types = await fetchEventTypes();
        
        types.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            select.appendChild(option);
        });
    }

    // Fetch events from database
    async function fetchEvents() {
        try {
            const response = await fetch('/api/events');
            const events = await response.json();
            return events.map(event => ({
                title: event.title,
                start: event.start_date,
                end: new Date(new Date(event.end_date).getTime() + 24*60*60*1000).toISOString().split('T')[0],
                color: typeColors[event.type] || '#16a34a',
                extendedProps: {
                    type: event.type,
                    description: event.description,
                    remarks: event.remarks,
                    id: event.id
                }
            }));
        } catch (error) {
            console.error('Error fetching events:', error);
            return [];
        }
    }

    // Initialize calendar
    document.addEventListener('DOMContentLoaded', async function () {
        // Populate event type dropdown
        await populateEventTypeDropdown();

        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();
        const pad = (value) => String(value).padStart(2, '0');
        const dateFor = (day) => `${year}-${pad(month + 1)}-${pad(day)}`;

        // Fetch custom events from database
        const customEvents = await fetchEvents();

        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
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
            events: customEvents
        });

        calendar.render();
    });

    // Handle modal form submission
    document.getElementById('modalEventForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = {
            title: document.getElementById('modalEventTitle').value,
            start_date: document.getElementById('modalEventStartDate').value,
            end_date: document.getElementById('modalEventEndDate').value,
            description: document.getElementById('modalEventDescription').value,
            remarks: document.getElementById('modalEventRemarks').value,
            type: document.getElementById('modalEventType').value,
            _token: document.querySelector('input[name="_token"]').value
        };

        try {
            const response = await fetch('/api/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData._token
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                // Close modal
                closeEventModal();

                // Reload calendar events
                const events = await fetchEvents();
                calendar.getEvents().forEach(event => {
                    if (event.extendedProps.id) {
                        event.remove();
                    }
                });
                events.forEach(event => calendar.addEvent(event));
            } else {
                alert('Error creating event');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error creating event');
        }
    });
</script>
@endsection
