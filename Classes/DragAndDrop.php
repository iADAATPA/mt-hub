<?php

/**
 * 
 * @author MarekM
 *
 */
class DragAndDrop
{
    private $folder = 'Images';
    private $filesLimit = 1;
    private $legalFileExtensions = ['jpg', 'png', 'gif', 'jpeg'];
    private $databaseTable = null;
    private $customCallback = null;

    /**
     * DragAndDrop constructor.
     */
    public function __construct()
    {
    }

    /**
     * This function prints all drag and drop javascript related functions
     *
     * @param string $id            
     */
    public function makeDragAndDropZone($id)
    {
        ?>

		<script type="text/javascript">
        
        	$(document).ready(function() {
        		// Add html code for status/progress bar
				$('#<?php echo $id; ?>').after('<div id="fileUploadingStatus" class="box-body"><div id="<?php echo $id; ?>StatusBar"></div></div>');
        		window.rowCount<?php echo $id; ?> = 0;
        		window.legalTypes<?php echo $id; ?> = <?php echo $this->getLegalFileExtensions(); ?>;
        		window.obj<?php echo $id; ?> = $('#<?php echo $id; ?>');
        		window.statusBar<?php echo $id; ?> = $('#<?php echo $id; ?>StatusBar');

    	        obj<?php echo $id; ?>.on('dragenter', function(e) {
    	            e.stopPropagation();
    	            e.preventDefault();

		    		$('#<?php echo $id; ?>').addClass('filesover');
    	        });

    	        obj<?php echo $id; ?>.on('dragover', function(e) {
    	             e.stopPropagation();
    	             e.preventDefault();

    	             $('#<?php echo $id; ?>').addClass('filesover');
    	        });

    	        obj<?php echo $id; ?>.on('drop', function(e) {
    	            e.preventDefault();
    	            var files = e.originalEvent.dataTransfer.files;

    	            $('#<?php echo $id; ?>').removeClass('filesover');
    	            //We need to send dropped files to Server
    	            handleFileUpload<?php echo $id; ?>(files, obj<?php echo $id; ?>, statusBar<?php echo $id; ?>);
    	        });

    	     	// On drag leave remove the blue background
				obj<?php echo $id; ?>.on( 'dragleave', function( e ){
		    		e.stopPropagation();
		    		e.preventDefault();

		    		 $('#<?php echo $id; ?>').removeClass('filesover');
				});

    	        $(document).on('dragenter', function(e) {
    	            e.stopPropagation();
    	            e.preventDefault();
    	        });

    	        $(document).on('dragover', function(e) {
    	              e.stopPropagation();
    	              e.preventDefault();
    	        });

    	        $(document).on('drop', function(e) {
    	            e.stopPropagation();
    	            e.preventDefault();
    	        });
    		});

        	function isExtensionOk<?php echo $id; ?>(filename){
            	if (legalTypes<?php echo $id; ?>.length > 0){
                	// Convert the filename to lower case
                	filename = filename.toLowerCase();
            		for(var i = 0; i < legalTypes<?php echo $id; ?>.length; i++){
            			var type = '.' + legalTypes<?php echo $id; ?>[i];
            			re = new RegExp(type +"$","g");
            			ExtensionIsOk = re.test(filename);
    
            			if(ExtensionIsOk){
            				return true;
            			}
            		}
    
            		return false;
            	} else {
					return true;
                }
        	}

			function handleFileUpload<?php echo $id; ?>(files,obj,statusBar) {
        		var filenames = "";
        	  	var filesizes = "";
        	  	var filesLimit = parseInt(<?php echo $this->getFilesLimit(); ?>);

        	  	for (var i = 0; i < files.length; i++) {
                    var folder = '<?php echo $this->getFolder(); ?>/<?php echo ucwords($this->getDatabaseTable()) ?>/';
                    var filename = "";
        	       	filename =  files[i].name;
                    var displayname = filename;
                    // Cut long file names
                    if (filename.length > 40) {
                        displayname = filename.substring(0, 37) + "...";
                    }

                    // We change the file name to the user id or account id
                    var fileExtension = filename.split('.').pop();
                    filename = '<?php echo $this->getDatabaseTable(); ?>' == 'accounts' ? <?php echo Session::getAccountId(); ?> : <?php echo Session::getUserId(); ?>;
                    filename = filename + '.' + fileExtension;

        	      	var fd = new FormData();
                    // Using this we can set progress.
                    var status = new createStatusBar<?php echo $id; ?>(statusBar);
        	       	status.setFileNameSize(displayname,files[i].size);
        	      	
        	    	if(!isExtensionOk<?php echo $id; ?>(filename)){
        				status.fileError.html(' Error: Unsupported file type.');
        				status.progressBar.find('.progress-bar').removeClass('progress-bar-aqua').addClass('progress-bar-red');
    	                status.setProgress(2.5);
    	             	status.statusbar.delay(7000).fadeOut('800');
        				checkStatusAndRefresh<?php echo $id; ?>(status);
        				
        				continue;
        			}

                    fd.append('key', '<?php echo $this->getFolder(); ?>/' + filename);
	               	fd.append('filename',filename);
	               	fd.append('fileext', files[i].type);
	               	fd.append('maxSize', '314572800');
	               	fd.append('folder', folder);
	               	fd.append('file', files[i]);

        	        // Check the size of the file and if it too big (more than 100MB) dont upload it
    	           	if (filesLimit != 0 && filesLimit <= i) {
    	           		status.fileError.html(' Error: Maximum number of files reached for this upload. Please try again.');
    	                status.progressBar.find('.progress-bar').removeClass('progress-bar-aqua').addClass('progress-bar-red');
    	                status.setProgress(2.5);
    	             	status.statusbar.delay(7000).fadeOut('800');
    	                checkStatusAndRefresh<?php echo $id; ?>(status);
        	  		} else if (files[i].size < 314572800) {
    	           		sendFileToServer<?php echo $id; ?>(fd, status);
    	                filenames = (filenames == "") ? filename : filenames + ";" + filename;
    	                filesizes = (filesizes == "") ? files[i].size : filesizes + ";" + files[i].size;
                        // Check if uploading is done and if it is refresh the page
                        checkStatusAndRefresh<?php echo $id; ?>(status);
    	            } else {
    	                status.fileError.html(' Error: File too big (max size: <?php echo Helper::formatBytes(314572800); ?>)');
    	                status.progressBar.find('.progress-bar').removeClass('progress-bar-aqua').addClass('progress-bar-red');
    	                status.setProgress(2.5);
    	              	status.statusbar.delay(7000).fadeOut('800');
    	                checkStatusAndRefresh<?php echo $id; ?>(status);
    	            }
        		}

        	  	if (filenames != "") {
        	       	$.post(
        	       	    "Ajax/imageupload.php",
                        {
                            table: "<?php echo $this->getDatabaseTable(); ?>",
                            filenames: filenames,
                            filesizes: filesizes,
                            <?php Csrf::printParameters("ImageUpload"); ?>
                        },
                        function(result){
                            <?php if ($this->getCustomCallback()) {
                              echo $this->getCustomCallback() . ';';
                            } ?>


                            if (result.indexOf('Users') !== -1) {
                                $('.profile-user-img').attr('src',result);
                                $('#headerProfileImage').attr('src',result);
                                $('.deletePicture').show();
                            } else if (result.indexOf('Accounts') !== -1) {
                                $('.logo-img').attr('src',result);
                                $('.deletePicture').show();
                            }
                        }
        	        );
        	   	}
        	}

        	function checkStatusAndRefresh<?php echo $id; ?>(status) {
        		var interval = setInterval(function() {
            	 	if (status.getProgress() == 100 || status.getProgress() == 2.5) {
            	  		if (rowCount<?php echo $id; ?> > 1) {
            	      		var completedRows = 0;
            	           	$(".progress-bar").parent().parent().each(function() {
            	           		if ($(this).css('display') == 'none') {
            	               		completedRows++;
            	           		}
            	            });
    
            	           	if (completedRows > rowCount<?php echo $id; ?> -1){
            	            	clearInterval(interval);
            	          	}
            	   		} else {
            	       		clearInterval(interval);
            	   		}
            	    }
        		}, 2000);
        	 }

     		function sendFileToServer<?php echo $id; ?>(formData, status) {
                window.uploadURL<?php echo $id; ?> = 'Ajax/uploadfile.php';

        		//Extra Data.
        	   	var extraData = {}; 
        	   	var jqXHR = $.ajax({
        	   		xhr: function() {
        	       		var xhrobj = $.ajaxSettings.xhr();
        	            if (xhrobj.upload) {
        	            	xhrobj.upload.addEventListener('progress', function(event) {
            	              	var percent = 0;
            	                var position = event.loaded || event.position;
            	                var total = event.total;
    
            	               	if (event.lengthComputable) {
            	              		percent = Math.ceil(position / total * 100);
            	               	}

            	                //Set progress
            	                if (percent < 100) {
            	            		status.setProgress(percent);
            	                }
            	        	}, false);
            	      	}
    
            	      	return xhrobj;
            	 	},
    				url: uploadURL<?php echo $id; ?>,
            	   	type: "POST",
    	            contentType:false,
    	            processData: false,
    	            cache: false,
    	            data: formData,
    	            success: function(data){
    	                status.setProgress(100);
    	            	status.statusbar.delay(7000).fadeOut('800');
    
    	                return;
    	            }
    	    	});
	    	}

        	function createStatusBar<?php echo $id; ?>(obj) {
	        	rowCount<?php echo $id; ?>++;

	         	this.statusbar = $("<div class='statusbar font-lsmall'></div>");
	         	this.filename = $("<span class='text-bold'></span>").appendTo(this.statusbar);
	        	this.progressPerc = $("<span></span>").appendTo(this.statusbar);
	         	this.size = $(" <span></span>").appendTo(this.statusbar);
	         	this.fileError = $("<span class='text-red'></span>").appendTo(this.statusbar);
	         	this.progressBar = $("<div class='progress progress-sm active'><div class='progress-bar progress-bar-aqua' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100'></div></div>").appendTo(this.statusbar);
	         	obj.after(this.statusbar);

	         	this.setFileNameSize = function(name, size) {
	            	var sizeStr = "";
	             	var sizeKB = size/1024;

	             	if (parseInt(sizeKB) > 1024) {
	                 	var sizeMB = sizeKB/1024;
	                 	sizeStr = sizeMB.toFixed(2)+" MB";
	             	} else {
	                 	sizeStr = sizeKB.toFixed(2)+" KB";
	             	}

	             	this.filename.html(name.replace(/·/g, ' '));
	             	this.size.html(sizeStr + ') ');
	         	}

        	 	this.setProgress = function(progress) {
        	   		var progressBarWidth = progress * this.progressBar.width()/ 100;
        	       	this.progressBar.find('div').animate({ width: progressBarWidth }, 10);
        	       	this.progressPerc.val(progress);

        	       	if (progress < 100) {
        	      		this.progressPerc.html(' (' + progress + '% of ');
        	       	} else if (progress == 100) {
        	       		this.progressBar.find('.progress-bar').toggleClass('progress-bar-aqua progress-bar-green');  	
            	      	this.progressPerc.html(' (' + progress + '% of ');
            	    }
        	  	}

        	    this.getProgress = function() {
        	  		return this.progressPerc.val();
        	    }
        	}
    	
        </script>

		<?php
    }

