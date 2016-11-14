<?php
    function echoJSON($withStatus,$andMessage){
        $data = array('status' => $withStatus, 'message' => $andMessage);
        $jsonstring = json_encode($data);
        header('Content-Type: application/json');
        echo $jsonstring;
    }
    $user = 'root';
    $password = 'root';
    $db = 'temperatureData';
    $host = 'localhost';
    $port = 8889;
    $link = mysqli_init();
    $success = mysqli_real_connect(
                                   $link,
                                   $host,
                                   $user,
                                   $password,
                                   $db,
                                   $port
                                   );
    $privateKey = "lsw";
    if($success){
        $key = $_GET["key"];
        if($key == $privateKey){
            $query = $_GET["query"];
            switch ($query){
                case "get":
                    $result = mysqli_query($link,"SELECT * FROM `Temperature`");
                    $row = mysqli_fetch_array($result);
                    
                    $data = array('status' => true, 'date' => $row["Date"], 'value' => intval($row["Value"]));
                    $jsonstring = json_encode($data);
                    header('Content-Type: application/json');
                    echo $jsonstring;
                    break;
                case "set":
                    $value = $_GET["value"];
                    $valueInt = intval($value);
                    if($valueInt){
                        mysqli_query($link,"DELETE FROM `Temperature` WHERE 1");
                        mysqli_query($link,"INSERT INTO `Temperature`(`Date`, `Value`) VALUES (CURRENT_TIMESTAMP,$valueInt);");
                        $data = array('status' => true, 'message' => 'setting success');
                        $jsonstring = json_encode($data);
                        header('Content-Type: application/json');
                        echo $jsonstring;
                    }else{
                        echoJSON(false,"invalid value");
                    }
                    break;
                default:
                    echoJSON(false,"unsupported query");
            }
        }else{
            echoJSON(false,"invalid key");
        }
    }else{
        echoJSON(false,"Connect Error: " . mysqli_connect_error());
    }
    mysqli_close($link);
?>
