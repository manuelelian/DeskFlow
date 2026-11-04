<script>
	$(document).ready(inicializarEventos);

	function inicializarEventos() {
		actualizarCarrito();
		$(document).on('click', '#agregar', function() {		
			let id_producto = $('#producto').val();
			let cantidad = $('#cantidad').val();
			console.log(id_producto)
			console.log(cantidad)

			if (id_producto == ""|| id_producto == null || id_producto == 0){
				alert('Selecciona el producto');
			}else if (cantidad == ""|| cantidad == null || cantidad == 0) {
				alert('Por Favor, Ingrese Cantidad');
			}else{
				$.ajax({
		            url: '#path#productos/agregar_producto_lista',
		            type: 'POST',
		            dataType: 'json',
		            data: {id_producto:id_producto,cantidad:cantidad},
		        })
		        .done(function(response) {
		            console.log("success");
		            console.log(response.status);
		            if(response.status == true){
					    actualizarCarrito();
						$("#cantidad").val('');
					}
		        })
		        .fail(function(response) {
		            console.log("error");
		            console.log("no paso bien por aca");
		        })
		        .always(function(response) {
		            console.log("complete");
		        });
			}
		});
	}

	function actualizarCarrito(){
		$.ajax({
		  	url: '#path#productos/listar_productos',
		  	type: 'POST',
		  	dataType: 'json',
		})
		.done(function(response) {
		  	console.log("success");
		  	console.log(response);
		  	$('#tabla-productos').html(response.productos_lista);
		  	$('#total').html(response.total);
		  	console.log("paso bien por aca listar productos");
		})
		.fail(function(response) {
		  	console.log(response);
		  	console.log("no paso bien por aca listar productos");
		})
		.always(function(response) {
		  	console.log("complete");
		});
	}

	function eliminar_de_lista(id_producto) {
		$.ajax({
			url: '#path#productos/eliminar_lista',
			type: 'POST',
			dataType: 'json',
			data: {id_producto: id_producto},
		})
		.done(function(response) {
			console.log("success");
			console.log(response);
			if(response.status == true){
				actualizarCarrito();
			}

			console.log("paso bien por aca eliminar producto");
		})
		.fail(function(response) {
			console.log("error");
			console.log("no paso bien por aca eliminar producto");
		})
		.always(function(response) {
			console.log("complete");
			console.log(response);
		});
	}
</script>