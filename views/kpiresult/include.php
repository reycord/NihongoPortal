<script>
    $(function(){
        $('#result-form input[name=result]').change(function() {
           $("#message_div").hide();
           
           $('#result-form td.has-warning:has(input[name=result])').removeClass('has-warning');
        });
        $('#result-form input[name=mark]').change(function() {
           $("#message_div").hide();
           if (/\d+/m.test($(this).val()) == false){
               $(this).val("");
               return;
           }
           $('#result-form td.has-warning:has(input[name=mark])').removeClass('has-warning');
        });
    });

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
	function showmodal(kpi_id, month){
		var data = {
			kpi_id: kpi_id,
			month: month,
		};
		$.ajax({
		  type: "POST",	
		  url: "<?php echo $this->url("kpiresult") ?>",
		  data: data,
		}).done(function(data) {

			$('#modal').html(data);
			$('#modal').modal('show');
				
		}).fail(function(data) {
	    	//alert( "error" );
	  	});
	};
	
	function showversioninfo(){
		var data = {
		};
		$.ajax({
		  type: "POST",	
		  url: "<?php echo $this->url("versioninfo") ?>",
		  data: data,
		}).done(function(data) {

			$('#modal').html(data);
			$('#modal').modal('show');
				
		}).fail(function(data) {
	    	//alert( "error" );
	  	});
	};
	
	function showterms(){
		var data = {
		};
		$.ajax({
		  type: "POST",	
		  url: "<?php echo $this->url("terms") ?>",
		  data: data,
		}).done(function(data) {

			$('#modal').html(data);
			$('#modal').modal('show');
				
		}).fail(function(data) {
	    	//alert( "error" );
	  	});
	};
	
	function update_data(kpi_id,data){
		var selected_row = $('#dataTable-list >tbody >tr.info')[0];
	}
	
	function getdata(kpi_id, month){
		var data = {
			kpi_id: kpi_id,
			month: month,
		};
		$.ajax({
		  type: "POST",	
		  url: "<?php echo $this->url("kpiresult") ?>",
		  data: data,
		}).done(function(data) {
			$('#modal').html(data);

			
		}).fail(function(data) {
	    	//alert( "error" );
	  	});
	};
	
	function create(){
		
			$("#result-form :input#submit").val('create');		
			resultFormSubmit();
		
		
	};
	function delete_id(delete_id){

			$("#modal_error_div").hide();
			if (!confirm("<?php echo getMessageById("101") ?>"))
			{
				return ;
			}
			else{					
			$("#result-form :input#submit").val('delete');
			$("#result-form :input#delete_id").val(delete_id);
			resultFormSubmit();
			}

		
		
	};
	function edit_id(){
		$("#result-form :input#submit").val('edit');        
            resultFormSubmit();
	};
	
	function resultFormSubmit(){
		var data = $("#result-form").serialize();
		$.ajax({
		  type: "POST",	
		  url: "<?php echo $this->url("kpiresult") ?>",
		  data: data,
		}).done(function(data) {
			$('#modal').html(data);
			var selected_row = $('#dataTable-list >tbody >tr.info')[0];
			selected_row.cells[7].textContent = $("#modal-kpi-result").html();
            if(!isset(selected_row.cells[7].textContent.val())){
                selected_row.cells[7].textContent.val("0");
            }
		}).fail(function(data) {
	    	//alert( "error" );
	  	});
	}
</script>
<div id="modal" class="modal fade">