<link rel="stylesheet" href="<?php echo base_url('plugins/jorgchartmaster/css/jquery.jOrgChart.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/jorgchartmaster/css/custom.css');?>"/>
<link href="<?php echo base_url('plugins/jorgchartmaster/css/prettify.css');?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url('plugins/jorgchartmaster/js/prettify.js');?>"></script>
<script src="<?php echo base_url('plugins/jorgchartmaster/js/jquery.jOrgChart.js');?>"></script>

<script>
$(document).ready(function() {
  $("#org").jOrgChart({
      chartElement : '#chart',
      dragAndDrop  : false
  });

  $(document).on('click','.iframed',function(e){
    e.preventDefault();
    var iframe_url = $(this).attr('href');
    var iframe_height = $(this).attr('data-height');
    $("iframe").attr('src',iframe_url);
    $("#iframe_modal").modal();
  });

  $(document).on('click','.zoomaction',function(e){
    e.preventDefault();
    var zoomaction = $(this).attr("id");
    var scale = parseFloat($("#scale").val());
    var new_scale = 0;
    var steps = .1;

    if(zoomaction=="zoomin") new_scale = scale+steps;
    else if(zoomaction=="zoomout") new_scale = scale-steps;
    else new_scale = 1;

    if(new_scale>=.20 && new_scale<=1)
    {
      $("#scale").val(new_scale);
      $(".jOrgChart").css('transform','scale('+new_scale+')');
      $(".jOrgChart").css('transform-origin','0 0');

      var percent = parseInt(new_scale*100);
      $("#percent").html(percent);

    }

  });


 

  $('#iframe_modal').on('hidden.bs.modal', function () { 
    location.reload();
  });
});  
</script>

<section class="section section_custom">

  <div class="section-header">
    <h1 style="font-style: normal !important;">
      <i class="fas fa-sitemap"></i> <?php echo $this->lang->line("Tree View"); ?> :
      <a href="https://facebook.com/<?php echo $page_info['page_id']; ?>" target="_BLANK"><?php echo $page_info['page_name']; ?></a>
    </h1>
    <div class="section-header-breadcrumb">
      <span id="percent">100</span>%&nbsp;&nbsp;
      <a href="" id="zoomin" class="zoomaction" data-toggle="tooltip" title="Zoom In"><i class="fas fa-plus-circle" style="font-size: 17px;"></i></a>
      &nbsp;&nbsp;
      <a href="" id="zoomout" class="zoomaction" data-toggle="tooltip" title="Zoom Out"><i class="fas fa-minus-circle" style="font-size: 17px;"></i></a>
      &nbsp;&nbsp;
      <a href="" id="resetzoom" class="zoomaction" data-toggle="tooltip" title="Reset Zoom"><i class="fas fa-sync" style="font-size: 17px;"></i></a>
      <input type="hidden" id="scale" value="1">
    </div>
  </div>

  <div class="row">
    <div class="col-12">

      <div class="card">
          <div class="card-body">
            <div onload="prettyPrint();"> 
              <ul id="org" style="display:none">
                <?php  echo $get_started_tree; ?>
              </ul>

              <?php 
                $i=1;
                foreach ($keyword_bot_tree as $key => $value) 
                {
                  echo '<ul id="org'.$i.'" style="display:none">'.$value.'
                  </ul>';
                  echo '<script>
                    $(document).ready(function() {
                        $("#org'.$i.'").jOrgChart({
                            chartElement : "#chart",
                            dragAndDrop  : false
                        });
                    });
                    </script>';
                  $i++;
                }
              ?>

              <ul id="org0" style="display:none">
                <?php  echo $no_match_tree; ?>
              </ul>


              <center>
                  <div class="table-responsive nicescroll">
                      <div id="chart" class="orgChart"></div>
                  </div>
              </center>

            </div>
          </div>
      </div>

    </div>
  </div>
</section>







<script>
$(document).ready(function() {
    $("#org0").jOrgChart({
        chartElement : '#chart',
        dragAndDrop  : false
    });
    $('[data-toggle="tooltip"]').tooltip();
});
</script>




<div class="modal fade" id="iframe_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-cog"></i> <?php echo $page_title; ?> : <?php echo $this->lang->line('Settings'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
         <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
      </div>
    </div>
  </div>
</div>
