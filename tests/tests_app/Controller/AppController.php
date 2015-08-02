<?php
namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    static public function canUploadMedias($ref, $refId) {
        if($ref == 'Pages' || $refId == 2){
            return false;
        }else{
            return true;
        }
    }
}