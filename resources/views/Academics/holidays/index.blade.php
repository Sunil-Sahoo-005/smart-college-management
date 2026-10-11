@extends('admin_layout')

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        .fc-event { cursor: pointer; padding: 2px 4px; border-radius: 4px; border: none; }
        .fc-daygrid-event { white-space: normal !important; }
        .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700; }
        .fc-button-primary { background-color: #4e73df !important; border-color: #4e73df !important; }
        .fc-button-primary:hover { background-color: #2e59d9 !important; border-color: #2e59d9 !important; }
        .fc-button-active { background-color: #2e59d9 !important; border-color: #2e59d9 !important; }
        #holidayCalendar { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .view-toggle-btn { transition: all 0.2s; }
        .view-toggle-btn.active { background-color: #4e73df; color: #fff; border-color: #4e73df; }
        .holiday-active { background-color: #1cc88a !important; }
        .holiday-inactive { background-color: #858796 !important; }
        .holiday-cancelled { background-color: #e74a3b !important; }
    </style>
@endpush

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm mb-2"
                 style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">

                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Holidays List</h5>
                        <small class="text-muted">
                            Showing <span id="visibleCount">{{ count($holidaysJson) }}</span>
                            of {{ count($holidaysJson) }} holidays
                        </small>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0 flex-wrap">
                        {{-- View Toggle --}}
                        <div class="btn-group btn-group-sm me-2" role="group">
                            <button type="button" class="btn btn-outline-primary view-toggle-btn active" id="listViewBtn">
                                <i class="feather-list me-1"></i> List
                            </button>
                            <button type="button" class="btn btn-outline-primary view-toggle-btn" id="calendarViewBtn">
                                <i class="feather-calendar me-1"></i> Calendar
                            </button>
                        </div>

                        <div class="vr mx-1" style="height:24px;"></div>

                        {{-- Status filter --}}
                        <select id="filter_status" class="form-select form-select-sm filter-select"
                            style="width:auto; min-width:130px;">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        @if (auth()->user()->hasPermissionTo('Create Holidays'))
                            <a href="{{ route('admin.holidays.create') }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span class="d-none d-sm-inline">Add Holiday</span>
                            </a>
                        @endif
                    </div>

                </div>
            </div>

            {{-- CONTENT --}}
            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- List View --}}
                <div id="listView">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="fw-semibold">Holiday Name</th>
                                            <th class="fw-semibold">Date</th>
                                            <th class="fw-semibold">Description</th>
                                            <th class="fw-semibold">Status</th>
                                            <th class="text-end fw-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listTbody"></tbody>
                                </table>
                            </div>

                            <div id="tablePagination"
                                 class="p-3 border-top d-flex justify-content-between align-items-center gap-2 flex-wrap d-none">
                                <small class="text-muted" id="paginationInfo"></small>
                                <div class="d-flex gap-1" id="paginationBtns"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Calendar View --}}
                <div id="calendarView" class="d-none">
                    <div id="holidayCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ALL_DATA   = @json($holidaysJson);
const CSRF_TOKEN = '{{ csrf_token() }}';
const CAN_EDIT   = {{ auth()->user()->hasPermissionTo('Edit Holidays')   ? 'true' : 'false' }};
const CAN_DELETE = {{ auth()->user()->hasPermissionTo('Delete Holidays') ? 'true' : 'false' }};
</script>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- LIST VIEW LOGIC ---
    const PAGE_SIZE    = 20;
    let filtered       = [...ALL_DATA];
    let currentPage    = 1;

    const filterStatus  = document.getElementById('filter_status');
    const filterSelects = document.querySelectorAll('.filter-select');

    filterSelects.forEach(sel => sel.addEventListener('change', () => { 
        currentPage = 1; 
        applyFilters(); 
        if (calendar) {
            calendar.refetchEvents();
        }
    }));

    function applyFilters() {
        const status = filterStatus.value;
        filtered = ALL_DATA.filter(h => {
            if (status && h.status !== status) return false;
            return true;
        });
        document.getElementById('visibleCount').textContent = filtered.length;
        render();
    }

    function render() {
        const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PAGE_SIZE;
        const paged = filtered.slice(start, start + PAGE_SIZE);
        renderTable(paged, start);
        renderPagination(totalPages, start, paged.length);
    }

    function renderTable(paged, start) {
        const tbody      = document.getElementById('listTbody');
        const pagination = document.getElementById('tablePagination');

        if (paged.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5">No holidays found</td></tr>`;
            pagination.classList.add('d-none');
            return;
        }

        const statusMap = {
            active:    '<span class="badge bg-success">Active</span>',
            inactive:  '<span class="badge bg-secondary">Inactive</span>',
            cancelled: '<span class="badge bg-danger">Cancelled</span>',
        };

        tbody.innerHTML = paged.map(h => `
            <tr>
                <td><a href="${h.edit_url}" class="fw-medium">${esc(h.name)}</a></td>
                <td>${esc(h.date_formatted)}</td>
                <td class="text-muted">${esc(h.description)}</td>
                <td>${statusMap[h.status] ?? esc(h.status)}</td>
                <td>
                    <div class="hstack gap-2 justify-content-end">
                        ${CAN_EDIT ? `<a href="${h.edit_url}" class="avatar-text avatar-md text-warning"><i class="feather-edit-3"></i></a>` : ''}
                        ${CAN_DELETE ? `
                            <form action="${h.delete_url}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="avatar-text avatar-md border-0 bg-transparent text-danger"><i class="feather-trash-2"></i></button>
                            </form>` : ''}
                    </div>
                </td>
            </tr>`).join('');

        pagination.classList.remove('d-none');
        document.getElementById('paginationInfo').textContent = `Showing ${start + 1}–${Math.min(start + paged.length, filtered.length)} of ${filtered.length}`;
    }

    function renderPagination(totalPages, start, pagedLength) {
        const btnsEl = document.getElementById('paginationBtns');
        if (totalPages <= 1) { btnsEl.innerHTML = ''; return; }
        let html = '';
        for (let p = 1; p <= totalPages; p++) {
            html += `<button class="btn btn-sm ${p === currentPage ? 'btn-primary' : 'btn-outline-secondary'}" data-page="${p}">${p}</button>`;
        }
        btnsEl.innerHTML = html;
        btnsEl.querySelectorAll('[data-page]').forEach(btn => btn.addEventListener('click', function() {
            currentPage = parseInt(this.dataset.page);
            render();
        }));
    }

    function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // --- VIEW TOGGLE LOGIC ---
    const listViewBtn = document.getElementById('listViewBtn');
    const calendarViewBtn = document.getElementById('calendarViewBtn');
    const listView = document.getElementById('listView');
    const calendarView = document.getElementById('calendarView');
    let calendar = null;

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
        
        if (!calendar) {
            initCalendar();
        } else {
            calendar.render();
        }
    });

    function initCalendar() {
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
            events: function(info, successCallback, failureCallback) {
                const status = filterStatus.value;
                const events = ALL_DATA
                    .filter(h => !status || h.status === status)
                    .map(h => {
                        let className = 'holiday-active';
                        if (h.status === 'inactive') className = 'holiday-inactive';
                        if (h.status === 'cancelled') className = 'holiday-cancelled';

                        return {
                            id: h.id,
                            title: h.name,
                            start: h.date,
                            className: className,
                            extendedProps: {
                                description: h.description,
                                edit_url: h.edit_url
                            }
                        };
                    });
                successCallback(events);
            },
            eventDidMount: function(info) {
                // Add tooltip or description
                info.el.title = info.event.title + (info.event.extendedProps.description ? ': ' + info.event.extendedProps.description : '');
            },
            eventClick: function(info) {
                if (CAN_EDIT) {
                    window.location.href = info.event.extendedProps.edit_url;
                }
            }
        });
        calendar.render();
    }
    
    // I need the raw date in holidaysJson. Let's update the controller.
    applyFilters();
});
</script>

@endsection