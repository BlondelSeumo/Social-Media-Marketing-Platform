
<style type="text/css">
 /* Global Spinner Style */
.xit-spinner {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: none;
  z-index: 1000;
}
.xit-spinner i {
  position: absolute;
  top: 45%;
  left: 50%;
  transform: translate(-50%, -45%);
}

/* Styles for post planner */
#pp-upload-container #wizard-selected .wizard-step {
    cursor: pointer;
}

#pp-upload-container #wizard-selected .wizard-step-layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

#pp-upload-container .wizard-steps .wizard-step::before {
    display: none !important;
}

#pp-source-link i {
    font-size: 18px;
}

#pp-datatable-container {
    position: relative;
    min-height: 100px;
}   
</style>

<section class="section section_custom">

    <div class="section-header">
        <h1><i class="far fa-list-alt"></i> <?php echo $this->lang->line('Post Planner'); ?></h1>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item"><a href="<?php echo base_url('ultrapost') ?>"><?php echo $this->lang->line('Comboposter'); ?></a></div>
          <div class="breadcrumb-item"><?php echo $this->lang->line('Post planner'); ?></div>
        </div>
    </div><!-- ends section-header -->

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">

                        <div id="pp-csv-info" class="d-none">
                            <div class="alert alert-light alert-has-icon">
                                <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                                <div class="alert-body">
                                    <?php echo $this->lang->line("Upload a CSV file with the following header fields - <strong>campaign_name</strong>, <strong>campaign_type</strong>, <strong>message</strong>, and <strong>source</strong>. These fields must exist in the header of the CSV file. These are mandatory. But some values of them are optional. The order of the header fields should be in the order as you are seeing here but random order may not be a problem. The <strong>campaign_type</strong> must be <strong>text</strong>, <strong>image</strong>, or <strong>link</strong>."); ?>
                                </div>
                            </div>

                            <p class="text-center">
                                <a class="btn btn-link" href="<?php echo base_url('assets/post-planner/sample.csv'); ?>"><?php echo $this->lang->line('To get the idea, download the sample.csv file'); ?>
                                </a>
                            </p>
                        </div>

                        <div id="pp-upload-container" class="row justify-content-center mt-5 pt-5 d-none">
                            
                            <div id="wizard-selected" class="wizard-steps">
                                <div class="wizard-step text-primary border border-primary">
                                    <div class="wizard-step-icon">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                    <div class="wizard-step-label">
                                        <?php echo $this->lang->line("Upload csv file for Text, Image, and Link posts"); ?>
                                    </div>
                                    <div class="wizard-step-layer" data-post-type="text"></div>
                                </div>

                                <!-- <div class="wizard-step text-info border border-info" data-post-type="img">
                                    <div class="wizard-step-icon">
                                        <i class="fas fa fa-picture-o"></i>
                                    </div>
                                    <div class="wizard-step-label">
                                        <?php // echo $this->lang->line('Upload image posts'); ?>
                                    </div>
                                    <div class="wizard-step-layer" data-post-type="image"></div>
                                </div>
                                <div class="wizard-step text-warning border border-warning" data-post-type="lnk">
                                    <div class="wizard-step-icon">
                                        <i class="fas fa-link"></i>
                                    </div>
                                    <div class="wizard-step-label">
                                        <?php // echo $this->lang->line('Upload link posts'); ?>
                                    </div>
                                    <div class="wizard-step-layer" data-post-type="link"></div>
                                </div> -->

                                <!-- Spinner -->
                                <div class="xit-spinner d-none text-primary">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                            </div><!-- ends #wizard-selected -->

                            <input id="postfile" class="d-none" type="file" name="postfile" accept="text/csv">
                        </div> <!-- ends #pp-upload-container -->

                        <div id="pp-datatable-container">

                            <form id="pp-settings-form" method="post">

                                <div id="pp-actions-button" class="mb-1 d-none">
                                    <p class="d-block"><?php echo $this->lang->line("Click on a button below to set up campaign settings"); ?></p>

                                    <div class="btn-group btn-group-medium" role="group" aria-label="Actions">
                                        <button id="pp-manual-button" data-settings-type="manual" type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line("Click here to set up campaign settings manually"); ?>"><?php echo $this->lang->line("Manual"); ?></button>
                                        <button id="pp-automatic-button" data-settings-type="automatic" type="button" class="btn btn-primary" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line("Click here to make campaign settings automated"); ?>"><?php echo $this->lang->line("Automatic"); ?></button>
                                    </div>
                                    <button id="pp-link-clear-cached-data" class="btn btn-link d-none" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line("We are now displaying cached CSV data that was imported previously"); ?>"><?php echo $this->lang->line("Clear cached CSV data"); ?></button>
                                </div>

                                <div id="pp-datatable-wrapper" class="table-responsive2 d-none">
                                    <table id="pp-csv-data-table" class="table table-bordered">
                                        <thead>
                                            <tr>     
                                                <th>#</th>
                                                <th><?php echo $this->lang->line("Campaign Name"); ?></th>
                                                <th><?php echo $this->lang->line("Campaign Type"); ?></th>
                                                <th><?php echo $this->lang->line("Source"); ?></th>
                                                <th><?php echo $this->lang->line("Actions"); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="csv-data-container"></tbody>
                                    </table>
                                </div>

                                <div id="pp-schedule-settings" class="mt-3 d-none">
                                    <p class="h5"><?php echo $this->lang->line("Schedule settings"); ?></p>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="postStartDate"><?php echo $this->lang->line("Post start-datetime"); ?>*</label>
                                                <input id="postStartDate" class="form-control" type="text" name="postStartDate" placeholder="<?php echo $this->lang->line("Set start Date"); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line("Post between two times"); ?></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><?php echo $this->lang->line("From"); ?></span>
                                                    </div>
                                                    <input type="text" id="postStartTime" class="form-control" placeholder="00:00">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text border-left-0"><?php echo $this->lang->line("To"); ?></span>
                                                    </div>
                                                    <input type="text" id="postEndTime" class="form-control" placeholder="23:59">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line("Post interval") ?>*</label>
                                                <select name="postInterval" class="form-control select2" id="postInterval" required style="width:100%;">
                                                    <option value="1"><?php echo $this->lang->line("1 Minute"); ?></option>
                                                    <option value="2"><?php echo $this->lang->line("2 Minutes"); ?></option>
                                                    <option value="3"><?php echo $this->lang->line("3 Minutes"); ?></option>
                                                    <option value="4"><?php echo $this->lang->line("4 Minutes"); ?></option>
                                                    <option value="5"><?php echo $this->lang->line("5 Minutes"); ?></option>
                                                    <option value="6"><?php echo $this->lang->line("6 Minutes"); ?></option>
                                                    <option value="7"><?php echo $this->lang->line("7 Minutes"); ?></option>
                                                    <option value="8"><?php echo $this->lang->line("8 Minutes"); ?></option>
                                                    <option value="9"><?php echo $this->lang->line("9 Minutes"); ?></option>
                                                    <option value="10" selected="selected"><?php echo $this->lang->line("10 Minutes"); ?></option>
                                                    <option value="15"><?php echo $this->lang->line("15 Minutes"); ?></option>
                                                    <option value="20"><?php echo $this->lang->line("20 Minutes"); ?></option>
                                                    <option value="25"><?php echo $this->lang->line("25 Minutes"); ?></option>
                                                    <option value="30"><?php echo $this->lang->line("30 Minutes"); ?></option>
                                                    <option value="35"><?php echo $this->lang->line("35 Minutes"); ?></option>
                                                    <option value="40"><?php echo $this->lang->line("40 Minutes"); ?></option>
                                                    <option value="45"><?php echo $this->lang->line("45 Minutes"); ?></option>
                                                    <option value="50"><?php echo $this->lang->line("50 Minutes"); ?></option>
                                                    <option value="55"><?php echo $this->lang->line("55 Minutes"); ?></option>
                                                    <option value="60"><?php echo $this->lang->line("1 hour"); ?></option>
                                                    <option value="90"><?php echo $this->lang->line("1 hour and half"); ?></option>
                                                    <option value="120"><?php echo $this->lang->line("2 hours"); ?></option>
                                                    <option value="150"><?php echo $this->lang->line("2 hours and half"); ?></option>
                                                    <option value="180"><?php echo $this->lang->line("3 hours"); ?></option>
                                                    <option value="210"><?php echo $this->lang->line("3 hours and half"); ?></option>
                                                    <option value="240"><?php echo $this->lang->line("4 hours"); ?></option>
                                                    <option value="270"><?php echo $this->lang->line("4 hours and half"); ?></option>
                                                    <option value="300"><?php echo $this->lang->line("5 hours"); ?></option>
                                                    <option value="1440"><?php echo $this->lang->line("1 day"); ?></option>
                                                    <option value="2880"><?php echo $this->lang->line("2 days"); ?></option>
                                                    <option value="4320"><?php echo $this->lang->line("3 days"); ?></option>
                                                    <option value="7200"><?php echo $this->lang->line("5 days"); ?></option>
                                                    <option value="8640"><?php echo $this->lang->line("6 days"); ?></option>
                                                    <option value="10080"><?php echo $this->lang->line("7 days"); ?></option>
                                                    <option value="43200"><?php echo $this->lang->line("1 month"); ?></option>
                                                    <option value="86400"><?php echo $this->lang->line("2 months"); ?></option>
                                                    <option value="259200"><?php echo $this->lang->line("6 months"); ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line("Do not post on day(s)"); ?></label>
                                                <select name="postDayOff" class="form-control select2" id="postDayOff" multiple style="width:100%;">
                                                    <option value="Saturday"><?php echo $this->lang->line("Saturday"); ?></option>
                                                    <option value="Sunday"><?php echo $this->lang->line("Sunday"); ?></option>
                                                    <option value="Monday"><?php echo $this->lang->line("Monday"); ?></option>
                                                    <option value="Tuesday"><?php echo $this->lang->line("Tuesday"); ?></option>
                                                    <option value="Wednesday"><?php echo $this->lang->line("Wednesday"); ?></option>
                                                    <option value="Thursday"><?php echo $this->lang->line("Thursday"); ?></option>
                                                    <option value="Friday"><?php echo $this->lang->line("Friday"); ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>
                                                    <?php echo $this->lang->line('Repost') ?>
                                                    <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Please provide the number below that how many times you want to repost each post again.'); ?>" data-original-title="Repost"><i class="fa fa-info-circle"></i> </a>
                                                </label>
                                                <input id="recyclePost" class="form-control" type="number" name="recyclePost" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="pp-social-settings" class="col-12 mt-5 text-center d-none">
                                    <button id="social-settings-button" class="btn btn-primary btn-lg">
                                        <?php echo $this->lang->line("Next"); ?>    
                                    </button>
                                </div>

                                <!-- Keeps information of settings type -->
                                <input id="settings-type" type="hidden" name="settingsType">

                            </form><!-- ends form -->

                            <!-- Spinner -->
                            <div class="xit-spinner text-primary">
                                <i class="fa fa-spinner fa-spin fa-3x"></i>
                            </div>

                        </div><!-- ends #pp-datatable-container -->
                    </div><!-- ends .card-body -->    
                </div><!-- ends .card -->
            </div><!-- ends .col-12 -->
        </div><!-- ends .row -->
    </div><!-- ends .section-body -->
