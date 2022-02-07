<style type="text/css">
  .button-outline
  {
    background: #fff;
    border: .5px dashed #ccc;
  }
  .button-outline:hover
  {
    border: 1px dashed var(--blue) !important;
    cursor: pointer;
  }
  .multi_layout{margin:0;background: #fff}
  .multi_layout .card{margin-bottom:0;border-radius: 0;}
  .multi_layout p, .multi_layout ul:not(.list-unstyled), .multi_layout ol{line-height: 15px;}
  .multi_layout .list-group li{padding: 15px 10px 12px 25px;}
  .multi_layout{border:.5px solid #dee2e6;}
  .multi_layout .collef,.multi_layout .colmid,.multi_layout .colrig{padding-left: 0px; padding-right: 0px;}
  .multi_layout .collef,.multi_layout .colmid{border-right: .5px solid #dee2e6;}
  .multi_layout .main_card{min-height: 500px;box-shadow: none;}
  .multi_layout .collef .makeScroll{max-height: 790px;overflow:auto;}
  .multi_layout .list-group{padding-top:6px;}
  .multi_layout .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;cursor: pointer;z-index: 0;}
  .multi_layout .list-group .list-group-item:first-child{border-top:none;}
  .multi_layout .list-group .list-group-item:last-child{border-bottom:none;}
  .multi_layout .list-group .list-group-item.active{border:.5px solid var(--blue);}
  .multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
  .multi_layout .card-statistic-1{border-radius: 0;}
  .multi_layout h6.page_name{font-size: 14px;}
  .multi_layout .card .card-header input{max-width: 100% !important;}
  .multi_layout .waiting,.modal_waiting {height: 100%;width:100%;display: table;}
  .multi_layout .waiting i,.modal_waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
  .multi_layout .card .card-header h4 a{font-weight: 700 !important;}  
  .product-item .product-name{font-weight: 500;}
  .badge-status{border-color:#eee;}
  /* #right_column_title i{font-size: 17px;} */
  
  ::placeholder {
    color: #ccc !important;
  }
  .smallspace{padding: 10px 0;}
  .lead_first_name,.lead_last_name,.lead_tag_name{background: #fff !important;}
  .getstarted_lead_first_name,.getstarted_lead_last_name,.getstarted_lead_tag_name{background: #fff !important;}
  .ajax-file-upload-statusbar{width: 100% !important;}
  hr{margin-top: 10px;}
 .custom-top-margin{margin-top: 20px;}
 .sync_page_style{margin-top: 8px;}
  /* .wrapper,.content-wrapper{background: #fafafa !important;} */
  .well{background: #fff;}  
  .emojionearea, .emojionearea.form-control{height: 140px !important;}
  .emojionearea.small-height{height: 140px !important;}

  /*import bot modal section*/
  .radio_check{display:block;position:relative;padding-left:35px;cursor:pointer;font-size:22px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}
  .radio_check input{position:absolute;opacity:0;cursor:pointer}
  .checkmark{position:absolute;top:0px;right:0;height:18px;width:18px;background-color:#ccc;}
  .radio_check:hover input~.checkmark{background-color:#eee}
  .radio_check input:checked~.checkmark{background-color:#2196F3}.checkmark:after{content:"";position:absolute;display:none}
  .radio_check input:checked~.checkmark:after{display:block}
  .radio_check .checkmark:after{top:5px;left:5px;width:8px;height:8px;border-radius:50%;background:#fff}
  .template_sec{border:1px solid #dcd7d7;border-top-right-radius:6px;border-bottom-right-radius:6px;padding-right:0;overflow: hidden;}
  .template_img_section img{border-top-left-radius:6px;border-bottom-left-radius:6px}
  .template_body_section{height:94px;padding:3px 10px 0 10px;border-left:none}
  .description_section{font-size:10px;text-align:justify}
  .author-box .author-box-name { font-size: 14px;}
  .author-box .author-box-picture { width:80px;}

  .type3 .ajax-upload-dragdrop{text-align: center;}
  .type3 .ajax-file-upload-filename{width:100% !important;}

  .select2-container--default .select2-selection--multiple .select2-selection__choice {
      background: #fff;
      color: var(--blue);
      height: 30px;
      line-height: 27px;
      border: 1px solid var(--blue) !important;
  }
  .single_question_block
  {
    border: .5px dashed #ccc;
    padding: 10px;
    margin-bottom: 15px;
  }
  .add_more_question_block
  {
    margin-top: 5px;
  }
  .add_template,.ref_template{font-size: 10px;}
  #ice_breaker_info{cursor: pointer;}
  #action_button_settings_block .card .card-body {
    padding: 24px 0 !important;
  }
  #action_button_settings_block .card .card-body h4 {
    font-size: 14px !important;
    margin-bottom: 0 !important;
  }

  #action_button_settings_block .card {
    box-shadow: none !important;
    /*border: solid 1px #ebebeb !important;*/
    border: dashed 1px #e9e9e9 !important;
  }

  #action_button_settings_lists a:hover { text-decoration: none !important;}

  .settings_block .card .card-body {padding: 20px 0 20px 5px !important;}
  .settings_block .card {box-shadow: none !important;border: dashed 1px #e9e9e9 !important;}
  .settings_block .block-button {padding: 2px 5px;font-size: 10px !important;border:.5px dashed #0d8bf1 !important;margin:0 1px !important; }
  .settings_block .block-button i { font-size: 11px !important;}
  .settings_block .block-button:hover { text-decoration: none !important;}
  .settings_block .card.card-large-icons.card-condensed .card-icon { width:60px !important;}
  .settings_block .card.card-large-icons.card-condensed .card-icon i { font-size:35px !important;}
  .settings_block .card.card-large-icons.card-condensed a.btn-primary:not(.dropdown-item),.settings_block .card.card-large-icons.card-condensed a:not(.dropdown-item):hover,.settings_block .card.card-large-icons.card-condensed a:not(.dropdown-item):active,.settings_block .card.card-large-icons.card-condensed a:not(.dropdown-item):focus { color: #ffffff !important; }

  .list-group a.collapse_items {
    color: #6C757D !important;
    font-weight: normal;
  }
  .list-group a.collapse_items.active {
    color: var(--blue) !important;
    background-color: transparent !important;
  }

  .list-group a.collapse_items:hover {
    background: none;
  }

  .collapse_block .card-body .dropdown-toggle::after { content:none !important; }
  .card.collapse_block .card-body { padding: 24px 10px !important; }
  .card.collapse_block .card-body h4 { margin-bottom: 0px !important; }
  .card.collapse_block .card-body .list-group { margin-top: 12px !important; }

</style>