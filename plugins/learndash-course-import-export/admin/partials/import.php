<div class="wn_wrap ldcie-imp-panel">
    <div id="ld_cie_import_messages" style="display: none;">
    </div>
    <div id="ld_cie_content_wrap">
            <div id="ld_cie_loader_spinner" style="display: none;">
                <div class="ld_cie_spinner"></div>
            </div>
            <form id="ld_cie_upload_file_form" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" id="ld_cie_action" value="ld_cie_upload_excel_file">
                <input type="hidden" name="ld_cie_ajax_nonce" value="<?php echo wp_create_nonce( 'ld-qie-ajax-nonce' ); ?>">
                <div id="ld_cie_import_step1">
                    <div class="ldcie-box-file"><h2><?php _e( "Please select Microsoft Excel file (XLS/XLSX) to import", "learndash-course-import-export" ); ?></h2></div>
                    <div class="import-excel-page">
                        <div class="content">
                            <div class="box">
                                <div class="box-lsdie-main">
                                <input type="file" id="ldcie_import_file" name="ldcie_import_file"
                                       class="inputfile inputfile-6" accept=".xls, .xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                <label for="ldcie_import_file"> 
                                    <strong>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='20' height='17'
                                             viewBox='0 0 20 17'>
                                            <path d='M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z'/>
                                        </svg>
                                        <?php _e('Browse', 'learndash-course-import-export'); ?>
                                    </strong><span></span>
                                </label></div>
                                 <div class="submit" style="text-align: center;">
                        <button class="button button-primary ld-cie-button" type="submit" name="import-btn"><?php _e('Next', 'learndash-course-import-export'); ?> <span class="dashicons dashicons-arrow-right-alt2 no-left-margin"></span>
                        </button>
                    </div>
                            </div>
                        </div>
                    </div>
                    
               
                </div><!-- Upload file -->
                <div class="imprt-frm-cubmt">
                <h3>Import/Export Standard Template Files</h3>
                <p>
                    <a class="button button-primary ld-qie-button" href="<?php echo LEARNDASH_COURSE_IMPORT_EXPORT_URL . 'sample-import-files/standard-template-updated.xls' ?>">
                        <?php _e( 'Download Template', 'learndash-course-import-export' ); ?> 
                    </a>
                </p>                
            </div>
            </form>

            <div id="ld_cie_import_step2" style="display:none;">
                <div class="container">
                    <div class="progress-bar__container">
                        <div class="progress-bar">
                            <span class="progress-bar__text"><?php _e( 'Imported Successfully!', 'learndash-course-import-export' ) ?></span>
                        </div>
                    </div>
                </div>
                <div class="import_logs"></div>
            </div>
    </div>
</div>
