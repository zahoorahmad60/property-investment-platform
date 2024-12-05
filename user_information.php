<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['username'])){
    header("Location: login.php"); 
    exit();
}
$pageTitle = "profile";
include "init.php";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $experience = $_POST['experience'];
    $company_working = $_POST['company_working'];
    $education_level = $_POST['education_level'];
    $gender = $_POST['gender'];
    $user_id = $_SESSION['ID'];
    $id = $_SESSION['id'];

    if($id != ''){
        $stmt = $conn->prepare("UPDATE `user_profile` SET `company_working` = ?, `years_experience` = ?, `education_level` = ?, `gender` = ?, `user_id` = ? WHERE id = ?");

        $stmt->execute(array($company_working, $experience, $education_level, $gender, $user_id, $id));
    }else{
        $stmt = $conn->prepare("INSERT INTO
                            user_profile(`company_working`, `years_experience`, `education_level`, `gender`, `user_id`)
                        VALUES(:zcompany_working, :zyears_experience, :zeducation_level, :zgender, :zuser_id)");
        $stmt->execute(array(

            'zcompany_working'          => $experience,
            'zyears_experience'         => $company_working,
            'zeducation_level'          => $education_level,
            'zgender'                   => $gender,
            'zuser_id'                  => $user_id,
        ));
    }
    header("Location: user_information.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM `user_profile` WHERE `user_id` = ?");
$stmt->execute(array($_SESSION['ID']));
$row = $stmt->fetch();
?>
<link rel="stylesheet" href="layout/css/reg.css">
<script>
</script>
<style>
    .container{
        display: flex;
        flex-wrap: wrap;
    }
</style>
<form action="user_information.php" method="post" class="register">

    <!-- Register Form -->
    <div class="container">
        <div class="form-group" style="width:48%">
            <label for="Fname">Years of experience:</label>
            <input type="hidden" id="id" name="id"  value="<?= @$row['id']?>">
            <input type="number" id="experience" name="experience" placeholder="Years of experience" value="<?= @$row['years_experience']?>" required>
        </div>
        <div class="form-group" style="width:48%">
            <label for="Fname">Company working:</label>
            <input type="text" id="company_working" name="company_working" placeholder="Company working" value="<?= @$row['company_working']?>" required>
        </div>
        <div class="form-group" style="width:48%">
            <label for="Fname">Education level:</label>
            <input type="text" id="education_level" name="education_level" placeholder="Education level" value="<?= @$row['education_level']?>" required>
        </div>
        <div class="form-group" style="width:48%">
            <label for="registerUserType">Gender:</label>
            <select id="gender" name="gender">
                <option value="male" <?php echo @$row['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?php echo @$row['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
            </select>
        </div>
            <input type="submit" value="Send" name="submit">
    </div>

</form>

</body>

</html>