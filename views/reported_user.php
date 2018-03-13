  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Reported Users
        <!-- <small>Reported User table</small> -->
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">ALL USERS</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Profile Picture</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter=1;
                  foreach ($ReportedUsers as $key => $value) {  ?>
                  <tr>
                    <?php
                    $data = get_user_details('ms_users',array('id'=>$value->block_users_id));
                      // echo "<pre>"; print_r($data); die;
                    ?>
                    <td><?php echo $counter; ?></td>
                    <td><?php echo $data['name']; ?></td>
                    <td><?php echo $data['email']; ?></td>
                    <td><?php echo $data['phone']; ?></td>
                    <td><?php echo "<img height='60' width='90' src='".$data['profile_pic']."' / >";?></td>
                    <td><button id="SuspendUser" class="btn btn-block btn-primary" data-status ="<?php echo $data['is_suspend']; ?>" value="<?php echo $data['id'];?>">
                      <?php if($data['is_suspend'] == '1'){echo 'Activate';}else{echo"Suspend";}?></button>
                </td>
              </tr>
              <?php $counter++;
            } ?>
          </tbody>
        </table>
      </div><!-- /.box-body -->
    </div><!-- /.box -->
  </div><!-- /.col-xs-12 -->
</div><!-- /.row -->
</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Version</b> 2.4.0
  </div>
  <strong>Copyright &copy; 2018.<a href="https://adminlte.io">Osvin Web Solutions</a>.</strong> All rights
  reserved.
</footer>

<div class="control-sidebar-bg"></div>
</div>
<script src="<?php echo base_url(); ?>public/template/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>public/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
$(function () {
  $('#example1').DataTable()
  $('#example2').DataTable({
    'paging'      : true,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false
  })
})
</script>
<script>
$(document).ready(function() {
  $("#SuspendUser").click(function(event) {
    event.preventDefault();
    var UserID =$(this).val();
    var status = $('#SuspendUser').attr("data-status");
    jQuery.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>" + "Dashboard/suspend_user",
      data: {UserID: UserID, status: status},
      success: function(res) {
        var res=res.trim();
        if (res=="sucess") {
          location.reload();
        }
        else{
          alert("Something went wrong.Please try again later.");
        }
      }
    });
  });
});
</script>
</body>
</html>
