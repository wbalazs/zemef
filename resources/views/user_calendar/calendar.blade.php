@extends('layouts.app', ['activePage' => '', 'activeButton' => '', 'title' => 'Zemef ERP', 'navName' => 'Naptár' ])

@section('content')

<style>
    /* FullCalendar alap stílusok */
/* A hét napjait jelölő fejléc stílusainak testreszabása */
.fc .fc-col-header-cell-cushion {
    color: black;
    padding: 10px 0;
    font-weight: bold;
    text-transform: uppercase;
}

.fc .fc-highlight {
    background: #2a87d0;
    opacity: 0.3;
    border: 2px dashed white;
}

.fc .fc-col-header-cell:hover .fc-col-header-cell-cushion {
    cursor: pointer;
}

.fc .fc-daygrid-day {
    border: 1px solid #b1aeae;
}

.fc .fc-daygrid-day-number {
    color: black;
}

.fc .fc-toolbar-title {
    color: #333333;
    font-size: 26px;
}

.fc .fc-button {
    color: #333333;
    background-color: #f8f9fa;
    border-color: #f8f9fa;
}

.fc .fc-button:hover {
    color: #333333;
    background-color: #e2e6ea;
    border-color: #dae0e5;
}

.fc .fc-button:active {
    color: #333333;
    background-color: #e2e6ea;
    border-color: #dae0e5;
}

.fc .fc-button.fc-button-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.fc .fc-button.fc-button-primary:hover {
    color: #fff;
    background-color: #0069d9;
    border-color: #0062cc;
}
</style>
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h3 class="mb-0">Naptár</h3>
                        </div>
                    </div>
                </div>
                    <form action="{{ route('holiday.store') }}" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="col-12">
                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<!-- Esemény Hozzáadása Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalLabel">Új esemény hozzáadása</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Esemény form -->
          <form id="eventForm">
            <div class="form-group">
              <label for="eventTitle">Esemény címe</label>
              <input type="text" class="form-control" id="eventTitle" required>
            </div>
            <div class="form-group">
              <label for="eventStart">Kezdés dátuma</label>
              <input type="date" class="form-control" id="eventStart" required>
            </div>
            <div class="form-group">
              <label for="eventEnd">Befejezés dátuma</label>
              <input type="date" class="form-control" id="eventEnd" required>
            </div>
            <div class="form-group">
              <label for="eventColor">Szín</label>
              <input type="color" class="form-control" id="eventColor" value="#001eff" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Mentés</button>
          </form>
        </div>
      </div>
    </div>
</div>  
<!-- Esemény szerkesztése Modal -->
<div class="modal fade" id="eventModalEdit" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalLabel">Esemény szerkesztése</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Esemény form -->
          <form id="eventFormEdit">
            <div class="form-group">
              <label for="eventTitle">Esemény címe</label>
              <input type="hidden" id="eventIdEdit">
              <input type="text" class="form-control" id="eventTitleEdit" required>
            </div>
            <div class="form-group">
              <label for="eventStart">Kezdés dátuma</label>
              <input type="date" class="form-control" id="eventStartEdit" required>
            </div>
            <div class="form-group">
              <label for="eventEnd">Befejezés dátuma</label>
              <input type="date" class="form-control" id="eventEndEdit" required>
            </div>
            <div class="form-group">
              <label for="eventColor">Szín</label>
              <input type="color" class="form-control" id="eventColorEdit" value="#001eff" required>
            </div>
            <div class="row">
                <div class="col-10">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Mentés</button>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger delete_event"><i class="fa fa-trash"></i></button>
                </div>  
            </div>
          </form>
        </div>
      </div>
    </div>
</div>  
@endsection

