<?php
session_start();

if(isset($_POST['employee_list'])) {
    $_SESSION['temp_employee_list'] = $_POST['employee_list'];
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No employee list provided']);
}
?>