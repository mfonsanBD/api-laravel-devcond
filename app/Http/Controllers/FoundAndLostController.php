<?php

namespace App\Http\Controllers;

use App\Models\FoundAndLost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FoundAndLostController extends Controller
{
    public function getAll(){
        $array = ['error' => ''];

        $lost = FoundAndLost::where('status', 'LOST')
        ->orderBy('datecreated', 'DESC')->orderBy('id', 'DESC')->get();

        $recovered = FoundAndLost::where('status', 'RECOVERED')
        ->orderBy('datecreated', 'DESC')->orderBy('id', 'DESC')->get();

        foreach($lost as $lostKey => $lostValue){
            $lost[$lostKey]['datecreated'] = date('d/m/Y', strtotime($lostValue['datecreated']));
            $lost[$lostKey]['photos'] = asset('storage/'.$lostValue['photos']);
        }

        foreach($recovered as $recKey => $recValue){
            $recovered[$recKey]['datecreated'] = date('d/m/Y', strtotime($recValue['datecreated']));
            $recovered[$recKey]['photos'] = asset('storage/'.$recValue['photos']);
        }
        
        $array['lost'] = $lost;
        $array['recovered'] = $recovered;

        return $array;
    }
    
    public function insert(Request $request){
        $array = ['error' => ''];

        $data = $request->only('description', 'where', 'photo');
        $rules = [
            'description' => 'required',
            'where' => 'required',
            'photo' => 'required|file|mimes:jpg,png|max:2048'
        ];

        $validator = Validator::make($data, $rules);

        if(!$validator->fails()){
            $description    = $request->input('description');
            $where          = $request->input('where');
            $file           = $request->file('photo')->store('public');
            $file           = explode('public/', $file);
            $photo          = $file[1];

            $newFoundAndLost = new FoundAndLost;
            $newFoundAndLost->photos        = $photo;
            $newFoundAndLost->description   = $description;
            $newFoundAndLost->where         = $where;
            $newFoundAndLost->datecreated   = date('Y-m-d');
            $newFoundAndLost->save();
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function update(Request $request, $id){
        $array      = ['error' => ''];
        $idExists   = FoundAndLost::find($id);

        if($idExists){
            $status = $request->input('status');
            if($status && in_array($status, ['LOST', 'RECOVERED'])){
                $idExists->status = $status;
                $idExists->save();
            }else{
                $array['error'] = "Status não identificado.";
                return $array;
            }
            
        }else{
            $array['error'] = "Este item não existe no sistema.";
            return $array;
        }

        return $array;
    }
}