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
});
//Just a test
Route::get('/download/{id}', 'UploadController@download');

Route::get('/curltest', function() {
    $ch = curl_init("http://google.com");
    // initialize curl handle
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $data = curl_exec($ch);
    print($data);
});

Route::get('/utils',function(){


    $identity = new \OpenStack\Identity\v2\IdentityService($_ENV['swiftendpoint']);


    echo '<h2>identity</h2><pre>';
    var_dump($identity);
    echo '</pre>';

    try {
        $token = $identity->authenticateAsUser($_ENV['swiftusername'], $_ENV['swiftpassword'], null, $_ENV['swifttenantname']);


        echo '<h2>token</h2><pre>';
        var_dump($token);
        echo '</pre>';
    }
    catch(\OpenStack\Common\Exception $ex){
        echo '<h2>token</h2><pre>';
        var_dump($ex);
        echo '</pre>';
    }

    echo 'finished';
});

