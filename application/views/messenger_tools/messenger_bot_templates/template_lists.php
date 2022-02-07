<style>
  .template_box { cursor: pointer; }
  .template_box.active {
    border: solid 2px var(--blue);
  }

  .template_box .article-details { padding: 15px;height: 100px; }
  .template_box .article-header {height: 130px; }
  .template_box .article-header .article-badge .article-badge-item {
      padding: 2px 10px;
      font-weight: 500;
      color: #fff;
      border-radius: 30px;
      font-size: 12px;
  }
</style>

<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-plug"></i> <?php echo $page_title; ?></h1>    
    <div class="section-header-button">
      <a class="btn btn-primary" href="<?php echo base_url('messenger_bot/upload_template');?>"><i class="fas fa-cloud-upload-alt"></i> <?php echo $this->lang->line('Upload Template');?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="row">
          <?php $i=1; foreach ($saved_template_list as $key=>$val) : 
            $id=$val['id'];
            $template_name=isset($val['template_name']) ? $val['template_name'] : '';
            $description=isset($val['description']) ? $val['description'] : '';
            $preview_image=isset($val['preview_image']) ? $val['preview_image'] : ''; 
            $added_date = date("M j, y H:i",strtotime($val['saved_at']));

            $img_src = '';
            $visit_url = base_url('messenger_bot/saved_template_view/'.$id);
            if($preview_image != '' && file_exists('upload/image/'.$val['user_id'].'/'.$preview_image)) {
              $img_src = base_url('upload/image/'.$val['user_id'].'/'.$preview_image);
            } else {
              $img_src = base_url("assets/img/news/img13.jpg");
            }

            if($val['template_access'] == 'private') {
              $template_access = ucfirst($val['template_access']);
              $bg_color = "bg-danger";
            } else {
              $bg_color = "bg-success";
              $template_access = ucfirst($val['template_access']);
            }

          ?>


          <div class="col-3">
            <article class="article article-style-b template_box">
              <div class="article-header">
                <div class="article-image" data-background="<?php echo $img_src; ?>" style="background-image: url(&quot;<?php echo $img_src; ?>&quot;);">
                </div>
                <div class="article-badge">
                  <div class="article-badge-item <?php echo $bg_color;?>"><i class="fas fa-book-reader"></i> <?php echo $template_access; ?></div>
                </div>
              </div>
              <div class="article-details">
                <div class="article-title mb-0">
                  <h6 class="text-primary">
                    <?php 
                      if(strlen($template_name) > 24)
                      {
                        $short_template_name = substr($template_name,0,16);
                        echo $short_template_name."..."; 
                      } else 
                      {
                        echo $template_name;
                      }
                    ?>
                  </h6>
                </div>
                <small>
                  <?php
                    if(strlen($description) > 35)
                    {
                      $short_des = substr($description,0,38);
                      echo $short_des."..."; 
                    } else 
                    {
                      echo $description;
                    }
                  ?>
                </small>
<!--                 <div class="article-cta">
                  <a href="#">See details <i class="fas fa-chevron-right"></i></a>
                </div> -->
              </div>
            </article>
          </div>

        <?php $i++; endforeach; ?>

        </div>
      </div>
    </div>
  </div>
</section>