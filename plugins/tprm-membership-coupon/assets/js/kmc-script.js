jQuery(document).ready(function ($) {

  function proccessBuy(code, prID, formThis, uid) {
    let lic_activate = $(formThis).find('[type="submit"]').val();
    $(formThis).find('[type="submit"]').val(kmc_translate.creating);

    $.ajax({
      url: TPRM_Data.ajaxUrl,
      method: 'POST',
      data: {
        code: code,
        prid: prID,
        user_id: uid,
        action: 'license_buy',
      },
      success: function (response) {
        //console.log('License Buy Response:', response);
        if (!response.success) {
          $(formThis).find('.TPRM_Erros').html('');
          $(formThis).find('.TPRM_Erros').text(response.data.msg);
          $(formThis).find('.TPRM_Erros').show();
          $(formThis).find('[type="submit"]').val(lic_activate);

          return;
        }
        if (response.success) {
          $('.TPRM_-license').html('<h3>' + response.data.msg + '</h3>');
          //setTimeout(() => {
          location.reload();
          //}, 1000);
        }
        // do something with the response data
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log('License Buy Error:', textStatus, errorThrown); // Log error details
        $(formThis).find('[type="submit"]').val(lic_activate);
      }
    });
  }


  $('.license-paste i').on('click', function (e) {
    // Check if the Clipboard API is supported in the browser
    if (navigator.clipboard) {
      navigator.clipboard.readText()
        .then(clipboardText => {
          // Paste the clipboard content into the input field
          $('.licenseinpt').val(clipboardText);
        })
        .catch(error => {
          console.error('Failed to read clipboard contents: ', error);
        });
    } else {
      // If Clipboard API is not supported, fallback to the traditional method
      $('.licenseinpt').select();
      document.execCommand('paste');
    }
  });


  $('form.TPRM_FormLicense').on('submit', function (e) {
    e.preventDefault();

    let lic_activate = $(this).find('[type="submit"]').val();

    $(this).find('[type="submit"]').val(kmc_translate.proccessing);

    $(this).find('.TPRM_Erros').hide();

    let code = $(this).find('.licenseinpt').val();
    if (code == '' || code.length <= 0) {
      $(this).find('.TPRM_Erros').html('');
      $(this).find('.TPRM_Erros').text(kmc_translate.empty_code);
      $(this).find('.TPRM_Erros').show();
      $(this).find('[type="submit"]').val(lic_activate);
      return;
    }
    let prID = $(this).find('[name="prId"]').val();
    let formThis = this;
    let uid = $(this).find('[name="sId"]').val();


    $.ajax({
      url: TPRM_Data.ajaxUrl,
      method: 'POST',
      data: {
        code: code,
        prid: prID,
        user_id: uid,
        action: 'license_check'
      },
      success: function (response) {
        if (!response.success) {
          $(formThis).find('.TPRM_Erros').html('');
          $(formThis).find('.TPRM_Erros').text(response.data.msg);
          $(formThis).find('.TPRM_Erros').show();
          $(formThis).find('[type="submit"]').val(lic_activate);
          return;
        }
        if (response.success) {
          //console.log(response);
          $(formThis).find('[type="submit"]').val(lic_activate);
          proccessBuy(code, prID, formThis, uid);
        }
      },
      error: function (result) {
        //console.log(result);
        $(formThis).find('[type="submit"]').val(lic_activate);
        // handle the error case
      }
    });

  });

});
