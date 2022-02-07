<style type="text/css">.no_hover:hover{text-decoration: none;}</style>
<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-bars"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon text-primary">
                        <i class="fas fa-pager"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo $this->lang->line("Page Manager"); ?></h4>
                        <p><?php echo $this->lang->line("Create, edit, delete custom pages"); ?></p>
                        <a href="<?php echo base_url("menu_manager/get_page_lists"); ?>" class="card-cta"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon text-primary">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo $this->lang->line("Link Manager"); ?></h4>
                        <p><?php echo $this->lang->line("Create menu links, manage menu links"); ?></p>
                        <a href="<?php echo base_url("menu_manager/get_menu_lists"); ?>" class="card-cta"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

