<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request){
        $array = ['error' => '', 'list' => []];
        $property = $request->input('property');
        
        if($property){
            $unitExists = Unit::find($property);

            if($unitExists){
                $user = auth()->user();
                $unit = Unit::where('id', $property)
                ->where('id_owner', $user['id'])->count();
    
                if($unit > 0){
                    $warnings = Warning::where('id_unit', $property)
                    ->orderBy('datecreated', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get();

                    foreach($warnings as $warnKey => $warnValue){
                        $warnings[$warnKey]['datecreated'] = date('d/m/Y', strtotime($warnValue['datecreated']));
                        $photoList = [];
                        $photos = explode(',', $warnValue['photos']);
                        foreach($photos as $photo){
                            if(!empty($photo)){
                                $photoList[] = asset('storage/'.$photo);
                            }
                        }                        
                        $warnings[$warnKey]['photos'] = $photoList;
                    }

                    $array['list'] = $warnings;
                }
                else{
                    $array['error'] = "Você não tem autorização para visualizar dados da unidade de outra pessoa...";
                }
            }else{
                $array['error'] = "Esta unidade não existe em nosso sistema. Tente outra unidade...";
            }
        }else{
            $array['error'] = "É obrigatório enviar uma unidade para continuar...";
        }
        return $array;
    }

    public function addWarningFile(Request $request){
        $array = ['error' => ''];
        $photo = $request->file('photo');

        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|mimes:jpg, png'
        ]);

        if(!$validator->fails()){
            $file = $photo->store('public');
            $array['photo'] = asset(Storage::url($file));
            return $array;
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function setWarning(Request $request){
        $array  = ['error' => ''];
        $data   = $request->only('property', 'title', 'list');
        $rules  = ['property' => 'required', 'title' => 'required'];

        $validator = Validator::make($data, $rules);
        if(!$validator->fails()){
            $property   = $request->input('property');
            $title      = $request->input('title');
            $list       = $request->input('list');

            $newWarn = new Warning;
            $newWarn->id_unit       = $property;
            $newWarn->title         = $title;
            $newWarn->datecreated   = date('Y-m-d');

            if($list && is_array($list)){
                $photos = [];
                foreach($list as $listItem){
                    $url = explode('/', $listItem);
                    $photos[] = end($url);
                }
                $newWarn->photos = implode(',', $photos);
            }else{
                $newWarn->photos = '';
            }

            $newWarn->save();
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
