<?php require_once 'inc/header.php' ?>
<?php require_once 'inc/conn.php' ?>



    <!-- Page Content -->
    <div class="page-heading products-heading header-text">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>view Post</h4>
              <h2>view new personal post</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php 
    require_once 'inc/security.php';
    
    if(isset($_GET['id'])):
        $id = (int)$_GET['id']; // Cast to integer for safety
        
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT posts.*, users.name as author_name FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows === 1):
            $post = $result->fetch_assoc();
        else:
            header("location:404.php");
            exit;
        endif;
    else:
        header("location:404.php");
        exit;
    endif;
    ?> 
    
    <div class="best-features about-features">
      <div class="container">
        <?php  require_once 'inc/success.php' ?>
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <h2>Our Background</h2>
            </div>
          </div>
          <div class="col-md-6">
            <div class="right-image">
              <img src="uploads/<?php echo htmlspecialchars($post["image"], ENT_QUOTES, 'UTF-8'); ?>" alt="">
            </div>
          </div>
          <div class="col-md-6">
            <div class="left-content">
              <h4><?php echo htmlspecialchars($post["title"], ENT_QUOTES, 'UTF-8'); ?></h4>
              <p><?php echo htmlspecialchars($post["body"], ENT_QUOTES, 'UTF-8'); ?></p>
              <p>created_at : <?php echo htmlspecialchars($post["created_at"], ENT_QUOTES, 'UTF-8');?></p>
              <p>created_by : <?php echo htmlspecialchars($post["author_name"], ENT_QUOTES, 'UTF-8');?></p>


                  <?php if(isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $post["user_id"]): ?>

              <div class="d-flex justify-content-center">
                  <a href="editPost.php?id=<?php echo $id; ?>" class="btn btn-success mr-3">Edit Post</a>
                    <form action="handle/delete.php?id=<?php echo $id ?>" method="post" >
                      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                      <button type="submit"  name="submit" class="btn btn-danger " >delete Post</button>
                    </form>       
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
</div>

<?php require_once 'inc/footer.php' ?>
