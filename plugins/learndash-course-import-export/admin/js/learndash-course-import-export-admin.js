(function( $ ) { 'use strict';
    $( document ).ready( function() {

        var LD_CIE_Admin = {

            init: function() {
                $('#ld_cie_upload_file_form').on('submit', LD_CIE_Admin.uploadFileForm);
            },
            LoadProgressBar: function( c_ids ) {
                const progressBar = document.querySelector( '.progress-bar' );
                const progressBarText = document.querySelector( '.progress-bar__text' );
                const progressBarContainer = document.querySelector( '.progress-bar__container' );
                const log = document.querySelector( '.import_logs' );
                const progressBarStates = [ 25, 45, 65, 85, 100 ];
                var content = '';
                let time = 0;
                let endState = 100;

                progressBarStates.forEach( state => {
                  let randomTime = Math.floor( Math.random() * 1000 );
                  setTimeout( () => {
                    if( state == endState ) {
                      gsap.to( progressBar, {
                        x: `${state}%`,
                        duration: 1,
                        backgroundColor: '#4895ef',
                        onComplete: () => {
                            progressBarText.style.display = "initial";
                            progressBarContainer.style.boxShadow = '0 0 5px #4895ef';
                            // log.style.display = 'block';
                            content += '<h4> Imported courses </h4>';
                            for( var i = 0; i < c_ids.length ; i++ ) {
                                content += '<li><a href="'+c_ids[i].link+'">'+ c_ids[i].name + '</a></li>'; 
                            }
                            $( log ).html( content  );
                            $( log ).slideDown( 500 );

                        }
                      });
                    } else {
                      gsap.to(progressBar, {
                        x: `${state}%`,
                        duration: 1,
                      });
                    }
                  }, randomTime + time);
                  time += randomTime;
                })
            },
            uploadFileForm: function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: LDCIEVars.ajaxurl,
                    type: 'POST',
                    data: formData,
                    cache: false,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    beforeSend: function (jqXHR, settings) {
                        $('#ld_cie_loader_spinner').show();
                        $('#ld_cie_import_messages').html('');
                    },
                    success: function (data, textStatus, jqXHR) {
                        console.log( data );
                        if ( data == 0 ){
                            $( '#ld_cie_import_messages' ).html( LDCIEVars.err_msg1 ).slideDown( 500 );
                            $( '#ld_cie_import_messages' ).delay( 2000 ).slideUp();;
                            return;
                        } else if ( data.success == true ){
                            $( '#ld_cie_import_step1' ).delay( 200 ).slideUp();      
                            $( '#ld_cie_import_step2' ).delay( 1000 ).slideDown();
                            LD_CIE_Admin.LoadProgressBar( data.c_ids );
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $( '#ld_cie_import_messages' ).html( LDCIEVars.err_msg2 ).slideDown( 500 );
                        $( '#ld_cie_import_messages' ).delay( 2000 ).slideUp();;

                    },
                    complete: function (jqXHR, textStatus) {
                        $( '#ld_cie_loader_spinner' ).hide();
                    }
                });
            },
        };

        LD_CIE_Admin.init();
        
        $( '#ldcie_import_file' ).each( function() {
            var $input   = $( this ),
                $label   = $input.next( 'label' ),
                labelVal = $label.html();

            $input.on( 'change', function( e )
            {
                var fileName = '';

                if( this.files && this.files.length > 1 )
                    fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
                else if( e.target.value )
                    fileName = e.target.value.split( '\\' ).pop();

                if( fileName )
                    $label.find( 'span' ).html( fileName );
                else
                    $label.html( labelVal );
            });

            // Firefox bug fix
            $input
                .on( 'focus', function(){ $input.addClass( 'has-focus' ); })
                .on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
        });

        if ( $('#ld_cie_import_messages').text() === '' ){
            $( '#ld_cie_import_messages' ).hide();
        }
    
        // Toggle settings description.
        $( document ).ready(function() {
            $(".qst-mrk-tbl-ldcie svg, .qst-mrk-tbl-ldcie i").click(function(){
                $(this).parent('.qst-mrk-tbl-ldcie label').next('p.description').fadeToggle();
            });
        });
    });


})( jQuery );