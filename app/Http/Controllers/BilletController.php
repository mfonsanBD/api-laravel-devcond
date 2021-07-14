<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Billet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BilletController extends Controller
{
    public function getAll(Request $request){
        $array = ['error' => '', 'list' => []];
        $property = $request->input('property');
        
        if($property){
            $unitExists = Unit::find($property);

            if($unitExists){
                $user = auth()->user();
                $unit = Unit::where('id', $property)
                ->where('id_owner', $user['id'])->count();
    
                if($unit > 0){
                    $billets = Billet::where('id_unit', $property)->get();
    
                    foreach($billets as $billetKey => $billetkValue){
                        $billets[$billetKey]['fileurl'] = asset('storage/'.$billetkValue['fileurl']);
                    }
        
                    $array['list'] = $billets;
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
}
