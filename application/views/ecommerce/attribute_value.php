<script type="text/javascript">
	$(document).ready(function() {

	  var attribute_info = [];  
	  $(".options").each(function() {          
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
	  });
	  $.ajax({
	    context: this,
	    type:'POST',
	    dataType:'JSON',
	    url:base_url+"ecommerce/get_price_basedon_attribues",
	    data:{product_id:current_product_id,current_store_id:current_store_id,attribute_info:attribute_info,currency_icon:currency_icon,currency_position:currency_position,decimal_point:decimal_point,thousand_comma:thousand_comma},
	    success:function(response){
	      $("#calculated_price_basedon_attribute").html(response.price_html);
	    }
	  });

	  
	  $(document).on('change','.options',function(e){      
	    var attribute_info = [];
	    $(".options").each(function() {        
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
	    });
	    $.ajax({
	      context: this,
	      type:'POST',
	      dataType:'JSON',
	      url:base_url+"ecommerce/get_price_basedon_attribues",
	      data:{product_id:current_product_id,current_store_id:current_store_id,attribute_info:attribute_info,currency_icon:currency_icon,currency_position:currency_position,decimal_point:decimal_point,thousand_comma:thousand_comma},
	      success:function(response){
	        $("#calculated_price_basedon_attribute").html(response.price_html);
	      }
	    });
	  });

	});
</script>