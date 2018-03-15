<?php // echo "<pre>"; print_r($qrcodes); die;  ?>
<div class="wrapper">
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Add Category
      </h1>
    </section>
    <section class="content">
     <div class="box">
       <div class="box-header with-border">
        <span id="span_msg" style="text-align: center; color:green;font-size: 15px;"></span>
        <div class="edit-pro">
         <?php if($msg > 0 && (!empty($msg))){  ?>
         <div class="alert alert-success alert-dismissible fade in">
         <strong>Category</strong> has been added successfully.
         </div>
         <?php   } ?>
         <form method="POST" action="add-category" enctype= multipart/form-data>
          <span id="errmsg" style="color:red;"></span>
          <div class="form-group">
            <label for="category">Category Name</label>
            <input type="text" class="form-control" id="category"  name="category" placeholder="Enter Category Name" required />
          </div>

          <div class="form-group">
            <label for="icon">Icon</label>
            <input type="file" class="form-control-file" id="icon" name="icon" required/>
          </div>

          <input type="submit" class="btn btn-primary" name="submit" value="SUBMIT" />
        </form>
      </div>
    </div>
  </div>
</section>
</div>
</div>
</body>