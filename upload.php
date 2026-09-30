<?php

$msg = "";

if(isset($_POST["upload"])) {
    $file = $_FILES["file"];

    if($file["error"] == 0 && $file["size"] <=200*1024*1024){
        if(!is_dir("uploads")) mkdir("uploads");

        move_uploaded_file($file["tmp_name"],
        "uploads/".basename($file["name"])
        );

      $msg = "file uploaded successfully";  
    }
    else{
        $msg = "upload failed or file is too large.";
    }
 }
 ?>


<!DOCTYPE html>
<html>
    <head>
        <title>File Upload</title>

        <style>
            body{
                font-family: arial;
                background: linear-gradient(135deg,#667eea, #764ba2);
                text-align: center;
                padding: 60px;
            }

            .box{
                background: white;
                max-width: 450px;
                margin: auto;
                padding: 35px;
                border-radius: 15px;
                box-shadow: 0 5px 15px #3335;
            }

            h1{
                color: #444;
            }

            input{
                margin: 20px;
            }

            button{
                background: #667eea;
                color: while;
                border: 0;
                padding: 12px 25px;
                border-radius: 8px;
            }

            p{
                color: #555;
            }
            </style>
    </head>
    <body>
        <div class="box">
            <p>Selecta file to upload</p>

            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="file" required><br>
                <Button name = "upload">Upload File</button>
            </form>

            <p><?php echo $msg;
            ?>
            </p>
        </div>
    </body>
</html>