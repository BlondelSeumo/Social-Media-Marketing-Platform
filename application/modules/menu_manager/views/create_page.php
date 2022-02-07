<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("menu_manager/index"); ?>"><?php echo $this->lang->line("Menu Manager"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("menu_manager/get_page_lists"); ?>"><?php echo $this->lang->line("Page Manager"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div> 
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form action="#" id="create_custom_page" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Page Name'); ?></label>
                                        <input type="text" class="form-control" id="page_name" name="page_name" value="<?php echo set_value("page_name") ?>">
                                        <div class="invalid-feedback"><?php echo $this->lang->line("Page Name is Required"); ?></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Page Description'); ?></label>
                                        <textarea type="text" class="form-control" id="page_description" name="page_description" placeholder="<?php echo $this->lang->line("Type your page description here..."); ?>"><?php echo set_value("page_description") ?></textarea>
                                        <div class="invalid-feedback"><?php echo $this->lang->line("Page Description is required"); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-whitesmoke">
                            <button class="btn btn-lg btn-primary" id="create_page" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Page") ?> </button>
                            <a class="btn btn-lg btn-light float-right" onclick='goBack("menu_manager/get_page_lists",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function($) {
    var base_url = '<?php echo base_url(); ?>';
    $('#page_description').summernote(); 
    $('div.note-group-select-from-files').remove();   

    $(document).on('click', '#create_page', function(event) {
        event.preventDefault();

        var page_name = $("#page_name").val();
        var page_description = $("#page_description").val();

        if(page_name=='') {
            $("#page_name").addClass('is-invalid');
            return false;
        }
        else {
            $("#page_name").removeClass('is-invalid');
        }

        if(page_description=='') {
            $("#page_description").addClass('is-invalid');
            return false;
        }
        else {
            $("#page_description").removeClass('is-invalid');
        }

        $(this).addClass('btn-progress');
        var that= $(this);

        var report_link = base_url+"menu_manager/get_page_lists";

        $.ajax({
            url: base_url+'menu_manager/create_page_action',
            type: 'POST',
            dataType:'JSON',
            data: {page_name: page_name, page_description:page_description},
            success:function(response) {
                $(that).removeClass('btn-progress');
                if(response.error) {
                    var span = document.createElement("span");
                    span.innerHTML = response.error;
                    swal({ title:'<?php echo $this->lang->line("Warning"); ?>', content:span,icon:'warning'});
                }

                if(response.status =="1") {
                    var span = document.createElement("span");
                    span.innerHTML = '<?php echo $this->lang->line("Page has been created successfully.") ?>';
                    swal({ title:'<?php echo $this->lang->line("Page Created"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=report_link;});
                } else if(response.status =='0') {
                    var span = document.createElement("span");
                    span.innerHTML = '<?php echo $this->lang->line("Something went wrong,please try again.") ?>';
                    swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {window.location.href=report_link;});
                }
            }
        })
    });
});
</script>

<style>
    .note-toolbar{background:#eee !important;}
    .note-editable{min-height:250px;max-height:800px !important;}
    .note-placeholder{color:#cacaca;}
    .note-btn{padding: 2px 10px !important}
</style>