<?php
/*
 * Helper functions for building a DataTables server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 *
 * See http://class.datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://class.datatables.net/license_mit
 */

/**
 * Class DataTables
 */
class DataTables extends Database
{
    /**
     * @var null|string
     */
    private $tableId = null;

    /**
     * @var null|string
     */
    private $emptyMessage = null;

    /**
     * @var null|boolean
     */
    private $serverSide = true;

    /**
     * @var null|string
     */
    private $scrolly = '380';

    /**
     * @var null|int
     */
    private $sortCol = 1;

    /**
     * @var null|string
     */
    private $sortOrder = 'desc';

    /**
     * @var null|string
     */
    private $ajaxCallBack = null;

    /**
     * @var null|boolean
     */
    private $autoRefresh = false;

    /**
     * @var null|string
     */
    private $emptyIcon = 'fa-exclamation-triangle';

    /**
     * @var null|string
     */
    private $formatters = '';

    /**
     * @var null|array
     */
    private $tableColumns = [];

    /**
     * @var null|boolean
     */
    private $lengthChange = true;

    /**
     * @var null|string
     */
    private $customToolbar = null;

    /**
     * @var null|string
     */
    private $filters = null;

    /**
     * @var null|boolean
     */
    private $showCheckBoxes = false;

    /**
     * @var null|boolean
     */
    private $searching = true;

    /**
     * @var null|boolean
     */
    private $paging = true;

    /**
     * @var null|boolean
     */
    private $enableBtnDownloadExcel = false;

    /**
     * @var null|boolean
     */
    private $enableBtnDownloadPdf = false;

    /**
     * @var null|boolean
     */
    private $enableBtnDownloadCsv = false;

    /**
     * @var null|boolean
     */
    private $enableBtnCopyToClipboard = false;

    /**
     * @var null|boolean
     */
    private $drillDownPageURL = null;

    /**
     * @var null|int
     */
    private $drillDownCol = 0;

    /**
     * @var null|string
     */
    private $drawCallback = null;

    /**
     * @var null|string
     */
    private $lengthMenu = "[[10, 25, 50, -1], [10, 25, 50, 'All']]";

    /**
     * @var null|string
     */
    private $customCol0 = false;

    /**
     * @var null|string
     */
    private $selectCallback = null;

    /**
     * Construct method
     */
    public function __construct() {}

