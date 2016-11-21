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
                        $dataArray[] = array('date'=> $row["Date"], 'distance'=> (double)$row["Distance"], 'duration'=> (int)$row["Duration"], 'durationPerKilometer'=> (int)$row["DurationPerKilometer"]);
                    }
                    $data = array('status' => true, 'runningdata' => $dataArray);
                    $jsonstring = json_encode($data);
                    header('Content-Type: application/json');
                    echo $jsonstring;
                    break;
                case "set":
                    $dateString = str_replace("_"," ",$_GET["date"]);
                    $distance = doubleval($_GET["distance"]);
                    $duration = intval($_GET["duration"]);
                    $durationPerKilometer = intval($_GET["durationperkilometer"]);
                    if($dateString && $distance && $duration && $durationPerKilometer){
                        mysqli_query($link,"INSERT INTO `SelfRunningData` (`Date`, `Distance`, `Duration`, `DurationPerKilometer`) VALUES (TIMESTAMP '$dateString', $distance, $duration, $durationPerKilometer);");
                        echoJSON(true,"setting success");
                    }else{
                        echoJSON(false,"invalid value");
                    }
                    break;
                case "delete":
                    $dateString = str_replace("_"," ",$_GET["date"]);
                    if($dateString){
                        mysqli_query($link,"DELETE FROM `SelfRunningData` WHERE `Date` = '$dateString'");
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

