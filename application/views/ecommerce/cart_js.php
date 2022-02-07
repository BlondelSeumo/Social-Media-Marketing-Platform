<script>
  var base_url="<?php echo site_url(); ?>";
  var subscriber_id = "<?php echo $subscriber_id;?>";
  var pickup = "<?php echo isset($pickup) ? $pickup : '';?>";
 
  $("document").ready(function()  {
    // $(".selecttwo").select2();
    $(".selecttwo_multiple").select2({minimumResultsForSearch: -1,placeholder: "<?php echo $this->lang->line('Choose Options');?>",});
    $(document).on('click','.add_to_cart',function(e){
     e.preventDefault();
     var product_id = $(this).attr("data-product-id");
     var attribute_ids = $(this).attr("data-attributes");
     var action = $(this).attr("data-action");
     var buy_now = false;
     if($(this).hasClass('buy_now')) buy_now = true;

     // if(attribute_ids!='')
     // {
     //    $(".options").each(function() {
     //        if($(this).val()=="")
     //        {
     //          if($(this).attr('data-optional')=='0') exit=true;
     //        }        
     //        temp = $(this).attr('data-attr');
     //        attribute_info[temp] = $(this).val();
     //    });
     // }
     
     var attribute_info = [];
     var required_attr = [];
     var exit = false;
     if(attribute_ids!='')
     {
      $(".options").each(function() {
          // if($(this).val()=="") exit=true;            
          if($(this).is(':checked'))
          {
            var temp = $(this).attr('data-attr');
            var value =  $(this).val();
            if(typeof(attribute_info[temp])==='undefined' || attribute_info[temp].length==0)
            {
              attribute_info[temp]=[];
            }
            attribute_info[temp].push(value);
          }
          var temp2 = $(this).attr('data-attr');
          if($(this).attr('data-optional')=='0' && required_attr.indexOf(temp2)==-1) required_attr.push(temp2);
      });
     }
     $.each(required_attr, function( index, value ) {
       if(typeof(attribute_info[value])==='undefined' || attribute_info[value].length==0){
        exit = true;
        return false;
       }
     });
     if(exit)
     {
      swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please choose the required options.'); ?> (*)", 'error');
      return false;
     }

     var item_count = $("#item_count").val();
     if (typeof(item_count)==='undefined') item_count = 0;
     else item_count = parseInt(item_count);

     if(item_count==0 && action=="remove")
     {
       swal("<?php echo $this->lang->line('Error'); ?>", '<?php echo $this->lang->line("Item can not be removed. It is not in cart anymore.");?>', 'error');
       return false;
     }
     var new_count = 0;
     var param = {'product_id':product_id,'action':action,'subscriber_id':subscriber_id,'attribute_info':attribute_info,pickup:pickup};
     var mydata = JSON.stringify(param);
     $(this).addClass("btn-progress");
     $.ajax({
       context : this,
       type: 'POST',
       dataType: 'JSON',
       data: {mydata:mydata},
       url: '<?php echo base_url('ecommerce/update_cart_item'); ?>',
       success: function(response) {

        $(this).removeClass("btn-progress");
        var cart_count = 0;
        if(response.status=='1')
        {
          cart_count = response.cart_data.cart_count;
          cart_count = parseInt(cart_count);
        }
        if(cart_count==0) 
        {
          $("#cart_count_display").html('0');
          // $("#single_visit_store").addClass('d-none');
          // $(".buy_now").removeClass('d-none');
          // if(attribute_ids=='')$("#single_buy_now").removeClass('d-none');
        }
        else
        {
          $("#cart_count_display").html(cart_count);
          $("#cart_count_display").parent().parent().attr('href',response.cart_data.cart_url);
          // $(".buy_now").addClass('d-none');
          // $("#cart_count_display").show();
          // $("#single_visit_store").attr('href',response.cart_data.cart_url).removeClass('d-none');
          // $("#single_buy_now").addClass('d-none');
        }
        
        if(response.status=='0')
        {
          var span = document.createElement("span");
          span.innerHTML = response.message;
          if(response.login_popup)
            swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {             
             $("#login_form").trigger('click');
            });
          else swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'});
        }
        else
        {
          if(buy_now) window.location.replace(response.cart_url);
          else 
          {
            iziToast.success({title: "",message: response.message,position: 'bottomRight',timeout: 1000});
            if(attribute_ids=='')
            {
              if(action=="add") new_count = item_count+1;              
              else new_count = item_count-1;              
            }
            else new_count = response.this_cart_item.quantity;
            $("#item_count").val(new_count);
          }

          $("#upsell_product").css("display","block");
        }
       }
     });

    });
  });
</script>