    /**
     * Draw the datatable. This part will print all the neccessary javascript code to get the table fully working.
     */
    public function drawTable()
    {
        ?>

        <script type="text/javascript">

        	var emptyTableHTML = "<i class='fa <?php echo $this->getEmptyIcon(); ?> fa-fw fa-5x' aria-hidden='true'></i>";
            emptyTableHTML += "<div><?php echo $this->getEmptyMessage(); ?></div>";

            var serverSide = <?php echo $this->getServerProcessing() ? 'true' : 'false'; ?>;
            var lengthChange = <?php echo $this->getLengthChange() ? 'true' : 'false'; ?>;
            var searching = <?php echo $this->getSearching() ? 'true' : 'false'; ?>;
            var scrolly = "<?php echo $this->getScrolly(); ?>" + "px";
            var sortCol = <?php echo $this->getSortCol(); ?>;
            var sortOrder = "<?php echo $this->getSortOrder(); ?>";
            var ajaxCallBack = "<?php echo $this->getAjaxCallBack(); ?>";
            var lengthMenu = <?php echo !$this->getPaging() ? "[[-1], ['All']]" : $this->getLengthMenu(); ?>;
            var customToolbar = <?php echo $this->getCustomToolbar() ? '' : ''; ?>

            $.fn.dataTable.ext.errMode = 'none';

        	$(document).ready(function() {
        		window.<?php echo $this->getTableId(); ?> = $('#<?php echo $this->getTableId(); ?>')
                	.DataTable( {
        			dom: 'l<"#toolbar<?php echo $this->getTableId(); ?>">B<"clear">ftrp',
        			processing: true,
                    language: {
                        sEmptyTable: emptyTableHTML,
                        processing: '<i class="fa fa-2x fa-refresh fa-spin fa-fw"></i>',
                        loadingRecords: '',
                        search: ''
                    },
            		serverSide: serverSide,
            		scrollY: scrolly,
            		stateSave: true,
            		responsive:  {
                        type: 'column'
                    },
            		lengthChange: lengthChange,
            		createdRow: function ( row, data, index ) {
                        <?php echo $this->getDrillDownPageURL() ?  '$(row).addClass("expandable");' : ''; ?>
                    },
            		lengthMenu: lengthMenu,
                    searching: searching,
            		buttons: {
            			buttons: [
                      			<?php if ($this->getEnableBtnCopyToClipboard()) { ?>
                      			{
                          			extend: 'copy',
                		         	className: 'btn btn-sm btn-info outline',
                		         	text: '<i class="fa fa-clipboard fa-fw fa-lg fa-palegray" aria-hidden="true"></i>&nbsp;&nbsp;Save to clipboard'
                    		    },
                    		    <?php } ?>
                    		    <?php if ($this->getEnableBtnDownloadExcel()) { ?>
                    		    {
                        		    extend: 'excel',
                        		    className: 'btn btn-sm btn-info outline',
                        		   	text: '<i class="fa fa-file-excel-o fa-fw fa-lg fa-green" aria-hidden="true"></i>&nbsp;&nbsp;Save to Excel',
                                    // Only select columns that have data in them.
                                    // Where the aria-label has column (the only common trait I spotted to use as a selector - colinh).
                                    exportOptions: {
                                        columns: 'th[aria-label~="column"]'
                                    }
                            	},
								<?php } ?>
								<?php if ($this->getEnableBtnDownloadPdf()) { ?>
                            	{
                                	extend: 'pdf',
                                	className: 'btn btn-sm btn-info outline',
                                	text: '<i class="fa fa-file-pdf-o fa-fw fa-lg fa-red" aria-hidden="true"></i>&nbsp;&nbsp;Save to pdf'
                                },
								<?php } ?>
								<?php if ($this->enableBtnCopyToClipboard) { ?>
                                {
                                	extend: 'csv',
                                	className: 'btn btn-sm btn-info outline',
                                	text: '<i class="fa fa-file-text fa-fw fa-lg fa-deeporange" aria-hidden="true"></i>&nbsp;&nbsp;Save to csv'
                                },
                                <?php } ?>
            			]
            		},
            		order: [[sortCol, sortOrder]],
            	    ajax: {
            	   		url: ajaxCallBack,
            	   		cache: false,
                        type: "POST",
                        data: {
                            <?php Csrf::printParameters($this->getTableId()); ?>
                        }
            	    },
            	    columnDefs: [
                        <?php echo $this->printFormatters(); ?>
         	        ],
                    drawCallback: function() {
                        <?php if (!$this->getPaging()) { ?>
                    		$('.pagination').hide();
                    	<?php } ?>
                    	// add custom scroll bar
       	   				$('.dataTables_scrollBody').mCustomScrollbar({theme:'dark-thick', advanced:{updateOnContentResize: true}});

                        // Initialize select to for length option
                        $("[name='<?php echo $this->getTableId(); ?>_length']").select2({
                            // Disable search box
                            minimumResultsForSearch: -1
                        });

                        // Check height on the table body and resize empty table class
                        var tableHeight = $('#<?php echo $this->getTableId(); ?>').closest('.dataTables_scrollBody').height();
                        // Set the empty table height - 18px. That way we dont get any scroll bars
                        $('#<?php echo $this->getTableId(); ?> .dataTables_empty').height(tableHeight-18);

                        // Call other custom function
                        <?php echo $this->getDrawCallback();  ?>
                    }
            	});

        		// Redraw the table after resizing event. That way we make sure the columns width is correct
        		<?php echo $this->getTableId(); ?>.on('responsive-resize', function (e, datatable, columns) {
        			<?php echo $this->getTableId(); ?>.responsive.recalc().columns.adjust().draw(false);
        		} ).on('length.dt', function (e, settings, len) {
        			var tableResize = setInterval(function(){
                            <?php echo $this->getTableId(); ?>.columns.adjust().draw(false);
                            <?php echo $this->getTableId(); ?>.responsive.recalc().columns.adjust().draw(false);
                	}, 500);
                	// And stop the refreshing process after 2 sec.
        			setTimeout(
						function() {
							clearInterval(tableResize);
					}, 2000);
        		} );

        		$('.sidebar-toggle').click( function() {
            		// Resize the columns. We need to wait with execution of the code for a bit till the table is fully resized.
            		// As this depends form the server speed we refresh the table every 0.5 sec
            		var tableResize = setInterval(function(){
                            <?php echo $this->getTableId(); ?>.columns.adjust().draw(false);
                	}, 500);
                	// And stop the refreshing process after 2 sec.
        			setTimeout(
						function() {
							clearInterval(tableResize);
					}, 2000);

        			return;
        		});

        		$.fn.dataTable.ext.errMode = 'throw';

            	<?php if ($this->getAutoRefresh()) { ?>
            	// Autorefresh the table
            	window.interval = setInterval( function () {
            		<?php echo $this->getTableId(); ?>.ajax.reload(null, false);
                    <?php echo $this->getTableId(); ?>.columns.adjust().draw(false);
                    <?php echo $this->getTableId(); ?>.responsive.recalc().columns.adjust().draw(false);
                }, 30000 );
                <?php } ?>

                <?php if ($this->showCheckBoxes) { ?>
                // Add select all functionality to checkboxes
                $("#<?php echo $this->getTableId(); ?>-selectall").change(function() {
                	$('#<?php echo $this->getTableId(); ?> tbody tr td input[type="checkbox"]').not(":disabled").prop('checked', $(this).prop('checked'));
                	// Run callback function
                	<?php echo $this->getSelectCallback(); ?>
                });
                <?php } ?>

                <?php if ($this->getCustomToolbar()) { ?>
             	// Asign custom toolbar to the datatable
        		$("#toolbar<?php echo $this->getTableId(); ?>").html('<?php echo $this->getCustomToolbar(); ?>');
                <?php } ?>

                // Check if there are any filters set
                <?php if ($this->getFilters()) {
                    $filters = $this->getFilters();
                    // Get the first filter from the array which is always the All filter
                    $filterAll = array_shift($filters);
                    $excludedColumns = isset($filterAll['excludedColumns']) ? $filterAll['excludedColumns'] : [];
                ?>
                    // Set the click function for the all filter
                    $('#<?php echo $filterAll['buttonId']; ?>').click(function() {
                        // Excluded colums from the All filter. (like month search for billing)
                        var excludedColumns = <?php echo json_encode($excludedColumns); ?>
                        // Remove the filter class from all filter buttons so none are highlighted
                        $(this).siblings().removeClass('filter');
                        // Set the search for every column to empty
                        <?php echo $this->getTableId(); ?>.columns().every(function() {
                            // Check if the column number is not on the excluded list
                            if (excludedColumns.indexOf(this[0][0]) === -1) {
                                // Set the search to empty for the column
                                this.search('');
                            }
                        });
                        // Draw the table again
                        <?php echo $this->getTableId(); ?>.draw();
                    });

                    // For each filter set the click event function
                <?php foreach ($filters as $filter) { ?>

                        $('#<?php echo $filter['buttonId']; ?>').click(function() {
                            var column = <?php echo $filter['column']; ?>;
                            var search = <?php echo json_encode($filter['search'], JSON_FORCE_OBJECT); ?>;

                            // If the button is already active (filtering)
                            if ($(this).hasClass('filter')) {
                                // Remove the filter class so the button is no longer highlighted
                                $(this).removeClass('filter');
                                // Clear the search for that column and redraw the datatable
                                <?php echo $this->getTableId(); ?>.column(column).search('').draw();
                            } else {
                                // Else the button is not active (filtering)
                                // Find ONLY the other same filter group column buttons and
                                // remove their filter classes to turn off the highlighted button.
                                $(this).siblings('.filter-group-' + column).removeClass('filter');
                                $(this).addClass('filter');
                                // Add a filter group class for the column so we
                                // can select later which columns to clear and which to leave filtering
                                $(this).addClass('filter-group-' + column);
                                // Default search parameters for the column().search() function
                                var defaultSearch = {
                                    input: '',
                                    regex: false,
                                    smart: true
                                };
                                // Overwrite the default options with the custom options
                                // defined in this function's search parameter
                                search = $.extend(defaultSearch, search);
                                // Filter by column and redraw the datatable
                                <?php echo $this->getTableId(); ?>.column(column).search(
                                    search.input,
                                    search.regex,
                                    search.smart
                                ).draw();
                            }
                        });
                <?php
                    }
                }
                ?>

				<?php if ($this->getDrillDownPageURL()) { ?>
                $('#<?php echo $this->getTableId(); ?> tbody ').on('click', 'td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = <?php echo $this->getTableId(); ?>.row(tr);

                    if (typeof xhr !== 'undefined') {
                        xhr.abort();
                    }

                    if (tr.next().hasClass('child')) {
                    	return;
                    } else if (row.child.isShown()) {
                    	 // This row is already open - close it
                    	tr.removeClass('shown');
                    	row.child.hide();
                    	$('#<?php echo $this->getTableId(); ?> tbody').find('td.control').css('pointer-events', '');
                    } else {
                        if (tr.find('td.control').is(":visible")) {
                        	$('#<?php echo $this->getTableId(); ?> tbody').find('td.control').css('pointer-events', 'none');
                        }

                        var otherOpenTr = $('.shown');
                        var otherOpenRow = <?php echo $this->getTableId(); ?>.row(otherOpenTr);

                        if (otherOpenRow.child.isShown()) {
                            otherOpenTr.removeClass('shown');
                            otherOpenRow.child.hide();
                        }

                     	// Open this row
                        tr.addClass('shown');
                        row.child(loader).show();
                        tr.next().addClass('drillDownRow');
                        $('div.tableSlider', row.child()).slideDown(200);
                        xhr = $.post('<?php echo $this->getDrillDownPageURL(); ?>', { 'id' : row.data()[<?php echo $this->getDrillDownCol(); ?>]}, function(result) {
                            $('div.tableSlider', row.child()).slideUp(50);
                            var rowContent = result;
                            row.child(rowContent).show();
                            tr.next().addClass('drillDownRow');
                            $('div.tableSlider', row.child()).slideDown(600);
                            delete xhr;
                        });
                	}
                });
                <?php } ?>

        	});

