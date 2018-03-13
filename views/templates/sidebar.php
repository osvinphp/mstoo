  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel LOGo">
        <span class="logo-lg"><img src="<?php echo base_url(); ?>public/template/dist/img/logo_mstoo.png"></span>


      </div>
      <ul class="sidebar-menu" data-widget="tree">
        <li>
          <a  href="<?php echo base_url();?>dashboard"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i>
          </span>

        </li>


        <li>
          <a  href="<?php echo base_url();?>users">
            <i class="fa fa-users"></i> <span>Users</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li>


        <li>
          <a href="<?php echo base_url();?>posts">
            <i class="fa fa-th"></i> <span>Posts</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-caret-down"></i>
            <span>Reported</span>
            <span class="pull-right-container">
              <!-- <span class="label label-primary pull-right">4</span> -->
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url();?>reported_user"><i class="fa fa-user-plus"></i> Users</a></li>
            <li><a href="<?php echo base_url();?>reported_post"><i class="fa fa-calendar-plus-o"></i> Posts</a></li>
          </ul>
        </li>

         <li class="treeview">
          <a href="#">
            <i class="fa fa-caret-down"></i>
            <span>Category</span>
            <span class="pull-right-container">
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url();?>add-category"><i class="fa fa-user-plus"></i>Add Category</a></li>
            <li><a href="<?php echo base_url();?>view-category"><i class="fa fa-calendar-plus-o"></i>View Category</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-caret-down"></i>
            <span>Sub-Category</span>
            <span class="pull-right-container">
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url();?>add-sub-category"><i class="fa fa-user-plus"></i>Add Sub-Category</a></li>
            <li><a href="<?php echo base_url();?>view-sub-category"><i class="fa fa-calendar-plus-o"></i>View Sub-Category</a></li>
          </ul>
        </li>

      </section>
    </aside>

