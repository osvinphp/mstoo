
<!-- jQuery 3 -->

<script src="<?php echo base_url(); ?>/public/template/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/raphael/raphael.min.js"></script>
<script src="<?php echo base_url(); ?>/public/template/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>/public/template/bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?php echo base_url(); ?>/public/template/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo base_url(); ?>/public/template/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo base_url(); ?>/public/template/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url(); ?>/public/template/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url(); ?>/public/template/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>/public/template/dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo base_url(); ?>/public/template/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url(); ?>/public/template/dist/js/demo.js"></script>
<script src="<?php echo base_url(); ?>public/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>public/template/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
   $(document).ready(function(){

  var url= window.location.href;
  var n  = url.split(" ");
  var page=n[n.length - 1];
   
    // alert(url);
  $('li a').click(function(){
  	 // alert('hello');
     $('li a').removeClass("active");
    $(this).addClass("active");
});
});
    </script>
</body>
</html>
