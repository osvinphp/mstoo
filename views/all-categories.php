<?php
// echo "<pre>"; print_r($AllCategories); die;
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      CATEGORIES
      <!-- <small>User table</small> -->
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">ALL CATEGORIES</h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Icon</th>
                    <th>Category Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter=1;
                  $base_url=base_url();
                  $img_path='public/assets/images/';
                  foreach ($AllCategories as $key => $value) {  ?>
                  <tr>
                    <td><?php echo $counter; ?></td>
                    <td><?php echo "<img class='round_cls'  width='90' src='".$base_url.$img_path.$value->icon."' / >";?></td>
                    <td><?php echo $value->category_name; ?></td>
                    <td><a  data-toggle="modal" data-target="#myModal" class="btn btn-block btn-primary"
                      data-id="<?php echo $value->id;?>"
                      data-name="<?php echo $value->category_name;?>"
                      data-icon="<?php echo $value->icon;?>">Edit</a></td>
                      <td><a href="<?php base_url();?>delete_category/<?php echo $value->id; ?>" onclick="return confirm('Are you sure?')" class="btn btn-block btn-primary">Delete</a></td>  
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

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <!-- <b>Version</b> 2.4.0 -->
    </div>
    <strong>Copyright &copy; 2018.<a href="https://adminlte.io">MSToo</a>.</strong> All rights
    reserved.
  </footer>

  <div class="control-sidebar-bg"></div>
</div>

<!--******************** FETAILS MODAL BOX STARTS HERE ************************************-->
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Quick Edit Category</h3>
            </div>
            <form role="form" name="editUser" method="POST" action="UpdateCategory" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="UpdateName">Category Name</label>
                  <input type="text" class="form-control" name="update_category" id="update_category" required/>
                </div>

                <div class="form-group">
                  <label for="update_icon">Icon</label>
                  <img src="" style="width:90px;height:auto;border-radius:50%;margin-left:10px;" id="update_icon" />
                  <input type="file" id="profile_pic" name="update_icon">
                </div>

                <input type="hidden" name="update_Id" id="update_Id" />
                <input type="submit" name="editCategory" value="Submit" class="btn btn-primary"/>
              </div>

            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          </div>

        </div><!--modal-body -->
      </div><!--modal-content -->
    </div><!--modal-dialog  -->
  </div><!--modal fade -->
  <!--************************************ FETAILS MODAL BOX ENDS HERE ************************************-->
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
    $('#myModal').on('show.bs.modal', function(e) {

     var Id = $(e.relatedTarget).data('id');
     var category_name = $(e.relatedTarget).data('name');
     var Icon = $(e.relatedTarget).data('icon');

     $('#update_Id').val(Id);   
     $('#update_category').val(category_name);            
     $('#update_icon').val(Icon); 
   });
  });
</script>
</body>
</html>
<!-- *********************** EDIT MODAL END  ***************************** -->



