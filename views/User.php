  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Users
        <!-- <small>User table</small> -->
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
              <div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Profile Picture</th>
                    <th>Name</th>
                    <th>Email</th>
                    <!-- <th>Country Code</th> -->
                    <th>Phone</th>
                    <!-- <th>Date</th> -->
                    <!-- <th>Action</th> -->
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter=1;
                  foreach ($allusers as $key => $value) {  ?>
                  <tr>
                    <td><?php echo $counter; ?></td>
                    <td><?php echo "<img class='round_cls'  width='90' src='".$value->profile_pic."' / >";?></td>
                    <td><?php echo $value->name; ?></td>
                    <td><?php echo $value->email; ?></td>
                    <!-- <td><?php echo $value->country_code; ?></td> -->
                    <td><?php echo $value->phone; ?></td>
                    <!-- <td><?php echo date('d-M-Y g:i a',strtotime($value->date_created));?></td> -->
                    <!-- <td><a  id="EditData" data-target="#modal-default" class="btn btn-block btn-primary"
                      data-userid="<?php echo $value->id;?>"
                      data-name="<?php echo $value->name;?>"
                      data-email="<?php echo $value->email;?>"
                      data-country="<?php echo $value->country_code;?>"
                      data-phone="<?php echo $value->phone;?>"
                      data-date="<?php echo date('d-M-Y g:i a',strtotime($value->date_created));?>"
                      data-profile="<?php echo $value->profile_pic;?>"
                      data-toggle="modal">Details</a></td> -->
                    </tr>
                    <?php $counter++;
                  } ?>
                </tbody>
              </table>
            </div>
            </div><!-- /.box-body -->
          </div><!-- /.box -->
        </div><!-- /.col-xs-12 -->
      </div><!-- /.row -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->

  <!--******************** FETAILS MODAL BOX STARTS HERE ************************************-->
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 class="modal-title">User Details</h3>
        </div>
        <div class="modal-body">
          <!-- <h5 class="text-center">Hello. Some text here.</h5> -->
          <table class="table table-striped" id="tblGrid">
            <thead id="tblHead">
              <tr>
                <th>User ID</th>
                <td id="UserId"></td>
              </tr>
              <tr>
                <th>Name</th>
                <td id="UpdateName"></td>
              </tr>
              <tr>
                <th>Email</th>
                <td id="UpdateEmail"></td>
              </tr>
              <tr>
                <th>Country Code</th>
                <td id="UpdateCode"></td>
              </tr>
              <tr>
                <th>Phone</th>
                <td id="UpdatePhone"></td>
              </tr>
              <tr>
                <th>Date</th>
                <td id="UpdateDate"></td>
              </tr>
              <tr>
                <th>Profile Picture</th>
                <td id=""> <img src="" style="width:100px;height:100px;" id="UpdateProfile" /></td>
              </tr>
            </thead>
          </table>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <!--************************************ FETAILS MODAL BOX ENDS HERE ************************************-->

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <!-- <b>Version</b> 2.4.0 -->
    </div>
    <strong>Copyright &copy; 2018.<a href="https://adminlte.io">MSToo</a>.</strong> All rights
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
$(document).ready(function(){
  $('#modal-default').on('show.bs.modal', function(e) {

   var userid  = $(e.relatedTarget).data('userid');
   var name    = $(e.relatedTarget).data('name');
   var email   = $(e.relatedTarget).data('email');
   var country = $(e.relatedTarget).data('country');
   var phone   = $(e.relatedTarget).data('phone');
   var date   = $(e.relatedTarget).data('date');
   var profile = $(e.relatedTarget).data('profile');

   // $('#UserId').val(userid);   --------------/
   // $('#UpdateName').val(name);             --/
   // $('#UpdateEmail').val(email);           --/-------- Code to display values in HTML Form
   // $('#UpdateCode').val(country);          --/
   // $('#UpdatePhone').val(phone); ------------/
   $('#UpdateProfile').attr('src',profile);
   $('#UserId').html(userid);
   $('#UpdateName').html(name);
   $('#UpdateEmail').html(email);
   $('#UpdateCode').html(country);
   $('#UpdateDate').html(date);
   $('#UpdatePhone').html(phone);
 });

});
</script>


</body>
</html>










































































































































































































































































<!-- *********************** EDIT MODAL END  ***************************** -->
  <!-- <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit User Info </h4>
          </div>
          <div class="modal-body">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Quick Edit</h3>
              </div>
              <form role="form" name="editUser" method="POST" action="EditUser" enctype="multipart/form-data">
                <div class="box-body">
                  <div class="form-group">
                    <label for="UpdateName">Name</label>
                    <input type="text" class="form-control" name="UpdateName" id="UpdateName" />
                  </div>
                  <div class="form-group">
                    <label for="UpdateEmail">Email</label>
                    <input type="email" class="form-control" name="UpdateEmail" id="UpdateEmail" />
                  </div>
                  <div class="form-group">
                    <label for="UpdateCode">Country Code</label>
                    <input type="text" class="form-control"  name="UpdateCode" id="UpdateCode" />
                  </div>
                  <div class="form-group">
                    <label for="UpdatePhone">Phone address</label>
                    <input type="text" class="form-control"  name="UpdatePhone" id="UpdatePhone" />
                  </div>
                  <div class="form-group">
                    <label for="exampleInputFile">Profile Pic</label>
                    <img src="" style="width:90px;height:auto;border-radius:50%;margin-left:10px;" id="UpdateProfile" />
                    <input type="file" id="profile_pic" name="profile_pic">
                  </div>
                  <input type="hidden" name="UserId" id="UserId" />
                  <input type="submit" name="editUsers" value="Submit" class="btn btn-primary"/>
                </div>
              </form>
            </div> -->
            <!-- <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div> -->
            <!--</div>modal-body -->
            <!--</div>modal-content -->
            <!--</div>modal-dialog -->
            <!--</div>modal fade-->

<!-- *********************** EDIT MODAL END***************************** -->