<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Route::get('/', 'HomeController@index');
Route::get('downloadfile', 'HomeController@downloadfile');
Route::get('/', 'UploadController@index');

Route::group(array('before' => 'ajax|ajaxban'), function () {
    Route::post('uploadfiles', ['as' => 'files.upload', 'uses' => 'UploadController@upload'])->before('ban');
    // Emails test
    Route::get('sharefiles', ['as' => 'files.share', 'uses' => 'UploadController@transferemail'])->before('ban');
});

//Just a test
Route::get('/downloadTransfer/{unique_id?}', 'DownloadController@index');
Route::get('/download/{id}/{pid?}', 'DownloadController@download');
Route::get('/progress/{pid}', 'DownloadController@progress');
Route::get('/server/{fileName}', 'DownloadController@serveFile');

Route::get('/curltest', function() {
    $ch = curl_init("http://google.com");
    // initialize curl handle
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $data = curl_exec($ch);
    print($data);
});

Route::get('/utils',function(){

    $ops = [
        'passwordCredentials' => [
            'username' => $_ENV['swiftusername'],
            'password' => $_ENV['swiftpassword'],
        ]
    ];
    // If a tenant ID is provided, added it to the auth array.
    $ops['tenantName'] = $_ENV['swifttenantname'];
    $envelope = [
        'auth' => $ops,
    ];
    $data = json_encode($envelope);
    $urli = $_ENV['swiftendpoint']."/tokens";
    $ch = curl_init($urli);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    try {
        $response = curl_exec($ch);
    }
    catch(Exception $ex){
        var_dump($ex);
    }
    $info = curl_getinfo($ch);
    curl_close($ch);


        //$token = $identity->authenticateAsUser($_ENV['swiftusername'], $_ENV['swiftpassword'], null, $_ENV['swifttenantname']);


    echo '<h2>$response</h2><pre>';
    var_dump(\GuzzleHttp\json_decode($response));
    echo '</pre>';

    echo '<h2>curl version</h2><pre>';
    var_dump(curl_version());
    echo '</pre>';

    echo 'finished';
});

