<?php
    function echoJSON($withStatus,$andMessage){
        $data = array('status' => $withStatus, 'message' => $andMessage);
        $jsonstring = json_encode($data);
        header('Content-Type: application/json');
        echo $jsonstring;
    }
    $user = 'root';
    $password = 'root';
    $db = 'HealthyDaySelfData';
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
        if($key == $privateKey) {
            $query = $_GET["query"];
            switch ($query){
                case "get":
                    $result = mysqli_query($link,"SELECT * FROM `SelfRunningData`");
                    while($row=mysqli_fetch_array($result)){
                        $dataArray[] = array('date'=> $row["Date"], 'distance'=> $row["Distance"], 'duration'=> $row["Duration"], 'durationPerKilometer'=> $row["DurationPerKilometer"]);
                    }
                    $data = array('status' => true, 'runningdata' => $dataArray);
                    $jsonstring = json_encode($data);
                    header('Content-Type: application/json');
                    echo $jsonstring;
                    break;
                case "set":
                    $distance = doubleval($_GET["distance"]);
                    $duration = intval($_GET["duration"]);
                    $durationPerKilometer = intval($_GET["durationperkilometer"]);
                    if($distance && $duration && $durationPerKilometer){
                        mysqli_query($link,"INSERT INTO `SelfRunningData` (`Date`, `Distance`, `Duration`, `DurationPerKilometer`) VALUES (CURRENT_TIMESTAMP, $distance, $duration, $durationPerKilometer);");
                        echoJSON(true,"setting success");
                    }else{
                        echoJSON(false,"invalid value");
                    }
                    break;
                case "delete":
                    $dateOrigin = $_GET["date"];
                    $date = str_replace("_"," ",$dateOrigin);
                    //`Date` = '2016-11-01 00:00:00'
                    if($date){
                        mysqli_query($link,"DELETE FROM `SelfRunningData` WHERE `Date` = '$date'");
                        echoJSON(true,"delete success");
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