     	</script>

        <?php
    }

    /**
     * Print html code for the table. The table id and columns need to be set before calling this function
     */
    public function printTable()
    {
        ?>

        <table id="<?php echo $this->getTableId(); ?>" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
       		<thead>
            	<tr>

        <?php
            foreach ($this->getTableColumns() as $index => $column) {
                $class = isset($column['class']) ? $column['class'] : '';
                $label = isset($column['label']) ? $column['label'] : '';
                $title = isset($column['title']) ? $column['title'] : $label;


                // As the 0 column is reserved for responsive drill down, we print the checkbox in the next column
                if ($this->showCheckBoxes && $index == 1) {
                   echo '<th class="dt-center" title="' . $title . '"><input id="' . $this->getTableId() . '-selectall" class="dt-checkboxes" value="" type="checkbox"></th>';
                } else {
                    echo '<th class="' . $class . '" title="' . $title . '">' . $label . '</th>';
                }
            }
        ?>

       			</tr>
            </thead>
            <tbody></tbody>
    	</table>

    	<?php
    }

    /**
     * Print column definitions in the datatable's js.
     */
    private function printFormatters()
    {
        // Check if we have to show checkboxes. If yes, add following code at the beggining of the formatters
        if ($this->getShowCheckBoxes()) {
            ?>

            {
            	targets: 0,
            	className: 'control',
            	orderable: false,
            	data: null,
            	defaultContent: '',
            	width: '1%'
            },
           	{
                targets: 1,
                orderable: false,
                render: function ( data, type, row ) {
                	return '<input type="checkbox" value="' + data + '" onchange="<?php echo $this->getSelectCallback(); ?>" class="dt-checkboxes">';
                },
                className: 'dt-body-center all',
                width: '1%'
            },

            <?php
        }

        // Check if we have to show checkboxes. If yes, add following code at the beggining of the formatters
        else if ($this->getDrillDownPageURL()) {
            ?>

            {
            	targets: 0,
            	className: 'control',
            	orderable: false,
            	data: null,
            	defaultContent: '',
            	width: '1%'
            },
           	{
                targets: 1,
                orderable: false,
                data: null,
                defaultContent: '',
                className: 'details-control',
                width: '1%'
            },

            <?php
        }

        else if (!$this->getCustomCol0()) {
            ?>

            {
            	targets: 0,
            	className: 'control',
            	orderable: false,
            	data: null,
            	defaultContent: '',
            	width: '1%'
            },

            <?php
        }

        // Check if formatters are set and print them on the page
        if ($this->getFormatters() && is_array($this->getFormatters())) {
            foreach ($this->getFormatters() as $rowFormatter){
                $targets = isset($rowFormatter['targets']) ? $rowFormatter['targets'] : null;

                if (($this->getDrillDownPageURL() || $this->getShowCheckBoxes()) && ($targets == 0 || $targets == 1)) {
                    continue;
                }

                $width = isset($rowFormatter['width']) ? $rowFormatter['width'] . '%' : 'auto';
                $renderData = isset($rowFormatter['renderdata']) ? '(data, type, row, ' . $rowFormatter['renderdata'] .' )' : '(data, type, row)';
                $render = isset($rowFormatter['render']) ?  $rowFormatter['render'] . $renderData : 'data';
                $orderable = isset($rowFormatter['orderable']) ? $rowFormatter['orderable'] : 'true';
                $visible = isset($rowFormatter['visible']) ? $rowFormatter['visible'] : 'true';
                $className = isset($rowFormatter['class']) ? $rowFormatter['class'] : 'null';
                $orderData = isset($rowFormatter['orderData']) ? $rowFormatter['orderData'] : $targets;
                ?>

               	{
                	targets: <?php echo $targets; ?>,
                	width: '<?php echo $width; ?>',
                	className: '<?php echo $className; ?>',
                	orderable: <?php echo $orderable; ?>,
                    orderData: <?php echo (int)$orderData; ?>,
                    visible: <?php echo $visible; ?>,
                    <?php if(!isset($rowFormatter['visible'])) { ?>
                	render: function (data, type, row) {
                   		return <?php echo $render; ?>;
                   	},
                   	<?php } ?>
              	},

                <?php
            }
        }
    }

    /**
     * Set table columns.
     *
     *  @param array $array An associative array of parameters that can have the following keys: <ul>
     * 	<li><code>label</code> - <code>string</code> - Required - A column label </li>
     * 	<li><code>title</code> - <code>string</code> - Optional - A column title. If not set, the column label will be used as a title</li>
     * 	<li><code>class</code> - <code>string</code> - Optional - A column class name. Eg. 'all' will display the column on all devices (for responsive tables).</li></ul>
     */
    public function setTableColumns($array)
    {
        $this->tableColumns = is_array($array) ? $array : [];
    }

    /**
     * Get table columns
     *
     * @return array
     */
    private function getTableColumns()
    {
        return $this->tableColumns;
    }

    /**
     * Enable or disable a 'Download to Csv' button
     *
     * @param boolean $boolean
     */
    public function setEnableBtnCopyToClipboard($boolean)
    {
        $this->enableBtnCopyToClipboard = is_bool($boolean) ? $boolean : false;
    }

    /**
     * Get if the download to csv button is enabled or disabled
     *
     * @return boolean
     */
    private function getEnableBtnCopyToClipboard()
    {
        return $this->enableBtnCopyToClipboard;
    }

    /**
     * Enable or disable a 'Download to Csv' button
     *
     * @param boolean $boolean
     */
    public function setEnableBtnDownloadCsv($boolean)
    {
        $this->enableBtnDownloadCsv = is_bool($boolean) ? $boolean : false;
    }

    /**
     * Get if the download to csv button is enabled or disabled
     *
     * @return boolean
     */
    private function getEnableBtnDownloadCsv()
    {
        return $this->enableBtnDownloadCsv;
    }

    /**
     * Set drill down page url
     *
     * @param string $url
     */
    public function setDrillDownPageURL($url)
    {
        $this->drillDownPageURL = $url;
    }

    /**
     * Get drill down page url
     */
    public function getDrillDownPageURL()
    {
        return $this->drillDownPageURL;
    }

    /**
     * Enable or disable a 'Download to Pdf' button
     *
     * @param boolean $boolean
     */
    public function setEnableBtnDownloadPdf($boolean)
    {
        $this->enableBtnDownloadPdf = is_bool($boolean) ? $boolean : false;
    }

    /**
     * Get if the download to pdf button is enabled or disabled
     *
     * @return boolean
     */
    private function getEnableBtnDownloadPdf()
    {
        return $this->enableBtnDownloadPdf;
    }

    /**
     * Enable or disable a 'Download to Excel' button
     *
     * @param boolean $boolean
     */
    public function setEnableBtnDownloadExcel($boolean)
    {
        $this->enableBtnDownloadExcel = is_bool($boolean) ? $boolean : false;
    }

    /**
     * Get if the download to excel button is enabled or disabled
     *
     * @return boolean
     */
    private function getEnableBtnDownloadExcel()
    {
        return $this->enableBtnDownloadExcel;
    }

    /**
     * Set custom toolbar
     *
     * @param string $customToolbar
     */
    public function setCustomToolbar($customToolbar)
    {
        $this->customToolbar = $customToolbar;
    }

    /**
     * Get custom toolbar
     */
    private function getCustomToolbar()
    {
        return $this->customToolbar;
    }

    /**
     * Hide or show the length change option (select with numbers of rows to display).
     * This will only work if there is no custom toolbar set
     *
     * @param boolean $boolean
     */
    public function setLengthChange($boolean)
    {
        $this->lengthChange = is_bool($boolean) ? $boolean : $this->lengthChange;
    }

    /**
     * Get LengthChange
     */
    private function getLengthChange()
    {
        return $this->lengthChange;
    }

    /**
     * Set column formatters
     *
     * @param array $formatters
     */
    public function setFormatters($formatters)
    {
        $this->formatters = $formatters;
    }

    /**
     * Get column formatters
     */
    private function getFormatters()
    {
        return $this->formatters;
    }

    /**
     * Set table autorefresh
     *
     * @param boolean $boolean
     */
    public function setAutoRefresh($boolean)
    {
        $this->autoRefresh = is_bool($boolean) ? $boolean : $this->autoRefresh;
    }

    /**
     * Get autorefresh
     *
     * @return boolean
     */
    private function getAutoRefresh()
    {
        return $this->autoRefresh;
    }

    /**
     * Set a server side script url for the table data
     *
     * @param string $url
     */
    public function setAjaxCallBack($url)
    {
        $this->ajaxCallBack = preg_replace('/\s+/', '', $url);
    }

    /**
     * Get a table data server side script url
     */
    private function getAjaxCallBack()
    {
        return $this->ajaxCallBack;
    }

    /**
     * Set column order direction
     *
     * @param string $order desc or asc
     */
    public function setSortOrder($order)
    {
        $this->sortOrder = in_array($order, ['desc', 'asc']) ? $order : $this->sortOrder;
    }

    /**
     * Get column order direction
     */
    private function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set sort column id
     *
     * @param int $num
     */
    public function setSortCol($num)
    {
        $this->sortCol = is_int($num) ? $num : 0;
    }

    /**
     * Get sort column id
     */
    private function getSortCol()
    {
        return $this->sortCol;
    }

    /**
     * Set table id
     *
     * @param string $id
     */
    public function setTableId($id)
    {
        $this->tableId = preg_replace('/\s+/', '', $id);
    }

    /**
     * Get table id
     *
     * @return $string
     */
    public function getTableId()
    {
        return $this->tableId;
    }

    /**
     * Enable or disable check boxes in the first column of a datatable
     *
     * @param boolean $boolean
     */
    public function setShowCheckBoxes($boolean)
    {
        $this->showCheckBoxes = is_bool($boolean) ? $boolean : false;
    }

    /**
     * Show check boxes
     *
     * @return boolean
     */
    private function getShowCheckBoxes()
    {
        return $this->showCheckBoxes;
    }

    /**
     * Set scrolly - table height
     *
     * @param string $scrolly
     */
    public function setScrolly($scrolly)
    {
        $this->scrolly  = $scrolly;
    }

    /**
     * Get scrolly
     *
     * @return string
     */
    private function getScrolly()
    {
        return $this->scrolly;
    }

    /**
     * Get empty message displayed when there is no data in table to display
     */
    private function getEmptyMessage()
    {
        return $this->emptyMessage;
    }

    /**
     * Set icon name for empty message displayed when there is no data in the table to display
     *
     * @param string $string - we only accept icon names from <a href="http://fontawesome.io/icons/">www.fontawesome.io</a>
     */
    public function setEmptyIcon($string)
    {
        $this->emptyIcon = $string;
    }

    /**
     * Get empty icon name
     *
     * @return string
     */
    private function getEmptyIcon()
    {
        return $this->emptyIcon;
    }

    /**
     * Set empty table message
     *
     * @param string $string
     */
    public function setEmptyMessage($string)
    {
        $this->emptyMessage = $string;
    }


    /**
     * Set which column's value will be passed to drill down page url
     * @param unknown $int
     */
    public function setDrillDownCol($int)
    {
        $this->drillDownCol = is_int($int) ? $int : 0;
    }

    /**
     * Pass column's value to drill down page url
     */
    public function getDrillDownCol()
    {
        return $this->drillDownCol;
    }

    /**
     * @return bool
     */
    public function getServerProcessing()
    {
        return $this->serverSide;
    }

    /**
     * @param boolean $serverProcessing
     */
    public function setServerProcessing($serverProcessing)
    {
        $this->serverSide = $serverProcessing;
    }

    /**
     * @return null|string
     */
    public function getDrawCallback()
    {
        if (!empty($this->drawCallback)) {
            return $this->drawCallback . '();';
        } else {
            return $this->drawCallback;
        }
    }

    /**
     * @param boolean $drawCallback
     */
    public function setDrawCallback($drawCallback)
    {
        $this->drawCallback = $drawCallback;
    }

    /**
     * @return null|string
     */
    public function getSelectCallback()
    {
        if (!empty($this->selectCallback)) {
            return $this->selectCallback . '();';
        } else {
            return $this->selectCallback;
        }
    }

    /**
     * @param boolean $selectCallback
     */
    public function setSelectCallback($selectCallback)
    {
        $this->selectCallback = $selectCallback;
    }

    /**
     * Enable or disable a datatable search box
     *
     * @param boolean $boolean
     */
    public function enableSearching($boolean)
    {
        $this->searching = is_bool($boolean) ? $boolean : $this->searching;
    }

    /**
     * Get searching option (search box)
     */
    private function getSearching()
    {
        return $this->searching;
    }


    /**
     * Enable or disable a datatable paging
     *
     * @param boolean $boolean
     */
    public function setPaging($boolean)
    {
        $this->paging = is_bool($boolean) ? $boolean : $this->paging;
    }

    /**
     * Get paging option
     */
    private function getPaging()
    {
        return $this->paging;
    }

    /**
     * @param $boolean
     */
    public function setCustomCol0($boolean)
    {
        $this->customCol0 = is_bool($boolean) ? $boolean : $this->customCol0;
    }

    /**
     * Get custom column option
     */
    private function getCustomCol0()
    {
        return $this->customCol0;
    }

    /**
     * Sets the interval values for the length change menu.
     * Send in the format [[value, label], [value, label], [value, label], etc...]
     *
     * @param array $lengthMenu
     */
    public function setLengthMenu($lengthMenu)
    {
        if (is_array($lengthMenu)) {
            $arrayOne = "";
            $arrayTwo = "";

            foreach($lengthMenu as $value) {
                $arrayOne .= ", '" . $value[0] . "'";
                $arrayTwo .= ", '" . $value[1] . "'";
            }

            $result = "[[" . substr($arrayOne, 2) . "], [" . substr($arrayTwo, 2) . "]]";
            $this->lengthMenu = $result;
        }
    }

    /**
     * Gets the menu length intervals
     */
    public function getLengthMenu()
    {
        return $this->lengthMenu;
    }

	/**
	 * Create the data output array for the DataTables rows
	 *
	 *  @param  array $columns Column information array
	 *  @param  array $data    Data from the SQL get
	 *  @return array          Formatted data in a row based format
	 */
	static function data_output ( $columns, $data )
	{
		$out = [];
		for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
			$row = [];
			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];
				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
					$row[ $column['dt'] ] = $column['formatter']( $data[$i][ self::formatColumnName($column['db']) ], $data[$i] );
				}
				else {
					$row[ $column['dt'] ] = $data[$i][ self::formatColumnName($columns[$j]['db']) ];
				}
			}
			$out[] = $row;
		}
		return $out;
	}

    /**
     * @param $column
     * @return mixed
     */
	static function formatColumnName($column)
	{
	    $withDot = strpos($column, '.');
	    $withAS = strpos(strtolower($column), ' as ');

	    if ($withAS !== false) {
	        $temp = explode(' ', $column);
            return end($temp);
        } else if ($withDot !== false) {
	        $temp = explode('.', $column);
	        return end($temp);
	    } else {
	       return $column;
	    }
	}

	/**
	 * Paging
	 *
	 * Construct the LIMIT clause for server-side processing SQL query
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @return string SQL limit clause
	 */
	static function limit ( $request, $columns )
	{
		$limit = '';
		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
		}
		return $limit;
	}
	/**
	 * Ordering
	 *
	 * Construct the ORDER BY clause for server-side processing SQL query
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @return string SQL order by clause
	 */
	static function order ( $request, $columns )
	{
		$order = '';
		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = [];
			$dtColumns = self::pluck( $columns, 'dt' );
			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				if ( $requestColumn['orderable'] == 'true' ) {
					$dir = $request['order'][$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';
					$orderBy[] = $column['db'].' '.$dir;
				}
			}
			$order = 'ORDER BY '.implode(', ', $orderBy);
		}
		return $order;
	}
	/**
	 * Searching / Filtering
	 *
	 * Construct the WHERE clause for server-side processing SQL query.
	 *
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here performance on large
	 * databases would be very poor
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @param  array $bindings Array of values for PDO bindings, used in the
	 *    executeSql() function
	 *  @return string SQL where clause
	 */
	static function filter ( $request, $columns, &$bindings, $userSearch = null )
	{
		$globalSearch = [];
		$columnSearch = [];
		$dtColumns = self::pluck( $columns, 'dt' );
		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];
			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				if ( $requestColumn['searchable'] == 'true' ) {
					$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $withAS = strpos(strtolower($column['db']), ' as ');

                    if ($withAS !== false) {
                        $column['db'] = end((explode(' ', $column['db'])));
                    }

                    $globalSearch[] = $column['db']." LIKE ".$binding;
				}
			}
		}
		// Individual column filtering
		if ( isset( $request['columns'] ) ) {
			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				$str = $requestColumn['search']['value'];
				if ( $requestColumn['searchable'] == 'true' &&
				 $str != '' ) {
                    $withAS = strpos(strtolower($column['db']), ' as ');

                    if ($withAS !== false) {
                        $column['db'] = end((explode(' ', $column['db'])));
                    }

					if($requestColumn['search']['regex']){
					   $binding = self::bind( $bindings, $str, PDO::PARAM_STR );
					   $columnSearch[] = $column['db']." REGEXP ".$binding;
					} else {
					   $binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
					   $columnSearch[] = $column['db']." LIKE ".$binding;
					}
				}
			}
		}

		// Combine the filters into a single string
		$where = '';
		if ( count( $globalSearch ) ) {
			$where = '('.implode(' OR ', $globalSearch).')';
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where .' AND '. implode(' AND ', $columnSearch);
		}

		if ( $where !== '' ) {
			$where = 'WHERE '.$where;
			$where .= !empty($userSearch) ? ' AND ' . $userSearch . ' ' : '';
		} elseif ($userSearch) {
		    $where = 'WHERE ' . $userSearch . ' ';
		}

		return $where;
	}
	/**
	 * Perform the SQL queries needed for an server-side processing requested,
	 * utilising the helper functions of this class, limit(), order() and
	 * filter() among others. The returned array is ready to be encoded as JSON
	 * in response to an SSP request, or can be modified if needed before
	 * sending back to the client.
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array|PDO $conn PDO connection resource or connection parameters array
	 *  @param  string $table SQL table to query
	 *  @param  string $primaryKey Primary key of the table
	 *  @param  array $columns Column information array
	 *  @return array          Server-side processing response array
	 */
	public function getData($table, $primaryKey, $columns, $userSearch = null, $join = null, $union = null)
	{
	    $request = $_POST;

		$bindings = [];
		// Build the SQL query string from the request
		$limit = self::limit($request, $columns);
		$order = self::order($request, $columns);
		$where = self::filter($request, $columns, $bindings, $userSearch);

		$join = $join ? $join : '';
		$query = "SELECT " . implode(", ", self::pluck($columns, 'db')) . "
			 FROM $table " . $join . "
			 $where
			 $order
			 $limit";

	    // Check if we have to union two tables
        if ($union) {
            $unionWhere = self::filter($request, $union['columns'], $bindings, $union['usersearch']);
            $unionQuery = "SELECT " . implode(", ", self::pluck($union['columns'], 'db')) . "
                FROM " . $union['table'] . "
			    $unionWhere
			    $limit";

			// Merge two queries
			$query = "(" . $query . ") UNION ALL (" . $unionQuery . ")";
	    }

	    // Main query to actually get the data
		$data = self::executeSql(
		    $bindings,
	        $query
		);

		// Data set length after filtering
		$resFilterLength = self::executeSql(
		    $bindings,
			"SELECT COUNT({$primaryKey})
			 FROM   $table ". $join ."
			 $where",
			 true
		);

		$recordsFiltered = isset($resFilterLength[0][0]) ? $resFilterLength[0][0] : null;

		$where = empty($userSearch) ? '' : ' WHERE ' . $userSearch . ' ';
		// Total data set length
		$resTotalLength = self::executeSql(
		    null,
			"SELECT COUNT({$primaryKey})
			 FROM   $table ". $join ."
			 $where",
			true
		);

		$recordsTotal = isset($resTotalLength[0][0]) ? $resTotalLength[0][0] : null;

		//Output
		$result = [
			"draw"            => isset ( $request['draw'] ) ?
				intval( $request['draw'] ) :
				0,
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => self::data_output( $columns, $data )
		];

		echo json_encode($result);
	}

	/**
	 * Execute an SQL query on the database
	 *
	 * @param  resource $db  Database handler
	 * @param  array    $bindings Array of PDO binding values from bind() to be
	 *   used for safely escaping strings. Note that this can be given as the
	 *   SQL query string if no bindings are required.
	 * @param  string   $sql SQL query to execute.
	 * @return array         Result from the query (all rows)
	 */
	public function executeSql($bindings, $sql = null, $fetchNum = null)
	{
        // Argument shifting
        if ($sql === null) {
            $sql = $bindings;
        }

        $this->query($sql);

        // Bind parameters
        if (is_array($bindings)) {
            for ($i = 0, $ien = count($bindings); $i < $ien; $i++) {
                $binding = $bindings[$i];
                $this->bindValue($binding['key'], $binding['val'], $binding['type']);
            }
        }

        // Execute
        try {
            $result = $fetchNum ? $this->resultSetNum() : $this->resultset();
        } catch (PDOException $e) {
            error_log($e);
            self::fatal("An error occurred");
        }

        // Return all
        return $result;
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Internal methods
	 */
	/**
	 * Throw a fatal error.
	 *
	 * This writes out an error message in a JSON string which DataTables will
	 * see and show to the user in the browser.
	 *
	 * @param  string $msg Message to send to the client
	 */
	static function fatal ( $msg )
	{
		error_log($msg);

		exit(0);
	}
	/**
	 * Create a PDO binding key which can be used for escaping variables safely
	 * when executing a query with executeSql()
	 *
	 * @param  array &$a    Array of bindings
	 * @param  *      $val  Value to bind
	 * @param  int    $type PDO field type
	 * @return string       Bound key to be used in the SQL where this parameter
	 *   would be used.
	 */
	static function bind ( &$a, $val, $type )
	{
		$key = ':binding_'.count( $a );
		$a[] = [
			'key' => $key,
			'val' => $val,
			'type' => $type
		];
		return $key;
	}
	/**
	 * Pull a particular property from each assoc. array in a numeric array,
	 * returning and array of the property values from each item.
	 *
	 *  @param  array  $a    Array to get data from
	 *  @param  string $prop Property to read
	 *  @return array        Array of property values
	 */
	static function pluck ( $a, $prop )
	{
        $out = [];
        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }

        return $out;
	}
	/**
	 * Return a string from an array or a string
	 *
	 * @param  array|string $a Array to join
	 * @param  string $join Glue for the concatenation
	 * @return string Joined string
	 */
	static function _flatten ( $a, $join = ' AND ' )
	{
        if (!$a) {
            return '';
        } elseif ($a && is_array($a)) {
            return implode($join, $a);
        }

        return $a;
	}

    /**
     * Get the value of Server Side
     *
     * @return mixed
     */
    public function getServerSide()
    {
        return $this->serverSide;
    }

    /**
     * Set the value of Server Side
     *
     * @param mixed serverSide
     *
     * @return self
     */
    public function setServerSide($serverSide)
    {
        $this->serverSide = $serverSide;

        return $this;
    }

    /**
     * Get the value of Filters
     *
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set the value of Filters
     *
     * @param mixed filters
     *
     * @return self
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Set the value of Searching
     *
     * @param mixed searching
     *
     * @return self
     */
    public function setSearching($searching)
    {
        $this->searching = $searching;

        return $this;
    }

}
