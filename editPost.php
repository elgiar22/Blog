<?php require_once 'inc/header.php' ?>
 <!-- Page Content -->
 <div class="page-heading products-heading header-text">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>Edit Post</h4>
              <h2>edit your personal post</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

<div class="container w-50 ">
<div class="d-flex justify-content-center">
    <h3 class="my-5">edit Post</h3>
  </div>
<?php 
require_once 'inc/conn.php';
require_once 'inc/security.php';

if(!isset($_SESSION['user_id'])):
    header("location:Login.php");
    exit;
endif;

if(isset($_GET['id'])):
    $id = (int)$_GET['id']; // Cast to integer for safety
    
    // Check if user owns the post
    if(!userOwnsPost($conn, $id, $_SESSION['user_id'])):
        $_SESSION['errors'] = ["You are not authorized to edit this post"];
        header("location:index.php");
        exit;
    endif;
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT title, body, image FROM posts WHERE id = ?");
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
<?php require_once 'inc/errors.php'  ?>
    <form method="POST" action="handle/update.php?id=<?php echo $id  ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-3">
            <label for="body" class="form-label">Body</label>
            <textarea class="form-control" id="body" name="body" rows="5"><?php echo htmlspecialchars($post['body'], ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image (optional - leave empty to keep current)</label>
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
        </div>
        <div class="mb-3">
            <label>Current Image:</label><br>
            <img src="uploads/<?php echo htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" width="100px">
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Update Post</button>
    </form>
</div>


<?php require_once 'inc/footer.php' ?>