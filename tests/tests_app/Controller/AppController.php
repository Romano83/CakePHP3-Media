<?php
namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    static public function canUploadMedias($ref, $refId)
    {
        return true;
    }
}