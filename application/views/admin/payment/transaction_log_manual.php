<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-hand-holding-usd"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
      <?php if ('Member' == $this->session->userdata('user_type')): ?>
      <a class="btn btn-primary" href="<?php echo base_url('payment/buy_package'); ?>"><i class="fa fa-cart-plus"></i> <?php echo $this->lang->line('Renew Package'); ?></a>
      <?php endif; ?>
    </div>
    <div class="section-header-breadcrumb">
      <?php 
      if($this->session->userdata("user_type")=="Admin") 
      echo '<div class="breadcrumb-item">'.$this->lang->line("Subscription").'</div>';
      else echo '<div class="breadcrumb-item">'.$this->lang->line("Payment").'</div>';
      ?>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>  
                    <th><?php echo $this->lang->line("Name"); ?></th>      
                    <th><?php echo $this->lang->line("Email"); ?></th>      
                    <th><?php echo $this->lang->line("Additional Info"); ?></th>
                    <th><?php echo $this->lang->line("Attachment"); ?></th>
                    <th style="min-width:100px;"><?php echo $this->lang->line("Status"); ?></th>
                    <th><?php echo $this->lang->line("Actions"); ?></th>
                    <th><?php echo $this->lang->line("Package"); ?></th>
                    <th><?php echo $this->lang->line("Package Price"); ?></th>
                    <th><?php echo $this->lang->line("Package Validity"); ?></th>
                    <th><?php echo $this->lang->line("Paid Amount"); ?></th>      
                    <th><?php echo $this->lang->line("Paid At"); ?></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <th><?php echo $this->lang->line("Total"); ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>

<?php
$drop_menu ='<a href="javascript:;" id="payment_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="payment_date_range_val">';
?>

<?php if ('Admin' == $this->session->userdata('user_type')): ?>
  <div class="modal fade" tabindex="-1" role="dialog" id="manual-payment-reject-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line("Manual payment rejection");?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">            
            <div class="row">
              <!-- Additional Info -->
              <div class="col-lg-12">
                <div class="form-group">
                  <label for="paid-amount"><?php echo $this->lang->line('Describe, why do you want to reject this payment?'); ?></label>
                  &nbsp;
                  <textarea name="rejected-reason" id="rejected-reason" class="form-control"></textarea>
                  <input type="hidden" id="mp-transaction-id">
                  <input type="hidden" id="mp-action-type">
                </div>
              </div>  
            </div>

          </div><!-- ends container -->
        </div><!-- ends modal-body -->

        <!-- Modal footer -->
        <div class="modal-footer bg-whitesmoke br">
          <button type="button" id="manual-payment-reject-submit" class="btn btn-primary"><?php echo $this->lang->line('Submit'); ?></button>      
          <button type="button" class="btn btn-secondary btn-md" data-dismiss="modal"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="modal fade" tabindex="-1" role="dialog" id="manual-payment-modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo $this->lang->line("Manual payment");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          
          <!-- Manual payment instruction -->
          <div id="manual-payment-instructions" class="row d-none">
            <div class="col-lg-12 mb-4">
              <div class="alert alert-light alert-has-icon">
                <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                <div class="alert-body">
                  <div class="alert-title"><?php echo $this->lang->line('Manual payment instructions'); ?></div>
                  <p id="payment-instructions"></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Paid amount and currency -->
          <div class="row">
            <div class="col-lg-6 mb-4">
              <div class="form-group">
                <label for="paid-amount"><i class="fa fa-money-bill-alt"></i> <?php echo $this->lang->line('Paid Amount'); ?>:</label>
                <input type="number" name="paid-amount" id="paid-amount" class="form-control" min="1">
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="form-group">
                <label for="paid-currency"><i class="fa fa-coins"></i> <?php echo $this->lang->line('Currency'); ?></label>              
                <?php echo form_dropdown('paid-currency', $currency_list, [], ['id' => 'paid-currency', 'class' => 'form-control']); ?>
              </div>
            </div>
          </div>          
          
          <!-- Image upload - Dropzone -->
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label><i class="fa fa-paperclip"></i> <?php echo $this->lang->line('Attachment'); ?> <?php echo $this->lang->line('(Max 5MB)');?> </label>
                <div id="manual-payment-dropzone" class="dropzone mb-1">
                  <div class="dz-default dz-message">
                    <input class="form-control" name="uploaded-file" id="uploaded-file" type="hidden">
                    <span style="font-size: 20px;"><i class="fas fa-cloud-upload-alt" style="font-size: 35px;color: var(--blue);"></i> <?php echo $this->lang->line('Upload'); ?></span>
                  </div>
                </div>
                <span class="red">Allowed types: pdf, doc, txt, png, jpg and zip</span>
              </div>
            </div>

            <!-- Additional Info -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="paid-amount"><i class="fa fa-info-circle"></i> <?php echo $this->lang->line('Additional Info'); ?>:</label>
                &nbsp;
                <textarea name="additional-info" id="additional-info" class="form-control"></textarea>
              </div>
              <input type="hidden" id="selected-package-id">
              <input type="hidden" id="mp-resubmitted-id">
            </div>  
          </div>

        </div><!-- ends container -->
      </div><!-- ends modal-body -->

      <!-- Modal footer -->
      <div class="modal-footer bg-whitesmoke br">
        <button type="button" id="manual-payment-submit" class="btn btn-primary"><?php echo $this->lang->line('Submit'); ?></button>      
        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
      <div id="mp-spinner" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: #ffffff" class="justify-content-center align-items-center d-flex"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i></div><!-- spinner -->
    </div>
  </div>
