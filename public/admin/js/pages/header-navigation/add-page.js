    $(document).ready(function(){
    var formSubmitted = false;
	$('#form_submit').click(function(e){
		var flag = 0;
        if(!formSubmitted){
            formSubmitted = false;
             if($.trim($('#type').val()) == "cms"){
				 if($.trim($('#menu_page_id').val()) == ""){
					swal("Error!", 'Please Select CMS Page.', "error");
					flag = 1;
					return false;
				 }
			  }				 
			  if($.trim($('#type').val()) == "custom"){
				 if($.trim($('#title').val()) == ""){ 
					swal("Error!", 'Please Enter Page Title.', "error");
					flag = 1;
					return false;
				 }
				 if($.trim($('#url').val()) == ""){ 
					swal("Error!", 'Please Enter Page URL.', "error");
					flag = 1;
					return false;
				 }else{
					url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
					if(!url_validate.test($.trim($('#url').val()))){
					   swal("Error!", 'Please Enter valid URL.', "error");
					   flag = 1;
					   return false;
					}
				 }
				 if($.trim($('#seo_title').val()) == ""){
					swal("Error!", 'Please Enter SEO Title.', "error");
					flag = 1;
					return false;
				 }
				 if($.trim($('#seo_description').val()) == ""){
					swal("Error!", 'Please Enter SEO Description.', "error");
					flag = 1;
					return false;
				 }
				 if($.trim($('#seo_keyword').val()) == ""){
					swal("Error!", 'Please Enter SEO Keyword.', "error");
					flag = 1;
					return false;
				 }
			}
            if(flag == 0){
                $('#form_submit .indicator-label').addClass('d-none');
                $('#form_submit .indicator-progress').removeClass('d-none');
                var form = $('#pageForm')[0];
                var formData = new FormData(form);
                formSubmitted = true;
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: saveDataURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(msg){
                        var obj = JSON.parse(msg);
                        formSubmitted = false;
                        $('#form_submit .indicator-label').removeClass('d-none');
                        $('#form_submit .indicator-progress').addClass('d-none');
                        if(obj['heading'] == "Success"){
                            swal("", obj['msg'], "success").then((value) => {
                                window.location.assign(returnURL);
                            });
                        }else{
                            swal("Error!", obj['msg'], "error");
                            return false;
                        }
                    },error: function(ts) {
                        formSubmitted = false;
                        $('#form_submit .indicator-label').removeClass('d-none');
                        $('#form_submit .indicator-progress').addClass('d-none');
                        swal("Error!", 'Some thing want to wrong, please try after sometime.', "error");
                        return false;
                    }
                });
            }
        }
	});
});
