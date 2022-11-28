function eliminar(controller, id, borrar = "")
{
	if(confirm("Esta seguro que quiere eliminar este registro?"))
	{
		if(borrar == 1)
			url = "/"+ controller +"/delete/"+id
		else 
			url = "/"+ controller +"/"+id;
		$('#cargando').html('<img src="http://preloaders.net/preloaders/287/Filling%20broken%20ring.gif"> Cargando...');
			$.ajaxSetup({
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

	        var $data = new FormData();
	            $data.append('id', id);

	        $.ajax({
	            type: 'DELETE',
	            url: url,
	            data: $data,
    			dataType: 'json',
    			processData: false,
			    contentType: false,
			    cache: false,
			    timeout: 600000,
	            beforeSend: function() { $('#cargando').show(); },
	            success: function(response) {
	                location.reload();
	            },
	            error: function(response) {
	            	console.log(response);
	                //location.reload();	                
	            }
	        });
	}
}