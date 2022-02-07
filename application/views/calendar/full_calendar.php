<?php 
    $user_id_url = $this->uri->segment(3);
    if(empty($user_id_url)) $user_id_url = 0;
?>
<?php include(APPPATH.'views/calendar/fullcalendar_css.php'); ?>
<?php include(APPPATH.'views/calendar/fullcalendar_custom_js.php'); ?>

<section class="section section_custom">
    <div class="section-header">
        <h1><i class='fa fa-calendar'></i> <?php echo $this->lang->line("activity calendar"); ?></h1>
    </div>


    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</section>
