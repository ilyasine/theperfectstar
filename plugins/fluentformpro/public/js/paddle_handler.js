jQuery(document).ready((function(e){var a=e("body").find(".ff_paddle_payment_container"),t=e("body").find(".ff_frameless_header");if(a.length){var n=window.ff_paddle_vars,d=n.frame_initial_height||"450",i=n.frame_style||"width: 100%; min-width: 312px; background-color: transparent; border: none;",o=n.allowed_payment_methods||["alipay","apple_pay","bancontact","card","google_pay","ideal","paypal"],r=n.payment_mode||"sandbox",l=n.theme||"light",s=n.locale||"en",c=n.client_token;c&&(Paddle.Environment.set(r),Paddle.Initialize({token:c,checkout:{settings:{displayMode:"inline",allowedPaymentMethods:o,theme:l,locale:s,frameTarget:"ff_paddle_payment_container",frameInitialHeight:d,frameStyle:i}},eventCallback:function(d){if("checkout.completed"==d.name){var i={action:"fluentform_paddle_confirm_payment",transaction_hash:n.transaction_hash,submission_id:n.submission_id,paddle_payment:d.data};e.post(n.ajax_url,i).then((function(e){e.data&&e.data.payment.id==d.data.id&&(a.find("p").text(e.data.success_message),t.text(n.title_message))})).catch((function(e){var t="Request failed. Please try again";e&&e.responseJSON&&(t=e.responseJSON.errors),a.find("p").text(t)}))}if("checkout.error"==d.name){var o="Paddle payment process failed!";d.data&&d.data.error&&(o=d.data.error.detail),a.find("p").text(o)}}}))}}));