</div>

<script>
  var base_url="<?php echo site_url(); ?>";

  var drop_menu = '<?php echo $drop_menu;?>';
  setTimeout(function(){ 
    $("#mytable_filter").append(drop_menu); 
    $('#payment_date_range').daterangepicker({
      ranges: {
        '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
        '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
        '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    }, function (start, end) {
      $('#payment_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
    });
  }, 2000);
    
   
  $(document).ready(function() {
    var perscroll;
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 0, "desc" ]],
        pageLength: 10,
        ajax: {
          url: '<?php echo base_url('payment/transaction_log_manual_data'); ?>',
          type: 'POST',
          data: function (d) {
            d.payment_date_range = $('#payment_date_range_val').val();
          }
        },
        language: {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columns: [
          {data: 'id'},
          {data: 'name'},
          {data: 'email'},
          {data: 'additional_info'},
          {data: 'attachment'},
          {data: 'status'},
          {data: 'actions'},
          {data: 'package'},
          {data: 'price'},
          {data: 'validity'},
          {data: 'paid_amount'},
          {data: 'created_at'},
        ],          
        columnDefs: [
          {
            // targets: [1,2],
            // visible: false
          },
          {
            targets: [0,1,2,4,5,6,7,8,9,10,11],
            className: 'text-center'
          },
          {
            // targets: [10],
            // className: 'text-right'
          },
          {
            targets: [3,4,5,6,7,8,9,10],
            sortable: false
          }
        ],
        footerCallback: function ( row, data, start, end, display ) {
          var api = this.api(), data;
          var payment_total = api
            .column(8)
            .data()
            .reduce(function (a, b) {
              return parseInt(a) + parseInt(b);
            }, 0);

            $(api.column(8).footer()).html(parseFloat(payment_total, 2));
        },
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll) {
              if (perscroll) {
                perscroll.destroy();
              }

              perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
            if(areWeUsingScroll) { 
              if (perscroll) {
                perscroll.destroy();
              }

              perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }
    });

    $(document).on('change', '#payment_date_range_val', function(event) {
      event.preventDefault(); 
      table.draw();
    });

    // Downloads file
    $(document).on('click', '#mp-download-file', function(e) {
      e.preventDefault();

      // Makes reference 
      var that = this;

      // Starts spinner
      $(that).removeClass('btn-outline-info');
      $(that).addClass('btn-info disabled btn-progress');

      // Grabs ID
      var file = $(this).data('id');

      // Requests for file
      $.ajax({
        type: 'POST',
        data: { file },
        dataType: 'JSON',
        url: '<?php echo base_url('payment/manual_payment_download_file') ?>',
        success: function(res) {
          // Stops spinner
          $(that).removeClass('btn-info disabled btn-progress');
          $(that).addClass('btn-outline-info');

          // Shows error if something goes wrong
          if (res.error) {
            swal({
              icon: 'error',
              text: res.error,
              title: '<?php echo $this->lang->line('Error!'); ?>',
            });
            return;
          }

          // If everything goes well, requests for downloading the file
          if (res.status && 'ok' === res.status) {
            window.location = '<?php echo base_url('payment/manual_payment_download_file'); ?>';
          }
        },
        error: function(xhr, status, error) {
          // Stops spinner
          $(that).removeClass('btn-info disabled btn-progress');
          $(that).addClass('btn-outline-info');

          // Shows internal errors
          swal({
            icon: 'error',
            text: error,
            title: '<?php echo $this->lang->line('Error!'); ?>',
          });
        }
      });
    });
  
<?php if ('Admin' == $this->session->userdata('user_type')): ?>    
    // Approve manual transaction
    $(document).on('click', '#mp-approve-btn, #mp-reject-btn', function(e) {
      e.preventDefault();

      // Makes reference
      var that = this;

      // Gets transaction ID
      var id = $(that).data('id');
      var action_type = $(that).attr('id');

      if ('mp-reject-btn' === action_type) {
        var reject_modal = $('#manual-payment-reject-modal');

        // Sets values to rejection form's hidden fields
        $('#mp-transaction-id').val(id);
        $('#mp-action-type').val(action_type);

        // Opens up rejection modal
        $(reject_modal).modal();
        return;
      }

      // Gets classes
      var prev_btn_el = $(that).parent().prev(); 
      var el_classes = prev_btn_el ? prev_btn_el[0].className : '';
      var new_classes = el_classes ? el_classes.replace('-outline', '') : '';

      // Shows spinner
      $(prev_btn_el).removeClass();
      $(prev_btn_el).addClass(new_classes.concat(' disabled btn-progress'));

      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        data: { id, action_type },
        url: '<?php echo base_url('payment/manual_payment_handle_actions'); ?>',
        success: function(res) {
          // Stops spinner
          $(prev_btn_el).removeClass();
          $(prev_btn_el).addClass(el_classes);

          // Shows error if something goes wrong
          if (res.error) {
            swal({
              icon: 'error',
              text: res.error,
              title: '<?php echo $this->lang->line('Error!'); ?>',
            });
            return;
          }
          // If everything goes well, requests for downloading the file
          if (res.status && 'ok' === res.status) {
            // Shows success message
            swal({
              icon: 'success',
              text: res.message,
              title: '<?php echo $this->lang->line('Success!'); ?>',
            });

            // Reloads datatable
            table.ajax.reload();
          }
        },
        error: function(xhr, status, error) {
          // Stops spinner
          $(prev_btn_el).removeClass();
          $(prev_btn_el).addClass(el_classes);

          // Shows error if something goes wrong
          swal({
            icon: 'error',
            text: xhr.responseText,
            title: '<?php echo $this->lang->line('Error!'); ?>',
          });            
        }
      });
    });

    // Handles payment's approval
    $(document).on('click', '#manual-payment-reject-submit', function(e) {
      e.preventDefault();

      // Makes reference
      var that = this;

      // Starts spinner
      $(that).addClass('btn-progress disabled');

      // Gets some vars
      var id = $('#mp-transaction-id').val();
      var action_type = $('#mp-action-type').val();
      var rejected_reason = $('#rejected-reason').val();

      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        data: { id, action_type, rejected_reason },
        url: '<?php echo base_url('payment/manual_payment_handle_actions'); ?>',
        success: function(res) {
          // Stops spinner
          $(that).removeClass('btn-progress disabled');

          // Shows error if something goes wrong
          if (res.error) {
            swal({
              icon: 'error',
              text: res.error,
              title: '<?php echo $this->lang->line('Error!'); ?>',
            });
            return;
          }
          // If everything goes well, requests for downloading the file
          if (res.status && 'ok' === res.status) {
            // Shows success message
            swal({
              icon: 'success',
              text: res.message,
              title: '<?php echo $this->lang->line('Success!'); ?>',
            });

            // Clears rejection msg
            $('#rejected-reason').val('');

            // Closes modal
            $('#manual-payment-reject-modal').modal('toggle');

            // Reloads datatable
            table.ajax.reload();
          }
        },
        error: function(xhr, status, error) {
          // Stops spinner
          $(that).removeClass('btn-progress disabled');

          // Shows error if something goes wrong
          swal({
            icon: 'error',
            text: xhr.responseText,
            title: '<?php echo $this->lang->line('Error!'); ?>',
          });            
        }
      });
    });

