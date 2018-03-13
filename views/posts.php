<?php 

// echo "<pre>"; print_r($allposts); die;
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Posts
      <!-- <small>Preview page</small> -->
    </h1>
   <!--  <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Posts</li>
    </ol> -->
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- <h2 class="page-header">Social Widgets</h2> -->
    <div class="row"><!-- /.row -->
      <?php foreach ($allposts as $key => $value) {
        $user_details=get_user_details('ms_users',array('id'=>$value->user_id));?>
        <div class="col-md-6">
          <!-- Box Comment -->
          <div class="box box-widget">
            <div class="box-header with-border">
              <div class="user-block">
                <img class="img-circle" src="<?php echo $user_details['profile_pic']; ?>" alt="User Image">
                <span class="username"><a href="#"><?php echo $user_details['name']; ?></a></span>
                <span class="description"><?php echo date('d-M-Y g:i a',strtotime($value->date_created)); ?></span>
              </div>
              <!-- /.user-block -->
              <div class="box-tools">
                <!-- <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Mark as read"> -->
                <!-- <i class="fa fa-circle-o"></i></button> -->
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             <div class="flow_images">
              <?php
              // print_r(json_decode($value->images));
             $data=unserialize($value->images); 
             ?>
             <?php  foreach ($data as  $key=> $valueee) {  ?>
             <img class="img-responsive pad" src="<?php echo $valueee; ?>" style="height:100px;width:100px;" alt="Photo">
             <?php } ?></div>
             <h2 class="page-header Heading"><?php echo $value->title ?></h2>

             <h5><?php echo $value->description; ?></h5>
             <div class="location">
              <h5><strong>Location: </strong></h5><p><?php echo $value->loc_name; ?></p>
            </div>
            <div class="FOr_rent">
              <h3><strong>Rent details:</strong></h3>
              <div class="Daily">
               <?php if(!empty($value->daily)){ ?>
               <h4><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value->daily; ?></h4>
               <?php } else { ?>
               <h4>---</h4>
               <?php }  ?>
               <h5>per day</h5>
             </div>

             <div class="Daily">
              <?php if(!empty($value->weekly)){ ?>
              <h4><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value->weekly; ?></h4>
              <?php } else { ?>
              <h4>---</h4>
              <?php }  ?>
              <h5>per week</h5>
            </div>

            <div class="Daily">
              <?php if(!empty($value->monthly)){ ?>
              <h4><i class="fa fa-inr" aria-hidden="true"></i><?php echo $value->monthly; ?></h4>
              <?php } else { ?>
              <h4>---</h4>
              <?php }  ?>
              <h5>per month</h5>
            </div>
             <div class="delete-button">
            <a class="btn btn-block btn-primary"  onclick="return confirm('Are you sure?')" href="<?php echo site_url('Dashboard/deletePost/'.$value->id);?>">Delete</a>
            </div>
            <div class="clear"></div>
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
    <?php } ?>
  </div><!-- /.row -->
</section><!-- /.content -->
</div><!-- /.content-wrapper -->
  <!-- <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2018.<a href="https://adminlte.io">Osvin Web Solutions</a>.</strong> All rights
    reserved.
  </footer> -->
  <div class="control-sidebar-bg"></div>
</div>
</body>
</html>
