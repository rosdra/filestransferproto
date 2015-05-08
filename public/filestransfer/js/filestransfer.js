/**
 * Update view thought ajax calls
 */
//$(document).ready(function(){
$('body').on('click','[data-view-action]', function(e){	
		
	e.preventDefault();
	
	var $target = $(e.target);	
	var action = $target.data('view-action');
	var data = null;
	var url = $target.attr('href');
	var method = 'get';
	if(action == 'update' || action == 'create'){
		var form = $('#form_data');
		url = form.attr('action');
	    data = form.serializeArray();
	    data.push({name: 'page', value: $('#hdfCurrentPage').val()});	    
	    method = form.attr('method');
	}
	else if(action == 'show'){
		$(this).closest('tr').addClass('active').siblings().removeClass('active');
	}
	else if(action == 'destroy'){			
		var form = $('#form_delete');
	    var title = $target.data('title');
	    var message = $target.data('message');
	    
		return openConfirmDialog(title, message, form);	    
	}
		
	$.ajax({
	      url: url,
	      data: data,
	      type: method,
	      success: function(response){
	    	  response_processing(response)
	      }
	});
});	
//});
		


function openConfirmDialog(title, message, form){
	BootstrapDialog.show({
		title: title,
		message: '<div class="panel panel-body">'
					+ '<div class="form-group">'
						+ message 
					+ '</div>'
					+ '<div id="errors" class="alert alert-danger" style="display:none">'
					+ '</div>'
				+ '</div>',
		type: 'type-warning',
		closable: true,
        closeByBackdrop: false,
        closeByKeyboard: false,
		buttons: [
          {
        	  label: 'No',
        	  cssClass: 'btn-default',
        	  action:  function(dialog){
                  dialog.close();
        	  }
          },
          {
        	  label: 'Yes',
        	  cssClass: 'btn-warning',
        	  action:  function(dialog){
        		  
        		  url = form.attr('action');
        		  data = form.serializeArray();
        		  method = form.attr('method');
        		  
        		  $.ajax({
        		      url: url,
        		      data: data,
        		      type: method,
        		      success: function(response){
        		    	  
        		    	  if(response.errors){
                			  var errors_div = dialog.getModalBody().find('#errors');
                			  
                			  $.each(response.errors, function(index, value){
                				  if (value.length != 0){
                					  $.each(value, function(index1, value1){
        	    	                	errors_div.append('<li>'+ value1 +'</li>');
        	    	                  });
        	                      }
        	                  });
                			  errors_div.show();
                		  }
                		  else{
                			  response_processing(response)
                			  dialog.close();
                		  }        		    	  
        		      }
        		  });        		  
        	  }
          }			
		]			
	});
}



function response_processing(response){
	
   	if(!response.errors){	  		
		if(response.content){
			$("#form_content").html(response.content);
		}		
		
		if(response.grid){
			$("#navigator_content").html(response.grid);
		}
		
		if(!response.content && !response.grid){
    		$("#form_content").html(response);
    	}			
  	}	
}

  
/*
 * Ajax pagination
 */
$('body').on('click','.pagination a', function(e){
  $(this).attr('data',$(this).attr('href'));
  $(this).attr('href','javascript:void(0);');
  var url = $(this).attr('data');
  $.ajax({
      	url: url
      })
      .done(function( response ) {
          $( "#navigator_content" ).html( '' );
          $( "#navigator_content" ).html( response );

          $( ".ajax_overlay" ).hide();
          $( ".loader" ).hide();

      });
});  