<?php endif; ?>
    
    // Handles data re-submit form's data
    $(document).on('click', '#manual-payment-resubmit', function(e) {
      e.preventDefault();

      // Makes reference 
      var that = this;

      // Gets transaction ID
      var id = $(that).data('id');
      $('#mp-resubmitted-id').val(id);

      // Starts spinner
      $('#mp-spinner').addClass('d-flex');

      // Opens up modal
      $('#manual-payment-modal').modal();

      // Gets data via ajax
      $.ajax({
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: { id },
        url: '<?php echo base_url('payment/transaction_log_manual_resubmit_data'); ?>',
        success: function(res) {

          if (res.status && 'ok' === res.status) {
            // Stops spinner
            $('#mp-spinner').removeClass('d-flex');
            $('#mp-spinner').addClass('d-none');
            
            // Sets values
            if (res.manual_payment_status 
              && 'yes' === res.manual_payment_status
            ) {
              $('#manual-payment-instructions').removeClass('d-none');
            } else {
              $('#manual-payment-instructions').addClass('d-none');
            }

            if (res.manual_payment_instruction) {
              $('#payment-instructions').text(res.manual_payment_instruction);
            }

            $('#paid-amount').val(res.paid_amount);
            $('#paid-currency').val(res.paid_currency);
            $('#additional-info').val(res.additional_info);
            $('#selected-package-id').val(res.package_id);
          }

          if (res.error) {
            swal({
              icon: 'error',
              title: '<?php echo $this->lang->line('Error!'); ?>',
              text: res.error,
            });
          }
        },
        error: function(xhr, status, error) {
          // Stops spinner
          $('#mp-spinner').removeClass('d-flex');
          $('#mp-spinner').addClass('d-none');

          // Displays error
          swal({
            icon: 'error',
            title: '<?php echo $this->lang->line('Error!'); ?>',
            text: error,
          });
        },
      });
    });

    // Uploads files
    var uploaded_file = $('#uploaded-file');
    Dropzone.autoDiscover = false;
    $("#manual-payment-dropzone").dropzone({ 
      url: '<?php echo base_url('payment/manual_payment_upload_file'); ?>',
      maxFilesize:5,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".pdf,.doc,.txt,.png,.jpg,.jpeg,.zip",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);

        // Shows error message
        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo $this->lang->line('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          $(uploaded_file).val(data.filename);
        }
      },
      removedfile: function(file) {
        var filename = $(uploaded_file).val();
        delete_uploaded_file(filename);
      },
    });

    // Handles form submit
    $(document).on('click', '#manual-payment-submit', function() {
      
      // Reference to the current el
      var that = this;

      // Shows spinner
      $(that).addClass('disabled btn-progress');

      var data = {
        paid_amount: $('#paid-amount').val(),
        paid_currency: $('#paid-currency').val(),
        package_id: $('#selected-package-id').val(),
        additional_info: $('#additional-info').val(),
        mp_resubmitted_id: $('#mp-resubmitted-id').val(),
      };

      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: '<?php echo base_url('payment/manual_payment'); ?>',
        data: data,
        success: function(response) {
          if (response.success) {
            // Hides spinner
            $(that).removeClass('disabled btn-progress');

            // Empties form values
            empty_form_values();
            $('#selected-package-id').val('');  
            $('#mp-resubmitted-id').val('');  

            // Shows success message
            swal({
              icon: 'success',
              title: '<?php echo $this->lang->line('Success!'); ?>',
              text: response.success,
            });

            // Refreshes datatable
            table.ajax.reload();

            // Hides modal
            $('#manual-payment-modal').modal('hide');
          }

          // Shows error message
          if (response.error) {
            // Hides spinner
            $(that).removeClass('disabled btn-progress');

            swal({
              icon: 'error',
              title: '<?php echo $this->lang->line('Error!'); ?>',
              text: response.error,
            });
          }
        },
        error: function(xhr, status, error) {
          $(that).removeClass('disabled btn-progress');
        },
      });
    });

    $('#manual-payment-modal').on('hidden.bs.modal', function (e) {
      var filename = $(uploaded_file).val();
      delete_uploaded_file(filename);
      $('#selected-package-id').val(''); 
    });

    function delete_uploaded_file(filename) {
      if('' !== filename) {     
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { filename },
          url: '<?php echo base_url('payment/manual_payment_delete_file'); ?>',
          success: function(data) {
            $('#uploaded-file').val('');
          }
        });
      }

      // Empties form values
      empty_form_values();     
    }

    // Empties form values
    function empty_form_values() {
      $('#paid-amount').val(''),
      $('.dz-preview').remove();
      $('#additional-info').val(''),
      $('#paid-currency').prop("selectedIndex", 0);
      $('#manual-payment-dropzone').removeClass('dz-started dz-max-files-reached');

      // Clears added file
      Dropzone.forElement('#manual-payment-dropzone').removeAllFiles(true);
    }    

  });

</script>