    /**
     * Set upload folder 
     * 
     * @param string $folder
     */
    private function setFolder($folder)
    {
        $this->folder = $folder;
    }
    
    /**
     * Get upload folder
     */
    private function getFolder()
    {
        return $this->folder;
    }
    
    /** 
     * Set file upload limit
     * 
     * @param int $limit
     */
    public function setFilesLimit($limit)
    {
        $this->filesLimit = is_int($limit) ? $limit : $this->filesLimit;
    }

    /**
     * Get upload files limit
     */
    private function getFilesLimit()
    {
        return $this->filesLimit;
    }
    
    /**
     * Set legal file extensions
     * 
     * @param array $extensionsArray
     */
    public function setLegalFileExtensions($extensionsArray) 
    {
        $this->legalFileExtensions = $extensionsArray;
    }
    
    /**
     * Get lefal file extensions
     * 
     * @return string
     */
    private function getLegalFileExtensions()
    {
        $legalFileExtensions = $this->legalFileExtensions;
        $legalFileExtensions = is_array($legalFileExtensions) ? json_encode($legalFileExtensions) : json_encode([]);
        
        return $legalFileExtensions;
    }

    /**
     * Set database table name to store uplaoded files in
     * 
     * @param string $tableName
     */
    public function setDatabaseTable($tableName)
    {
        $this->databaseTable = $tableName;
    }
    
    /**
     * Get the database table name
     */
    private function getDatabaseTable()
    {
        return $this->databaseTable;
    }
    
    /**
     * Set custom callback function name.
     * Pass javascript function name with brackets eg. 'showAlert()'.
     * No semi colon at the end of the function
     * 
     * @param string $callbackFunnctionName
     */
    public function setCustomCallback($callbackFunnctionName) 
    {
        $this->customCallback = $callbackFunnctionName;
    }
    
    /**
     * Get custom callback function name
     */
    private function getCustomCallback() 
    {
        return $this->customCallback;
    }
}
