<?php if(!isset($page_title)) $page_title = $this->lang->line("404 | Page not found"); ?>
<?php if(!isset($message)) $message = $this->lang->line("The page you were looking for could not be found."); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
  <link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.png"> 
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/v4-shims.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/components.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
</head>

<body style="background: white">
  <div class="card" style="box-shadow:none">
    <div class="card-body">
      <div class="empty-state" data-height="600" style="height: 600px;">
        <img class="img-fluid" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image" style="max-height: 400px;">
        <h2 class="mt-0"><?php echo $page_title; ?></h2>
        <p class="lead">
         <?php echo $message; ?>
        </p>
        <a class="btn btn-outline-primary mt-4" href="<?php echo base_url(); ?>"><i class="fas fa-arrow-circle-left"></i> <?php echo $this->lang->line("Go back to home"); ?></a>
      </div>
    </div>
  </div>
</body>
</html>
