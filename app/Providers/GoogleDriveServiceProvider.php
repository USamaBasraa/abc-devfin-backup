<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem;
use Storage;
use Google_Client;
class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
  
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
       \Storage::extend("google",function($app,$config){
        $client=new \Google_Client;
        $client->setClientId($config['clientId']);
        $client->setClientSecret($config['clientSecret']);
        $client->setAccessType('offline');
        $client->refreshToken($config['refreshToken']);
        $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
        $service = new \Google_Service_Drive($client);
        $adapter = new GoogleDriveAdapter($service,$config['folderId']);
        return new Filesystem($adapter);

       });
        //dd($client);
    }
}
