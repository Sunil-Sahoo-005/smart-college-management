@extends('hr.layout')

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        .fc-event { cursor: pointer; padding: 2px 4px; border-radius: 4px; border: none; }
        .fc-daygrid-event { white-space: normal !important; }
        .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700; }
        .fc-button-primary { background-color: #4e73df !important; border-color: #4e73df !important; }
        #holidayCalendar { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .holiday-active { background-color: #1cc88a !important; }
        .holiday-inactive { background-color: #858796 !important; }
        .holiday-cancelled { background-color: #e74a3b !important; }
        .view-toggle-btn.active { background-color: #4e73df; color: #fff; border-color: #4e73df; }
    </style>
@endpush

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm mb-2" style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Holidays</h5>
                        <small class="text-muted">Academic Holidays Calendar</small>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0 flex-wrap">
                        <div class="btn-group btn-group-sm me-2">
                            <button type="button" class="btn btn-outline-primary view-toggle-btn active" id="listViewBtn">
                                <i class="feather-list me-1"></i> List
                            </button>
                            <button type="button" class="btn btn-outline-primary view-toggle-btn" id="calendarViewBtn">
                                <i class="feather-calendar me-1"></i> Calendar
                            </button>
                        </div>

                        <div class="vr mx-1" style="height:24px;"></div>

                        <select id="filter_status" class="form-select form-select-sm" style="width:auto; min-width:130px;">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="content-area-body p-4">
                <div id="listView">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Holiday Name</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listTbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="calendarView" class="d-none">
                    <div id="holidayCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ALL_DATA = @json($holidaysJson);
</script>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterStatus = document.getElementById('filter_status');
    const listTbody = document.getElementById('listTbody');
    const listView = document.getElementById('listView');
    const calendarView = document.getElementById('calendarView');
    const listViewBtn = document.getElementById('listViewBtn');
    const calendarViewBtn = document.getElementById('calendarViewBtn');
    let calendar = null;

    function renderTable() {
        const status = filterStatus.value;
        const filtered = ALL_DATA.filter(h => !status || h.status === status);
        
        const statusMap = {
            active: '<span class="badge bg-success">Active</span>',
            inactive: '<span class="badge bg-secondary">Inactive</span>',
            cancelled: '<span class="badge bg-danger">Cancelled</span>',
        };

        listTbody.innerHTML = filtered.map(h => `
            <tr>
                <td class="fw-medium">${h.name}</td>
                <td>${h.date_formatted}</td>
                <td class="text-muted">${h.description}</td>
                <td>${statusMap[h.status] || h.status}</td>
            </tr>
        `).join('');
    }

    function initCalendar() {
        if (calendar) return;
        const calendarEl = document.getElementById('holidayCalendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,listMonth'
            },
            height: 'auto',
            events: function(info, successCallback) {
                const status = filterStatus.value;
                const events = ALL_DATA
                    .filter(h => !status || h.status === status)
                    .map(h => ({
                        title: h.name,
                        start: h.date,
                        className: h.status === 'active' ? 'holiday-active' : (h.status === 'inactive' ? 'holiday-inactive' : 'holiday-cancelled'),
                        extendedProps: { description: h.description }
                    }));
                successCallback(events);
            },
            eventDidMount: function(info) {
                info.el.title = info.event.title + (info.event.extendedProps.description ? ': ' + info.event.extendedProps.description : '');
            }
        });
        calendar.render();
    }

    filterStatus.addEventListener('change', () => {
        renderTable();
        if (calendar) calendar.refetchEvents();
    });

    listViewBtn.addEventListener('click', () => {
        listViewBtn.classList.add('active');
        calendarViewBtn.classList.remove('active');
        listView.classList.remove('d-none');
        calendarView.classList.add('d-none');
    });

    calendarViewBtn.addEventListener('click', () => {
        calendarViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        calendarView.classList.remove('d-none');
        listView.classList.add('d-none');
        initCalendar();
    });

    renderTable();
});
</script>
@endpush
@endsection
