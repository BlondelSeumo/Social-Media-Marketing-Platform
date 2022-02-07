<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-ticket-alt"></i>
      <?php $userurl = ($this->session->userdata("user_type")=="Admin") ? base_url("admin/edit_user/".$ticket_info[0]["user_id"]) : base_url("member/edit_profile");?>
      <?php echo $this->lang->line("Ticket");?> #<?php echo $ticket_info[0]['id']; ?>
      : <a href="<?php echo $userurl; ?>"><?php echo $user_info[0]['name']; ?></a>
      <span id="ticket_status">
      <?php if($ticket_info[0]["ticket_status"]=="1") echo "[".$this->lang->line("Open")."]";?>
      <?php if($ticket_info[0]["ticket_status"]=="2") echo "[".$this->lang->line("Closed")."]";?>
      <?php if($ticket_info[0]["ticket_status"]=="3") echo "[".$this->lang->line("Resolved")."]";?>
      </span>
    </h1>  
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('simplesupport/tickets');?>"><?php echo $this->lang->line("Support Desk"); ?></a></div>     
      <div class="breadcrumb-item"><?php echo $this->lang->line("Ticket"); ?> #<?php echo $ticket_info[0]["id"];  ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card card-primary">
          <div class="card-header">
            <h4>
              <i class="fas fa-ticket-alt"></i> <?php echo $ticket_info[0]["ticket_title"];?>
              <code>&nbsp;<i class="far fa-clock"></i> <?php echo date_time_calculator($ticket_info[0]['ticket_open_time'],true); ?></code>
            </h4>
            <div class="card-header-action">
              <div class="btn-group">
                <?php 
                $id = $ticket_info[0]["id"];
                $action="";

                if($ticket_info[0]['ticket_status'] != '3')
                $action .= '<a  table_id="'.$id.'" href="" class="btn btn-outline-primary ticket_action"  data-type="resolve"><i class="fas fa-paper-plane"></i> '.$this->lang->line("Resolve").'</a>';

                if($ticket_info[0]['ticket_status'] != '2')
                $action .= '<a  table_id="'.$id.'" href="" class="btn btn-outline-primary ticket_action"  data-type="close"><i class="fas fa-ban"></i> '.$this->lang->line("Close").'</a>';          

                if($ticket_info[0]['display'] == '1' && $this->session->userdata("user_type")=="Admin")
                $action .= '<a  table_id="'.$id.'" href="" class="btn btn-outline-primary ticket_action"  data-type="hide"><i class="fas fa-eye-slash"></i> '.$this->lang->line("Hide").'</a>';

                echo $action;
                ?>
              </div>
            </div>
          </div>
          <div class="card-footer bg-whitesmoke text-justify">
              <?php echo $ticket_info[0]['ticket_text']; ?>
          </div>
          <div class="card-body">
            <ul class="list-unstyled list-unstyled-border list-unstyled-noborder">
                <?php if(count($ticket_replied)==0) echo "<br>".$this->lang->line("No reply found.");?>
                <?php foreach($ticket_replied as $single_reply)
                { ?>
                  <li class="media">
           
                    <?php if($single_reply['brand_logo']!='') :?>
                    <img width="70" class="mr-3 rounded-circle" src="<?php echo base_url('member/').$single_reply['brand_logo']; ?> " alt="">
                    <?php  else: ?>
                    <img width="70" class="mr-3 rounded-circle" src="<?php echo base_url('assets/img/avatar.png'); ?> " alt="">
                    <?php  endif; ?>

                    <div class="media-body">
                      <div class="media-title mb-1"><?php echo $single_reply['name']; ?></div>
                      <div class="text-time"><i class="far fa-clock"></i> <?php echo date_time_calculator($single_reply['ticket_reply_time'],true); ?></div>
                      <div class="media-description text-muted text-justify">
                        <?php if(isset($single_reply['ticket_reply_text'])) echo $single_reply['ticket_reply_text']; ?>
                      </div>
                    </div>
                  </li>
                <?php 
                } ?>
            </ul>
          </div>
          <div class="card-footer bg-whitesmoke">
              <form class="from-show"  action="<?php echo base_url('simplesupport/reply_action/'); ?>" method="POST" enctype="multipart/form-data" novalidate>
                  <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
                  <input type="hidden" name="id" value="<?php echo $ticket_info[0]['id']; ?>">
                  <div class="form-group">
                      <label><?php echo $this->lang->line('Reply Ticket'); ?></label>
                      <div id="ckeditor">
                          <textarea required class="form-control" name="ticket_reply_text" id="ticket_reply_text"></textarea>
                      </div>
                 </div> 
          </div>
          <div class="card-footer">
              <?php $red_link="simplesupport/tickets";?>
              <button type="submit" class="btn btn-primary btn-lg reply"><i class="fa fa-send"></i> <?php echo $this->lang->line('Reply'); ?> </button> 
              <a onclick="goBack('<?php echo $red_link ?>',1)" class="btn btn-light btn-lg float-right cancel from-show"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel"); ?> </a>                        
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style type="text/css">
  .modal-backdrop, .modal-backdrop.in{
    display: none;
  }
</style>

<script>  
    var base_url="<?php echo site_url(); ?>";
    $(document).ready(function() {       

        $('#ticket_reply_text').summernote({
          height: 300,
          minHeight:300,
          toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'underline', 'clear']],
              ['fontname', ['fontname']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['table', ['table']],
              ['insert', ['link', 'picture']],
              ['view', ['codeview']]
          ]
        });

        $(document.body).on('click', '.ticket_action', function(e) {
          e.preventDefault();
          var id = $(this).attr("table_id");
          var action = $(this).attr("data-type");
          
          $(this).addClass('btn-progress');
          $.ajax({
            context: this,
            url: base_url+"simplesupport/ticket_action",
            type: 'POST',
            dataType: 'JSON',
            data: {id:id,action:action},
              success:function(response)
              {
                $(this).removeClass('btn-progress');
                if(response.status == "1") iziToast.success({title: '<?php echo $this->lang->line("Success"); ?>',message: response.message,position: 'bottomRight'});
                else iziToast.error({title: '<?php echo $this->lang->line("Error"); ?>',message: response.message,position: 'bottomRight'});

                setTimeout(function() {          
                  location.reload();
                }, 2000);
              }
          });
        });

        $(document.body).submit(function () {
                  $(".reply").attr("disabled", true);
                  return true;
              });
      
    });
</script>


<style type="text/css">
  .note-group-select-from-files {
    display: none;
  }
</style>