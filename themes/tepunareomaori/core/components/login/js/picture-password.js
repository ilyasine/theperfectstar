jQuery(document).ready(function($) {
    var selectedImages = [];
	// Function to handle member click event
	$('.group-member').on('click', function () {		
		var nonce = $(this).data('security');
        var userId = $(this).data('user-id');
		NProgress.start();
		NProgress.set(0.4);
		$("input[name='passwordType'][value='text']").prop("checked", true);
		var modalMessage = $('.modal-message');
		modalMessage.empty();		
        $.ajax({
			url: ajaxurl,
            type: 'POST',
			data: {
				action: 'fetch_images',
				security: nonce,
				userId: userId
			},
            dataType: 'json',
            success: function(response) {
				eval(response.javascript);
				NProgress.done();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });

    // Function to handle modal close
	$(document).on('click', '#imageModal .modal-content .modal-title .close', function() {
        $('#imageModal').hide();
        selectedImages = [];
        $('.image-grid img').removeClass('selected');
    });

    // Function to handle save button click
	$('#imageModal .modal-content').on('click', '#saveButton', function(){
		var userId = $(this).data('user_id');
		var modalMessage = $('.modal-message');
		modalMessage.empty();
		var confirmPass = $('#confirm_pass').val();
		var textPass = $('#text_pass').val();
		var selectedType = $('input[name="passwordType"]:checked').val();
		if(selectedType){
			if(confirmPass === ''){
				confirmPass = 'EMPTY_VALUE'; 
				modalMessage.append("<p style='color:red;margin:10px 0px;'>Confirm Password cannot be empty</p>");
				return;
			}
			if(textPass === ''){
				textPass = 'EMPTY_VALUE'; 
				if(selectedType == 'text'){
					modalMessage.append("<p style='color:red;margin:10px 0px;'>Password cannot be empty</p>");
					return;
				}
			}
			if(selectedType == 'picture' && selectedImages == ''){
				modalMessage.append("<p style='color:red;margin:10px 0px;'>Please Select Picture</p>");
				return;
			}
		}else{
			if(textPass == '' && $('#text_pass')[0]){
				textPass = 'EMPTY_VALUE'; 
				modalMessage.append("<p style='color:red;margin:10px 0px;'>Password cannot be empty</p>");
				return;
			}else if(selectedImages == '' && !$('#text_pass')[0]){
				modalMessage.append("<p style='color:red;margin:10px 0px;'>Please Select Picture</p>");
				return;
			}
		}
		var nonce = $(this).data('security');
		$("#imageModal .modal-content #saveButton").prop('disabled', true);
		NProgress.start();
		NProgress.set(0.4);
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'save_update_image',
				security: nonce,
				selectedType: selectedType,
				userId: userId,
				confirmPass: confirmPass,
				textPass: textPass,
				selectedImages: selectedImages
			},
			dataType: 'json',
			success: function(response) {
					modalMessage.empty();
				if (response.success) {
					if(response.success == true){
						window.location.href = "/kodingSchool/en/";
					}
					modalMessage.append("<p style='color:green;margin:10px 0px;'>"+response.message+"</p>");
				} else {
					modalMessage.append("<p style='color:red;margin:10px 0px;'>"+response.message+"</p>");
				}
			},
			error: function(xhr, status, error) {
				console.error(error);
			}
		});
		$("#imageModal .modal-content #saveButton").prop('disabled', false);
		NProgress.done();
	});
	
	$('#imageModal .modal-content').on('change', 'input[name="passwordType"]', function() {
		var selectedType = $(this).val();
		
		$('#text_pass').prop('disabled', (selectedType === 'picture'));
		
		$('#picture_pass').prop('disabled', (selectedType === 'text'));

		if (selectedType === 'text') {
			$('#imageModal .modal-content .image-grid img').removeClass('selected');
			selectedImages = [];
		}
		if(selectedType === 'picture'){
			$('#text_pass').val('');
			imageSelection();
			
		}else if(selectedType === 'text'){
			$(document).off('click', '#imageModal .modal-content .image-grid img');
			$('#imageModal .modal-content .image-grid img').removeClass('selected');
			selectedImages = [];
			
		}
	});
	
	function imageSelection(){
		 // Function to handle image selection
		$(document).on('click', '#imageModal .modal-content .image-grid img', function() {
			var imageUrl = $(this).attr('src');
			
			// Deselect all images first
			$('#imageModal .modal-content .image-grid img').removeClass('selected');
			selectedImages = [];
			
			// Select the clicked image
			$(this).addClass('selected');
			selectedImages.push(imageUrl);
		});
	}
	
});

