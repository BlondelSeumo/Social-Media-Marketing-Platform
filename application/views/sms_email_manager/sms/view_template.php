<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-th-list"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("sms_email_sequence/template_lists/").$templateType; ?>"><?php echo ucfirst($templateType).' '. $this->lang->line("Template"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <h2 class="section-title"><?php echo $this->lang->line("Template Name"); ?></h2>
                <div class="section-lead"><?php echo $template_data[0]['template_name']; ?></div>
                <h2 class="section-title"><?php echo $this->lang->line("Template Content"); ?></h2>
                <div class="alert alert-light section-lead mt-2"><?php echo $template_data[0]['content']; ?></div>
            </div>
        </div>
    </div>
</section>