@push('js')
<script type="text/javascript">

    $(document).ready(function () {
        let calendarEl = document.getElementById('calendar');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'hu',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            editable: true,
            eventDrop: function(info) {
                updateEvent(info.event);
            },
            eventResize: function(info) {
                updateEvent(info.event);
            },
            selectable: true,
            select: function(info) {
                let end = info.end;
                end = new Date(end.valueOf());
                end.setDate(end.getDate() - 1);
                let endStr = end.getFullYear() + '-' + 
                    (end.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                    end.getDate().toString().padStart(2, '0');
                $('#eventModal').find('#eventStart').val(info.startStr);
                $('#eventModal').find('#eventEnd').val(endStr.trim());
                $('#eventModal').modal('show');
            },
            dateClick: function(info) {
                $('#eventModal').find('#eventStart').val(info.dateStr);
                $('#eventModal').find('#eventEnd').val(info.dateStr);
                $('#eventModal').modal('show');
            },
            eventClick: function(info) {
                $('#eventModalEdit').find('#eventIdEdit').val(info.event.id);
                $('#eventModalEdit').find('#eventTitleEdit').val(info.event.title);
                $('#eventModalEdit').find('#eventStartEdit').val(info.event.start.toISOString().slice(0, 10));
                $('#eventModalEdit').find('#eventEndEdit').val(info.event.end.toISOString().slice(0, 10));
                $('#eventModalEdit').find('#eventColorEdit').val(info.event.backgroundColor);
                $('#eventModalEdit').modal('show');
            },
            events: '/getCalendarEvents'
        });

        calendar.render();


        $('.delete_event').on('click', function() {
            let id = $('#eventIdEdit').val();
            $.ajax({
                url: '/deleteCalendarEvent/' + id,
                method: 'POST',
                success: function(response) {
                    if(response.success){
                        let event = calendar.getEventById(id);
                        if (event) {
                            event.remove();
                        }
                        $.notify({ message: response.message }, { type: 'success', timer: 3000 });
                        $('#eventModalEdit').modal('hide');
                    }else{
                        $.notify({ message: response.message }, { type: 'danger', timer: 3000 });
                    }
                }
            });
        });


        $('#eventForm').on('submit', function(e) {
            e.preventDefault();
            
            let title = $('#eventTitle').val();
            let start = $('#eventStart').val();
            let end = $('#eventEnd').val();
            let color = $('#eventColor').val();

            $.ajax({
                url: '/saveCalendarEvent',
                method: 'POST',
                data: {
                    title: title,
                    start: start,
                    end: end,
                    color: color
                },
                success: function(response) {
                    if(response.success){
                            calendar.addEvent({
                            id: response.event.id,
                            title: response.event.title,
                            start: response.event.start,
                            end: response.event.end,
                            color: response.event.color
                        });
                        $.notify({ message: response.message }, { type: 'success', timer: 3000 });
                    }else{
                        $.notify({ message: response.message }, { type: 'danger', timer: 3000 });
                    }
                    $('#eventModal').modal('hide');
                }
            });
        });

        $('#eventFormEdit').on('submit', function(e) {
            e.preventDefault();
            let id = $('#eventIdEdit').val();
            let title = $('#eventTitleEdit').val();
            let start = $('#eventStartEdit').val();
            let end = $('#eventEndEdit').val();
            let color = $('#eventColorEdit').val();
            updateEventFull(id,start,end,color,title);
        });


        function updateEventFull(id,start,end,color,title) {
            $.ajax({
                url: '/updateCalendarEventFull/' + id,
                method: 'POST',
                data: {
                    start: start,
                    end: end,
                    color: color,
                    title: title
                },
                success: function(response) {
                    if(response.success){
                        var event = calendar.getEventById(id);
                        if (event) {
                            event.setProp('title', title);
                            event.setProp('start', start);
                            event.setProp('end', end);
                            event.setExtendedProp('color', color);
                            
                            let s_end = new Date(end.valueOf());
                            s_end.setDate(s_end.getDate() + 1);
                            let endStr = s_end.getFullYear() + '-' + 
                                (s_end.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                                s_end.getDate().toString().padStart(2, '0');
                            event.setDates(start, endStr);
                            event.setProp('backgroundColor', color);
                            event.setProp('borderColor', color);
                        }
                        $.notify({ message: response.message }, { type: 'success', timer: 3000 });
                        $('#eventModalEdit').modal('hide');
                    }else{
                        $.notify({ message: response.message }, { type: 'danger', timer: 3000 });
                    }
                }
            });
        }

    });

    function updateEvent(event) {
        let startStr = event.start.toISOString();
        let endStr = event.end.toISOString();

        $.ajax({
            url: '/updateCalendarEvent/' + event.id,
            method: 'POST',
            data: {
                start: startStr,
                end: endStr,
            },
            success: function(response) {
                if(response.success){
                    $.notify({ message: response.message }, { type: 'success', timer: 3000 });
                }else{
                    $.notify({ message: response.message }, { type: 'danger', timer: 3000 });
                    event.revert();
                }
            }
        });
    }

    function convertUTCDateToLocalDate(date) {
        let newDate = new Date(date.getTime() + date.getTimezoneOffset() * 60 * 1000);
        
        let offset = date.getTimezoneOffset() / 60;
        let hours = date.getHours();
        
        newDate.setHours(hours - offset);
    
        return newDate;   
    }
          
        
        
    </script>
@endpush