<script src='<?php echo base_url("assets/modules/fullcalendar/locales/all.js")?>'></script>
<script>
   var user_id_url = '<?php echo $user_id_url;?>';
    document.addEventListener('DOMContentLoaded', function() {
       $('[data-toggle="popover"]').popover();
       var isDashboard = '<?php echo $this->uri->segment(1); ?>';
      	var calendarEl = document.getElementById('calendar');
      	var loads = 'dayGridMonth,listWeek,dayGridDay';
      	var intialViews = '';
       if(isDashboard==="dashboard") {
       	calendarEl = document.getElementById('dashboard_calendar');
       	intialViews = 'listWeek'
       	loads = 'listWeek,dayGridDay';
       }

       var calendar = new FullCalendar.Calendar(calendarEl, {
       	   locale: 'all',
           direction : '<?php echo $is_rtl ? "rtl" : "ltr";?>',
           views: {
             dayGridMonth: {
               dayMaxEventRows: 5
             },
             timeGrid: {
               dayMaxEventRows: 1
             }
           },
           contentHeight: 650,
           themeSystem: 'bootstrap',
           editable:true,
           droppable:true,
           headerToolbar: {
               start: 'prev,next,today',
               center: 'title',
               end: loads
           },
           initialView: intialViews,
           eventMouseEnter: function(mouseEnterInfo) {
               $(mouseEnterInfo.el).popover({
                   placement:'top',
                   trigger : 'hover',
                   content: mouseEnterInfo.event.extendedProps.description,
                   container:'body',
                   html:true
               }).popover('show');
           	},
           	eventDidMount: function(info) {
           		$(info.el).parent().parent().parent().parent().addClass("has_events");
           		$(info.el).parent().parent().parent().parent().parent().addClass("event_row");
           	},
           	eventTimeFormat: { 
              hour: '2-digit',
              minute: '2-digit',
              meridiem: 'short'
           },
           events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: base_url+'calendar/new_full_calendar',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                        user_id_url: user_id_url
                    },
                    success: function(doc) {
                        var events = [];
                        if(!!doc){
                            $.each(Object.values(doc.data), function( index,value ) {
                                events.push({
                                    title: value.title,
                                    description: value.description,
                                    url: value.url,
                                    color: value.color,
                                    start: value.start,
                                    className: value.className,
                                });
                            });
                        }
                        successCallback(events);
                    }
                });
           },

            
        });
        calendar.render();
    });

</script>