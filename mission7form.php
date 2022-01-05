<?php
require 'vendor/autoload.php';
	
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


$link = mysqli_connect('missions7.cq986jtfyvcj.ap-southeast-1.rds.amazonaws.com','admin' , 'mission7','db_mission7');
if($link === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
} else {
    echo "Connected <br>";
}
$fullname = $_REQUEST['fullname'];
$gender = $_REQUEST['gender'];
$sql="INSERT INTO mission7 (fullname, gender)

VALUES

('$_POST[fullname]','$_POST[gender]')";
if(mysqli_query($link , $sql)){
    echo "Inserted Success";
} 
else{
    echo mysqli_error($link);
}
mysqli_close($link);


 //Upload To S3
$bucketName = 'mssion6';
	$IAM_KEY = 'AKIA3F2CYPN7VDQQHUEC';
	$IAM_SECRET = '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';
echo $_FILES['profile']['name'];
try {
    // You may need to change the region. It will say in the URL when the bucket is open
    // and on creation.
    $s3 = S3Client::factory(
        array(
            'credentials' => array(
                'key' => $IAM_KEY,
                'secret' => $IAM_SECRET
            ),
            'version' => 'latest',
            'region'  => 'ap-southeast-1'
        )
    );
} catch (Exception $e) {
    // We use a die, so if this fails. It stops here. Typically this is a REST call so this would
    // return a json object.
    die("Error: " . $e->getMessage());
}


// For this, I would generate a unqiue random string for the key name. But you can do whatever.
$keyName = 'mission7_image/' . basename($_FILES["profile"]['name']);
$pathInS3 = 'https://s3.ap-southeast-1.amazonaws.com/' . $bucketName . '/' . $keyName;

// Add it to S3
$url = '';
try {
    // Uploaded:
    $file = $_FILES["profile"]['tmp_name'];

    $s3->putObject(
        array(
            'Bucket'=>$bucketName,
            'Key' =>  $keyName,
            'SourceFile' => $file,
            'StorageClass' => 'REDUCED_REDUNDANCY'
        )
    
    );
    $url = $s3->getObjectUrl($bucketName , $keyName); 

} catch (S3Exception $e) {
    die('Error:' . $e->getMessage());
} catch (Exception $e) {
    die('Error:' . $e->getMessage());
}


echo '<br>';
echo 'Done <br>';
if($url !== '') {
echo 'Preivew Image <br>';
echo "<img src='$url'/>";
}


?>