  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <!-- <small>Control panel</small> -->
      </h1>
     <!--  <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>

            <div class="inner">
              <h3><?php  echo $allposts; ?></h3>
              <p>Posts</p>
            </div>
<!--             <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
 -->          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <div class="inner">
              <h3><?php  echo $spamposts; ?><sup style="font-size: 20px"></sup></h3>
              <p>Spam Posts</p>
            </div>
<!--             <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
 -->          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
          <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <div class="inner">
              <h3><?php  echo $allusers; ?></h3>
              <p>User Registrations</p>
            </div>
            <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
        <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <div class="inner">
              <h3><?php  echo $blockusers; ?></h3>
              <p>Spam Users</p>
            </div>
<!--             <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
 -->          </div>
        </div>
        <!-- ./col -->
      </div>
       </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <!-- <b>Version</b> 2.4.0 -->
    </div>
    <strong>Copyright &copy; 2018 <a href="https://adminlte.io">MSToo</a>.</strong> All rights
    reserved.
  </footer>
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
