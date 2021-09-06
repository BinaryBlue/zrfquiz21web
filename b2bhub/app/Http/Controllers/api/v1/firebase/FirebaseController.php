<?php

namespace App\Http\Controllers\api\v1\firebase;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Kreait\Firebase\Firestore;
//use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\User;
use PDF;

class FirebaseController extends Controller
{
    // private $firestore = null;
    // public function __construct(Firestore $firestore)
    // {
    //     $this->firestore = $firestore;
    // }

    public function participents(Request $r){
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/firebase_credentials.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();

        return ["result"=>"OK"];
    }
}