</section><!-- ends .section -->

<div class="modal fade" id="settings_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cog"></i> <?php echo $this->lang->line("Campaign Settings") ?> <span id="put_feed_name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body" id="feed_setting_container"></div>

            <div class="modal-footer" style="padding-left: 30px;padding-right: 30px;">
                <button type="button" class="btn-lg btn btn-default" data-dismiss="modal" id="close_settings"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close");?></button>
                <button type="button" class="btn-lg btn btn-primary" id="save_settings" style="margin-left: 0;"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Campaigns");?></button>
            </div>

            <!-- Spinner -->
            <div class="xit-spinner text-primary">
                <i class="fa fa-spinner fa-spin fa-3x"></i>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {

        var base_url = '<?php echo base_url(); ?>',
            extraHours = (Date.now() + 60000 * 60 * 1),
            minDateTime = new Date(extraHours);

        /* Handles datatime picker */ 
        $(document).on('click blur keydown mousedown', '.manual-schedule', function(e) {
            $('.datepicker_x').datetimepicker({
                theme:'light',
                format:'Y-m-d H:i:s',
                formatDate:'Y-m-d H:i:s',
                timepicker: true,                
                minDate: minDateTime,
                minTime: minDateTime,
            });
        });

        /* Handles starting date */
        $('#postStartDate').datetimepicker({
            theme:'light',
            format:'Y-m-d',
            formatDate:'Y-m-d',
            timepicker: false,
            minDate: minDateTime,
        });

        $('#postStartTime, #postEndTime').datetimepicker({
            format:'H:i',
            datepicker:false,
        });        

        /* Draws datatable from cached data */
        var cachedTableData = localStorage.getItem('xit-pp-prepared-csvdata');
        if (cachedTableData) {

            var jsonCachedTableData;
            
            try {

                /* Prepares JSON data */
                jsonCachedTableData = JSON.parse(cachedTableData);

                /* Displays button link to clear cache data */
                $('#pp-link-clear-cached-data').removeClass('d-none');

                /* Shows spinner */
                $('#pp-datatable-container .xit-spinner').show();
                
                /* Generates table rows from cached data */ 
                var cachedTableRows = generateTableRowWithTableData(jsonCachedTableData);

                /* Draws datatable */
                drawDatatableWithTableData(cachedTableRows);

            } catch (e) {
                throw e; 
            }

        } else {
            $('#pp-csv-info').removeClass('d-none');
            $('#pp-upload-container').removeClass('d-none');
        }

        /* Clears cahced table data */
        $(document).on('click', '#pp-link-clear-cached-data', function(e) {
            var cachedTableData = localStorage.getItem('xit-pp-prepared-csvdata');

            if (! cachedTableData) {
                return;
            }

            swal({
                title: '<?php echo $this->lang->line("Warning!") ?>',
                text: '<?php echo $this->lang->line("Are you sure? If you do so, that can not be undone!"); ?>',
                icon: 'warning',
                buttons: ['<?php echo $this->lang->line("Cancel"); ?>', '<?php echo $this->lang->line("OK"); ?>']
            }).then(willDelete => {
                if (willDelete) {
                    localStorage.removeItem('xit-pp-prepared-csvdata');
                    window.location.reload();
                }
            });
        });

        /* Truncates strings */
        function truncateString(str, charsLength, endDelimiter = '...') {
            if (str.length < charsLength) {
                return str;
            }

            return str.trim().slice(0, charsLength) + endDelimiter;
        }

        /* Renders table data */
        function generateTableRowWithTableData(data) {
            if (! Array.isArray(data) && 6 !== data[0].length) {
                return;
            }

            var output = '', i = 1, j = 0;

            for (let item of data) {
                let row_number = i++;
                let campaign_Id = j++;

                var truncatedName = item[0] ? truncateString(item[0], 35) : '';
                var link = item[3] ? '<a id="pp-source-link" class="btn btn-link" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line("Source link"); ?>" href="' + item[3] + '" target="_blank"><i class="fa fa-link"></i></a>' : '';

                output += '<tr>';
                output += '<td>' + row_number + '</td>';
                output += '<td>' + truncatedName + '</td>';
                output += '<td>' + item[1] + '</td>';
                output += '<td>' + link + '</td>';
                output += '<td id="pp-datatable-action">\
                        <span id="action-automatic"><?php echo $this->lang->line("Automatic"); ?></span>\
                        <input class="form-control manual-schedule datepicker_x d-none" type="text" name="manualSchedule[]" placeholder="Date and Time" required>\
                        <input id="campaign-ids" type="hidden" name="manualSettingsData[]" value="' + campaign_Id + '">\
                    </td>';
                output += '</tr>';
            }

            return output;
        }

        /* Prepares select options */
        function createSelectOptionsFromArray(data, type, selected = null) {
            if (! Array.isArray(data)) {
                return;
            }

            if (null !== selected && ! Array.isArray(selected)) {
                return;
            }

            const media = ['facebook', 'twitter', 'linkedin', 'reddit'];

            if (media.indexOf(type) < 0) {
                return;
            }

            var output = '';

            for (let median of data) {

                switch (type) {
                    case 'facebook':
                        const facebookValue = 'facebook_rx_fb_page_info-' + median.id;
                        output += '<option value="' + facebookValue + '">' + median.page_name + '</option>';
                        break;
                    case 'twitter':
                    case 'linkedin':
                        const twitterLinkedinValue = type + '_users_info-' + median.id;
                        output += '<option value="' + twitterLinkedinValue + '">' + median.name + '</option>';
                        break;
                    case 'reddit':
                        const redditValue = type + '_users_info-' + median.id;
                        output += '<option value="' + redditValue + '">' + median.username + '</option>';
                        break;
                }
            }

            return output;
        }

        /* Prepares select options */
        function createSelectOptionsFromObject(data, selected = null) {
            if ( typeof data !== 'object') {
                return;
            }

            if (! Object.keys(data).length > 0) {
                return;
            }

            if (null !== selected && ! Array.isArray(selected)) {
                return;
            }

            var output = '';

            for (const [key, value] of Object.entries(data)) {
                let isSelected = (selected && selected.indexOf(key) > -1) ? 'selected' : '';
                output += '<option value="'+ key +'" ' + isSelected + '>' + value + '</option>';
            }

            return output;
        }

        /* Renders select field */
        /* data = { data: data, type: type, data.multiple, data.selected } */
        function createSelectBox(data) {
            let selectBox = '';

            switch(data.type) {
                case 'facebook':
                case 'twitter':
                case 'linkedin':
                case 'reddit':
                    const optionsFromArray = createSelectOptionsFromArray(data.data, data.type, data.selected);
                    const isMultipleSocial = data.multiple ? 'multiple' : '';
                    selectBox = '<select id="' + data.type + 'SelectBox" class="form-control select2" name="' + data.type + 'SelectBox" ' + isMultipleSocial + ' style="width: 100%">\
                        <option value=""></option>' + optionsFromArray + '</select>\
                        <script>$("#' + data.type + 'SelectBox").select2();<\/script>';
                    return selectBox;
                case 'subreddit':
                case 'timezone':
                    const optionsFromObject = createSelectOptionsFromObject(data.data, data.selected);
                    const isMultipleOther = data.multiple ? 'multiple' : '';
                    selectBox = '<select id="' + data.type + 'SelectBox" class="form-control select2" name="' + data.type + 'SelectBox" ' + isMultipleOther + ' style="width: 100%">\
                        <option value=""></option>' + optionsFromObject + '</select>\
                        <script>$("#' + data.type + 'SelectBox").select2();<\/script>';
                    return selectBox;
            }
        }

        /* Fetches social configurations */
        function getSocialConfig() {
            var link =  base_url + 'post_planner/campaign_settings';

            $.get(link).always(function() {
                $('.xit-spinner').show();
            }).done(function(response) {
                
                let html = '<form id="pp-social-settings-form">';
                    html += '<div class="row">';

                if (response.timezones) {
                    const args = {
                        data: response.timezones,
                        type: 'timezone',
                        multiple: false,
                        selected: [response.defaultTimeZone],
                    };
                    const selectBox = createSelectBox(args);

                    html += '<div class="col-md-6">\
                        <div class="form-group">\
                            <label><?php echo $this->lang->line("Posting Timezone"); ?></label>\
                            ' + selectBox + '\
                        </div>\
                    </div>';
                }

                if (response.facebook_accounts) {
                    const args = {
                        data: response.facebook_accounts,
                        type: 'facebook',
                        multiple: true,
                        selected: null,
                    };
                    const selectBox = createSelectBox(args);
                    html += '<div class="col-md-6">\
                        <div class="form-group">\
                            <label><?php echo $this->lang->line("Post to facebook pages"); ?></label>\
                            ' + selectBox + '\
                        </div>\
                    </div>';
                }

                if (response.twitter_accounts) {
                    const args = {
                        data: response.twitter_accounts,
                        type: 'twitter',
                        multiple: true,
                        selected: null,
                    };
                    const selectBox = createSelectBox(args);

                    html += '<div class="col-md-6">\
                        <div class="form-group">\
                            <label><?php echo $this->lang->line("Post to twitter accounts"); ?></label>\
                            ' + selectBox + '\
                        </div>\
                    </div>';
                }

                if (response.linkedin_accounts) {
                    const args = {
                        data: response.linkedin_accounts,
                        type: 'linkedin',
                        multiple: true,
                        selected: null,
                    };
                    const selectBox = createSelectBox(args);
                    
                    html += '<div class="col-md-6">\
                        <div class="form-group">\
                            <label><?php echo $this->lang->line("Post to linkedin accounts"); ?></label>\
                            ' + selectBox + '\
                        </div>\
                    </div>';
                }

                if (response.reddit_accounts) {
                    const args = {
                        data: response.reddit_accounts,
                        type: 'reddit',
                        multiple: true,
                        selected: null,
                    };
                    const selectBox = createSelectBox(args);

                    html += '<div class="col-md-6">\
                        <div class="form-group">\
                            <label><?php echo $this->lang->line("Post to reddit accounts"); ?></label>\
                            ' + selectBox + '\
                        </div>\
                    </div>';
                }

                if (response.subreddits) {
                    const args = {
                        data: response.subreddits,
                        type: 'subreddit',
                        multiple: false,
                        selected: null,
                    };
                    const selectBox = createSelectBox(args);

                    html += '<div class="col-md-6">\
                        <div class="form-group">\
                            <label><?php echo $this->lang->line("Post to subreddit accounts"); ?></label>\
                            ' + selectBox + '\
                        </div>\
                    </div>';
                }

                html += '</div><!-- ends .row -->';
                html += '</form><!-- ends form -->';

                $("#feed_setting_container").html(html).promise().done(function() {
                    $('.xit-spinner').hide();
                    $("#settings_modal").modal();
                });
            }).fail(function(xhr, status, error){
                console.log('status: ', status);
                console.log('error: ', error);
            });
        }

        /* Draws datatable with table data */
        function drawDatatableWithTableData(tableRows) {
            $('#csv-data-container').html('');
            $('#csv-data-container').html(tableRows).promise().done(function() {
                var perscroll;
                var table = $("#pp-csv-data-table").DataTable({
                    serverSide: false,
                    processing: true,
                    order: [[ 1, "desc" ]],
                    pageLength: 10, 
                    language: {
                        url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
                    },
                    dom: '<"d-flex justify-content-end align-items-center"f>rt<"d-flex justify-content-between align-items-center"lip><"clear">',
                    columnDefs: [{
                            targets: [],
                            visible: false
                        }, {
                            targets: [0,2,3,4],
                            className: 'text-center'
                        }, {
                            targets: [3,4],
                            sortable: false
                        }
                    ],
                    /* when initialization is completed then apply scroll plugin */
                    fnInitComplete: function() {  
                        if(areWeUsingScroll) {
                            if (perscroll) {
                                perscroll.destroy();
                                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                            }
                        }
                    },
                    scrollX: 'auto',
                    /* on paginition page 2,3.. often scroll shown, so reset it and assign it again */
                    fnDrawCallback: function( oSettings ) { 
                        if(areWeUsingScroll) { 
                            if (perscroll) { 
                                perscroll.destroy();
                                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                            }
                        }

                        // Hides spinner
                        $('#pp-datatable-container .xit-spinner').hide();

                        // Opens up required elements
                        $('#pp-actions-button').removeClass('d-none');
                        $('#pp-datatable-wrapper').removeClass('d-none');

                        var settingsType = $('#settings-type').val();

                        if ('manual' === settingsType) {
                            $("#pp-csv-data-table").find('tr > td > #action-automatic').addClass('d-none');
                            $("#pp-csv-data-table").find('tr > td > .manual-schedule').removeClass('d-none');
                        } else if ('automatic') {
                            $("#pp-csv-data-table").find('tr > td > .manual-schedule').addClass('d-none');
                            $("#pp-csv-data-table").find('tr > td > #action-automatic').removeClass('d-none');
                        }

                    }
                });

                window.ppDataTable = table;

                // Falls back to drawing again if there is any problem for the table display
                table.draw();   
            });
        }

        /* Handles elements visibility for manual and automatic buttons */ 
        $(document).on('click', '#pp-manual-button, #pp-automatic-button', function(e) {
            var settingsType = $(this).data('settings-type'),
                actionButton = $('#pp-datatable-action');

            if ('manual' === settingsType) {
                $('#pp-automatic-button').removeClass('active');
                $(this).addClass('active');

                $('#settings-type').val(settingsType);
                $("#pp-csv-data-table").find('tr > td > #action-automatic').addClass('d-none');
                $("#pp-csv-data-table").find('tr > td > .manual-schedule').removeClass('d-none');
                $('#pp-schedule-settings').addClass('d-none');
                $('#pp-datatable-wrapper').removeClass('d-none');
                $('#pp-social-settings').removeClass('d-none');

            } else if ('automatic' === settingsType) {
                $('#pp-manual-button').removeClass('active');
                $(this).addClass('active');

                $('#settings-type').val(settingsType);
                $('#pp-datatable-wrapper').addClass('d-none');
                $('#pp-schedule-settings').removeClass('d-none');
                $("#pp-csv-data-table").find('tr > td > .manual-schedule').addClass('d-none');
                $("#pp-csv-data-table").find('tr > td > #action-automatic').removeClass('d-none');
                $('#pp-social-settings').removeClass('d-none');
            }
        });

        /* Handles social configuration options */
        $(document).on('click', '#social-settings-button', function(e) {
            var settingsType = $('#settings-type').val()
                settingsForm = document.getElementById('pp-settings-form');

            if (! settingsType) {
                swal({
                    title: '<?php echo $this->lang->line("Warning!") ?>',
                    text: '<?php echo $this->lang->line("Click on Manual or Automatic button to start configuring campaign settings"); ?>',
                    icon: 'warning',
                });

                return;
            }

            var table = window.ppDataTable;
            var settingsFormData = $(settingsForm).serializeArray();
            var allInputData = table.$('input').serializeArray();

            window.ppSettingsType = settingsType;

            if ('manual' === settingsType) {
                var filteredFormData = allInputData.filter(item => ('manualSchedule[]' === item.name)),
                    isFilledInEachField = item => ("" !== item.value);

                if (! filteredFormData.every(isFilledInEachField)) {
                    swal({
                        title: '<?php echo $this->lang->line("Warning!") ?>',
                        text: '<?php echo $this->lang->line("Please fill in all the datetime fields"); ?>',
                        icon: 'warning',
                    });

                    return;
                }

                // Sets the settings type
                $('#settings-type').val(settingsType);

                /* Prepares data */
                window.ppManauSchduleData = allInputData.map(item => item.name === 'manualSchedule[]' ? item.value : '').filter(Boolean);
                window.ppCampaignIds = allInputData.map(item => item.name === 'manualSettingsData[]' ? item.value : '').filter(Boolean);
                
            } else if ('automatic' === settingsType) {
                var postStartDate       = $('#postStartDate').val(),
                    postStartTime       = $('#postStartTime').val(),
                    postEndTime         = $('#postEndTime').val(),
                    postInterval        = $('#postInterval').val(),
                    postDayOff          = $('#postDayOff').val();
                    recyclePost         = $('#recyclePost').val();

                if (! postStartDate || ! postInterval) {
                    swal({
                        title: '<?php echo $this->lang->line("Warning!") ?>',
                        text: '<?php echo $this->lang->line("Please fill in required fields"); ?>',
                        icon: 'warning',
                    });

                    return;
                }

                // Sets the settings type
                $('#settings-type').val(settingsType);

                window.ppPostStartDate  = postStartDate;
                window.ppPostStartTime  = postStartTime;
                window.ppPostEndTime    = postEndTime;
                window.ppPostInterval   = postInterval;
                window.ppPostDayOff     = postDayOff;
                window.recyclePost      = recyclePost;

                var date = new Date(postStartDate),
                    day = date.toLocaleString('en-us', { weekday:'long' });

                if (! postStartDate) {
                    swal({
                        title: '<?php echo $this->lang->line("Warning!") ?>',
                        text: '<?php echo $this->lang->line("Post start date can not be empty"); ?>',
                        icon: 'warning',
                    });

                    return;
                }

                if (parseInt(postInterval, 10) < 0 || parseInt(postInterval, 10) > 259200) {
                    swal({
                        title: '<?php echo $this->lang->line("Warning!") ?>',
                        text: '<?php echo $this->lang->line("Post interval must be greater than 0 and less than or equal to 259200 mins"); ?>',
                        icon: 'warning',
                    });

                    return;
                }

                if (Array.isArray(postDayOff) && (postDayOff.indexOf(day) !== -1)) {
                    swal({
                        title: '<?php echo $this->lang->line("Warning!") ?>',
                        text: '<?php echo $this->lang->line("The start-date is similar to the day(s) that are off"); ?>',
                        icon: 'warning',
                    });

                    return;
                }
            }

            // Opens up modal with social configuration
            getSocialConfig();
        });

        /* Handles CSV file upload and prepares data */
        $(document).on('click', '.wizard-step-layer', function(e) {
            var post_type = $(this).data('post-type');

            // Triggers the input file
            $('#postfile').trigger('click');

            $(document).on('change', '#postfile', function(e) {

                if (e.target.files && e.target.files[0]) {

                    // Hides upload container
                    $('#pp-csv-info').hide();
                    $('#pp-upload-container').hide();

                    // Shows spinner
                    $('#pp-datatable-container .xit-spinner').show();
                    
                    // Prepares form data
                    var formData = new FormData();
                    formData.append('post_type', post_type);
                    formData.append('post_file', e.target.files[0]);

                    $.ajax({
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        url: '<?php echo base_url("post_planner/manage_csv_data"); ?>',
                        success: function(res) {
                            if (true === res.status) {
                                var tableRows = generateTableRowWithTableData(res.data);

                                /* Puts table rows into localStorage */
                                if (! localStorage.getItem('xit-pp-prepared-csvdata')) {
                                    localStorage.setItem('xit-pp-prepared-csvdata', JSON.stringify(res.data));
                                }

                                /* Draws datatable */
                                drawDatatableWithTableData(tableRows);

                            } else if (false === res.status) {
                                swal({
                                    title: '<?php echo $this->lang->line("Warning!") ?>',
                                    text: res.message,
                                    icon: 'warning',
                                });

                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000);
                            } else {
                                window.location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                        }
                    });
                }
            });
        });

        $(document).on('click','.campaign_settings',function(e){ 
            e.preventDefault();

            var id = $(this).attr('data-id');
            
            $.ajax({
                type:'POST',
                url: base_url + 'post_planner/campaign_settings',
                data: {id:id},
                dataType: 'JSON',
                success:function(response) {  
                    if(response.status == '0') {
                        $("#settings_modal .modal-footer").hide();
                    } else {
                        $("#settings_modal .modal-footer").show();
                    }

                    $("#feed_setting_container").html(response.html);                 
                    $("#settings_modal").modal();
                }
            });
        });

        /* Tries saving campaign saving data */ 
        $(document).on('click', '#save_settings', function(e) {
            // $(this).attr('disabled', true);
            // $(this).addClass('disabled');

            var postTimeZone        = $('#timezoneSelectBox').val(),
                facebookSelectBox   = $('#facebookSelectBox').val(),
                twitterSelectBox    = $('#twitterSelectBox').val(),
                linkedinSelectBox   = $('#linkedinSelectBox').val(),
                redditSelectBox     = $('#redditSelectBox').val(),
                subredditSelectBox  = $('#subredditSelectBox').val(),

                formId = document.getElementById('pp-social-settings-form'),
                formData = new FormData();

                formData.append('settingsType', window.ppSettingsType);

                if ('manual' === window.ppSettingsType) {
                    formData.append('campaignIds', window.ppCampaignIds);
                    formData.append('manauSchduleData', window.ppManauSchduleData);
                } else {
                    formData.append('postStartDate', window.ppPostStartDate);
                    formData.append('postInterval', window.ppPostInterval);
                    formData.append('postStartTime', window.ppPostStartTime);
                    formData.append('postEndTime', window.ppPostEndTime);
                    formData.append('postDayOff', window.ppPostDayOff);
                    formData.append('recyclePost', window.recyclePost);
                }

                formData.append('postTimeZone', postTimeZone);
                formData.append('facebookSelectBox', facebookSelectBox);
                formData.append('twitterSelectBox', twitterSelectBox);
                formData.append('linkedinSelectBox', linkedinSelectBox);
                formData.append('redditSelectBox', redditSelectBox);
                formData.append('subredditSelectBox', subredditSelectBox);
                formData.append('csvData', localStorage.getItem('xit-pp-prepared-csvdata'));

            $.ajax({
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                url: base_url + 'post_planner/manage_submitted_data',
                success: function(response) {
                    if (true === response.status) {

                        /* Deletes table rows from localStorage */
                        if (localStorage.getItem('xit-pp-prepared-csvdata')) {
                            localStorage.removeItem('xit-pp-prepared-csvdata');
                        }

                        var textCampaigns = (false !== response.data.text) 
                            ? response.data.text
                            : 0;

                        var linkCampaigns = (false !== response.data.link) 
                            ? response.data.link
                            : 0;

                        var imageCampaigns = (false !== response.data.image) 
                            ? response.data.image
                            : 0;

                        var message = '<?php echo $this->lang->line("We have created");?> <a href="' + base_url + 'comboposter/text_post/campaigns">' + textCampaigns + ' <?php echo $this->lang->line("text campaign(s)"); ?></a>, <a href="' + base_url + 'comboposter/image_post/campaigns">' + imageCampaigns + ' <?php echo $this->lang->line("image campaign(s)"); ?></a>, <a href="' + base_url + 'comboposter/link_post/campaigns">' + linkCampaigns + ' <?php echo $this->lang->line("link campaign(s)"); ?></a> <?php echo $this->lang->line("from the CSV upload.");?>';

                        var para = document.createElement("P");
                        para.innerHTML = message;

                        swal({
                            title: '<?php echo $this->lang->line("Success!") ?>',
                            content: para,
                            icon: 'success',
                            buttons: ['<?php echo $this->lang->line('Cancel'); ?>', '<?php echo $this->lang->line('OK'); ?>'],
                            closeOnClickOutside: false,
                        }).then(function(reload) {
                            if (reload) {
                                window.location.reload();
                            }
                        });

                    } else if (false === response.status) {
                        swal({
                            title: '<?php echo $this->lang->line("Warning!") ?>',
                            text: response.message,
                            icon: 'warning',
                        });                        
                    }
                },
                error: function(xhr, status, error) {
                    alert(status);
                    alert(error);
                }
            });
        });
    });

</script>