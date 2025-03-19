            <?php
            session_start();

            if (isset($_SESSION["adminName"])) {
                echo json_encode(["success" => true, "adminName" => $_SESSION["adminName"]]);
            } else {
                echo json_encode(["success" => false, "message" => "Not logged in"]);
            }
            ?>
