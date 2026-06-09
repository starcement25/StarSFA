<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../sfa_connection.php");
$localDB = new sfa_connection();
$conn = $localDB->conn;

ob_start();
session_start();

if(strtoupper($_SESSION['nick_name']) == 'STAR' && 
   (strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' || strtoupper($_SESSION['admin_login']) == 'E0674'))
{
    require("adminUtils_accounts.php");
}
else
{
    require("adminUtils.php");
}

if($_SESSION['admin_login'] == '') header('location: index.php');


ob_end_flush();

/* ===============================
   BIRTHDAY UPDATE LOGIC
================================= */

$message = "";
$status  = false;

$result = mysqli_query($conn,"SELECT * FROM birthday_master LIMIT 1");
$data   = mysqli_fetch_assoc($result);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $new_message = mysqli_real_escape_string($conn,$_POST['message']);
    $image_path  = $data['img'];

    // Image Upload
    if (!empty($_FILES['img']['name'])) {

        $target_dir = "../birthday/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_ext = strtolower(pathinfo($_FILES["img"]["name"], PATHINFO_EXTENSION));
        $allowed  = array("jpg","jpeg","png","gif");

        if (in_array($file_ext,$allowed)) {

            $new_file = "birthday_" . time() . "." . $file_ext;
            $target   = $target_dir . $new_file;

            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target)) {
                $image_path = "birthday/".$new_file;
            } else {
                $message = "Image Upload Failed!";
            }
        } else {
            $message = "Only JPG, JPEG, PNG, GIF allowed!";
        }

        $server_url = "https://" . $_SERVER['SERVER_NAME']."/";
    //$final_path=$server_url .$image_path;
    $final_path=$image_path;
        $update = "UPDATE birthday_master 
               SET img='$final_path' WHERE id='1'";

    mysqli_query($conn,$update);
    }
    
    $update = "UPDATE birthday_master 
               SET message='$new_message' WHERE id='1'";

    if(mysqli_query($conn,$update)){
        $status = true;
        $message = "Updated Successfully!";
    } else {
        $message = "Update Failed!";
    }
    header("Location: https://sfa.starcement.co.in/misreport/update_birthday_master.php?message=$message&status=$status");
    /*$result = mysqli_query($conn,"SELECT * FROM birthday_master LIMIT 1");
    $data   = mysqli_fetch_assoc($result);*/
}
disphtml("main();");
/* ===============================
   MAIN DESIGN
================================= */

function main()
{
    global $data,$message,$status;
    
?>

<!-- Birthday Button -->
<br><br>
<center>
    <button onclick="openPopup()" 
        style="padding:10px 20px; background:#b52b65; color:#fff; border:none; cursor:pointer;">
        Update Birthday Wish
    </button>
</center>

<!-- Overlay -->
<div id="popupOverlay" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
    z-index:999;">
</div>

<!-- Popup Box -->
<div id="birthdayPopup" style="
    display:none;
    position:fixed;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:450px;
    background:#fff;
    padding:20px;
    box-shadow:0 0 15px #000;
    z-index:1000;">

    <h3>Update Birthday Wish</h3>
    <hr>

    <?php if(isset($_GET['message']) && $_GET['message'] != "") { $status=$_GET['status'];?>
    <div style="padding:8px; color:#fff; background:<?php echo $status ? 'green' : 'red'; ?>">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
    <br>
<?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Birthday Message:</label><br>
        <textarea name="message" rows="4" style="width:100%;" required><?php 
            echo isset($data['message']) ? $data['message'] : ''; 
        ?></textarea>

        <br><br>

        <label>Current Image:</label><br>
        <?php if(!empty($data['img'])) { ?>
            <img src="<?php echo $data['img']; ?>" width="150">
        <?php } else { ?>
            No Image Found
        <?php } ?>

        <br><br>

        <label>Upload New Image:</label><br>
        <input type="file" name="img">

        <br><br>

        <button type="submit" 
            style="background:#b52b65; color:#fff; border:none; padding:8px 15px;">
            Update
        </button>

        <button type="button" onclick="closePopup()" 
            style="padding:8px 15px;">
            Close
        </button>

    </form>
</div>

<!-- JavaScript (Version 0.6.1 Compatible / Plain JS) -->
<script type="text/javascript">
function openPopup() {
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("birthdayPopup").style.display = "block";
}

function closePopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("birthdayPopup").style.display = "none";
}
</script>
<script>
openPopup();
</script>
<?php if($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
<script>
openPopup();
</script>
<?php } ?>

<?php
}
?>