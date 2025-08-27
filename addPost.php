<?php require_once 'inc/header.php' ;
require_once 'inc/conn.php';
require_once 'inc/security.php';
?>
<?php 
if(!isset($_SESSION['user_id'])):
header(header: "location:Login.php");
exit();
endif;
?>
    <!-- Page Content -->
    <div class="page-heading products-heading header-text">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>new Post</h4>
              <h2>add new personal post</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
require_once 'inc/errors.php';
?>
    
<div class="container w-50 ">
  <div class="d-flex justify-content-center">
    <h3 class="my-5">add new Post</h3>
  </div>
  <form method="POST" action="handle/Store.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php if(isset($_SESSION['title'])) echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['title']);  ?>">
    </div>
    <div class="mb-3">
        <label for="body" class="form-label">Body</label>
        <textarea class="form-control" id="body" name="body" rows="5"><?php if(isset($_SESSION['body'])) echo htmlspecialchars($_SESSION['body'], ENT_QUOTES, 'UTF-8');  unset($_SESSION['body']); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
        <small class="form-text text-muted">Only JPEG, PNG, and GIF files allowed. Max size: 1MB</small>
    </div>
    <button type="submit" class="btn btn-primary" name="submit">Create Post</button>
  </form>
</div>

    <?php require_once 'inc/footer.php' ?>



