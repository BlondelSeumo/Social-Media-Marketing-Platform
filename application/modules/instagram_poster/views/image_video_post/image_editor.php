  <link type="text/css" href="<?php echo base_url('plugins/tui-image-editor/tui-color-picker.css');?>" rel="stylesheet"/>
  <link type="text/css" href="<?php echo base_url('plugins/tui-image-editor/tui-image-editor.css');?>" rel="stylesheet" />
  <link type="text/css" href="<?php echo base_url('plugins/tui-image-editor/bootstrap4-modal-fullscreen.css');?>" rel="stylesheet"/>
  <script type="text/javascript" src="<?php echo base_url('plugins/tui-image-editor/fabric-v4.2.0.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('plugins/tui-image-editor/tui-code-snippet.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('plugins/tui-image-editor/tui-color-picker.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('plugins/tui-image-editor/FileSaver.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('plugins/tui-image-editor/tui-image-editor.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('plugins/tui-image-editor/white-theme.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('plugins/tui-image-editor/black-theme.js');?>"></script>

  <script src="<?php echo base_url('assets/js/system/instagram/image_editor.js');?>"></script>

<div class="modal fade modal-fullscreen" id="tuiModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header pt-1 pr-2 bg-dark no_radius">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-image"></i> <?php echo $this->lang->line("Editor");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
        <div id="tui-image-editor-container"></div>
        <input type="hidden" id="image_type">
      </div>
      <div class="modal-footer bg-dark no_radius">
        <button type="button" class="btn btn-warning btn-lg"  id="image_save"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save Image");?></button>
        <button type="button" class="btn btn-light" data-dismiss="modal"> <?php echo $this->lang->line("Close");?></button>
      </div>
    </div>
  </div>
</div>

