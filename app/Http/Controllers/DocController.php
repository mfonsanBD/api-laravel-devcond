<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocController extends Controller
{
    public function getAll(){
        $array = ['error' => '', 'list' => []];

        $docsList = Doc::all();
        foreach($docsList as $docKey => $dockValue){
            $docsList[$docKey]['fileurl'] = asset('storage/'.$dockValue['fileurl']);
        }

        $array['list'] = $docsList;
        return $array;
    }
}
