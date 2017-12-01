<?php

    $con = mysqli_connect("localhost","task_db","Z8c2W6y8","task_db");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $error_handle = false;
        if (isset($_POST['name']) && !empty($_POST['name'])) {
            $name = mysqli_real_escape_string($con, test_input($_POST['name']));
        } else {
            //echo "<p>Name is required</p>";
            $error_handle = true;
        }
        if (isset($_POST['phone']) && !empty($_POST['phone'])) {
            $phone = mysqli_real_escape_string($con, test_input($_POST['phone']));
        } else {
            //echo "<p>Phone number is required</p>";
            $error_handle = true;
        }
        if (isset($_POST['message']) && !empty($_POST['message'])) {
            $message = mysqli_real_escape_string($con, test_input($_POST['message']));
        } else {
            $message = NULL;
        }

    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if (!$error_handle) {

        $sql = "SELECT phone FROM requests WHERE phone = '$phone';";
        $result = mysqli_query($con, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $exist_count = 0;
            while($row = mysqli_fetch_assoc($result)) {
                if ($row["phone"] == $phone) {
                    $exist_count ++;
                }
            }

            echo "<script>notifier.show('', 'Этот номер телефона уже зарегистрирован в базе: ".$phone.". Подождите пока наш менеджер свяжется с вами.', '', 'img/high_priority-48.png', 6000);</script>";

            exit();
        } else {
            //echo "0 results with this number<br>";
        }

        date_default_timezone_set('Europe/Kiev');
        $date = date('Y-m-d H:i');

        $sql = "INSERT INTO requests (name, phone, message, date)
        VALUES ('$name', '$phone', '$message', '$date')";
        
        if (!mysqli_query($con, $sql)) {
            echo "<p>Error</p>";
            die('Error: ' . mysqli_error($con));
        } else {
            echo "<script>notifier.show('Отлично!', 'Ваша заявка принята и ожидает обработки.', '', 'img/ok-48.png', 6000);</script>";
            echo "<script>deleteform()</script>";
        }

        mysqli_close($con);

    } else {
        exit();
    }

?>