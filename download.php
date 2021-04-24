<?php
    // To start the session
    session_start();

    //importing files
    include_once("twitterAuthFiles/config.php");
    include_once("twitterAuthFiles/OAuth.php");
    include_once("twitterAuthFiles/TwitterAPIExchange.php");
    include_once("twitterAuthFiles/twitteroauth.php");
    require('fpdf/fpdf.php');

    $followers = "https://api.twitter.com/1.1/followers/list.json";
    $screenname = $_REQUEST['screenname'];
    $getMethod = 'GET';

    $settings = array(
        'oauth_access_token' => '1223633399950102530-2M5y2931jO72nYbP9O3XZXcy6QRikw',
        'oauth_access_token_secret' => 'fl4qt3xfB3EcutvJ4P6FH4Ldltsp9Ja7Ijub9YDbIgYJx',
        'consumer_key' => 'MLgX7MjUHqSh7uoMo1cJWYkD7',
        'consumer_secret' => 'ztV1qbghto1DsJE7vRsPbDdCSXCbilTzLtE6sUk3lacdUMOusz'
    );

    $getfield = '?screen_name='.$screenname.'&count=10';
    $twitter = new TwitterAPIExchange($settings);
                $twitter->setGetfield($getfield)
                        ->buildOauth($followers, $getMethod)
                        ->performRequest();

    $followersres = json_decode($twitter->setGetfield($getfield)
                                        ->buildOauth($followers, $getMethod)
                                        ->performRequest(), $assoc = TRUE);

    $download = array();
    foreach($followersres['users'] as $follower){
        array_push($download, $follower['screen_name']);
    }
    if ($_REQUEST['type'] == 'xls') {
        header("Content-Disposition: attachment; filename=followers.xls");
        header("Content-Type: application/vnd.ms-excel;");
        header("Pragma: no-cache");
        header("Expires: 0");
        $out = fopen("php://output", 'w');
        foreach ($download as $data)
        {
            fwrite($out, "@".$data.PHP_EOL);
        }
        fclose($out);
    } else {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);

        $pdf->Cell(40,10,'Followers of @'.$screenname);

        $pdf->Ln(10);
        foreach($download as $data){
            $pdf->Cell(40,10,"@".$data);
            $pdf->Ln(10);
        }
        $pdf->Output();
    }
?>