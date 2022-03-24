<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Full Calendar 3IL
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container mt-4">
        <div id='fullCalendar'></div>
    </div>

    <script>
        $(document).ready(function () {

            var endpoint = "{{ url('/') }}";
            var infos_user=null;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var calendar = $('#fullCalendar').fullCalendar({
                editable: true,
                editable: true,
                events: endpoint + "/show-event-calendar",
                displayEventTime: true,
                eventRender: function (event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }


                },
                selectable: true,
                selectHelper: true,
                select: function (event_start, event_end, allDay) {

                    var event_title = prompt('Event Name:');
                    var formath=false;

                    if(event_title === null)
                        formath = true;
                    var heur_validation='[0-9]\{2\}:[0-9]\{2\}$';
                    while(!formath){
                        var heur_deb=prompt('Heure de debut (hh:mm)');

                   if(heur_deb.match(heur_validation)){
                       var heur=parseInt(heur_deb.split(':')[0]);
                       var minutes=parseInt(heur_deb.split(':')[1])
                       if(heur<24 && minutes <60){
                           var heur_fin=prompt('Heure de fin (hh:mm)');
                           if(heur_fin.match(heur_validation)){
                               var heur=parseInt(heur_fin.split(':')[0]);
                               var minutes=parseInt(heur_fin.split(':')[1])
                               if(heur<24 && minutes <60){
                                   formath=true;
                               }
                           }
                       }
                   }
                   if(!formath){
                       alert('le format de l\'heure doit être hh:mm');
                   }
                    }


                    if (event_title && heur_deb && heur_fin) {
                        var event_start = $.fullCalendar.formatDate(event_start, "Y-MM-DD HH:mm:ss");
                        var event_end = $.fullCalendar.formatDate(event_end, "Y-MM-DD HH:mm:ss");
                        $.ajax({
                            url: endpoint + "/manage-events",
                            data: {
                                event_title: event_title,
                                event_start: event_start,
                                event_end: event_end,
                                heure_deb:heur_deb,
                                heure_fin:heur_fin,
                                type: 'create'
                            },
                            type: "POST",
                            success: function (data) {
                                displayMessage("Event created.");

                                calendar.fullCalendar('renderEvent', {
                                    id: data.id,
                                    title: event_title + ' ('+ heur_deb+ ' a '+heur_fin+' )',
                                    start: event_start,
                                    end: event_end,
                                    allDay: allDay
                                }, true);
                                calendar.fullCalendar('unselect');
                            }
                        });
                    }
                },
                eventDrop: function (event, delta) {

                    var event_start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    var event_end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

                    $.ajax({
                        url: endpoint + '/manage-events',
                        data: {
                            title: event.title,
                            start: event_start,
                            end: event_end,
                            id: event.id,
                            type: 'edit'
                        },
                        type: "POST",
                        success: function (response) {
                            displayMessage("Event updated");
                        }
                    });
                },
                eventClick: function (event) {
                    var removeEvent = confirm("Really?");
                    if (removeEvent) {
                        $.ajax({
                            type: "POST",
                            url: endpoint + '/manage-events',
                            data: {
                                id: event.id,
                                type: 'delete'
                            },success: function (response) {
                                calendar.fullCalendar('removeEvents', event.id);
                                displayMessage("Event deleted");
                            }
                        });
                    }
                },
                eventResize: function (event) {
                    var event_start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    var event_end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");
                        $.ajax({
                            type: "POST",
                            url: endpoint + '/manage-events',
                            data: {
                                start: event_start,
                                end: event_end,
                                id: event.id,
                                type: 'resize'
                            },success: function (response) {
                                displayMessage("Event resized");
                            }
                        });

                }
            });
            $.ajax({
                url: endpoint + '/get_infos_user',
                        type: "GET",
                        success: function (response) {
                            infos_user=JSON.parse(response);
                            console.log(infos_user)
                            for(var i=0; i<infos_user.length;i++){
                        calendar.fullCalendar('renderEvent', {
                                    id: infos_user[i].id,
                                    title: infos_user[i].event_title+ ' ('+ infos_user[i].heure_deb+ ' a '+infos_user[i].heure_fin+' )',
                                    start: infos_user[i].event_start,
                                    end: infos_user[i].event_end,

                                }, true);
                    }
                        }
            })
        });

        function displayMessage(message) {
            toastr.success(message, 'Event');
        }
    </script>
            </div>
        </div>
    </div>
</x-app-layout>
