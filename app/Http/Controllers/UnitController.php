<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitPet;
use App\Models\UnitPeople;
use App\Models\UnitVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function getInfo($id){
        $array = ['error' => ''];

        $user = auth()->user();
        $propertyExists = Unit::find($id);

        if($propertyExists){
            $isOwner = Unit::where('id', $id)->where('id_owner', $user['id'])->count();
            if($isOwner > 0){
                $property  = Unit::where('id', $id)->get();
                $pet       = UnitPet::where('id_unit', $id)->get();
                $people    = UnitPeople::where('id_unit', $id)->get();
                $vehicle   = UnitVehicle::where('id_unit', $id)->get();

                foreach($people as $pKey => $pValue){
                    $people[$pKey]['birthdate'] = date('d/m/Y', strtotime($pValue['birthdate']));
                }

                $array['property']  = $property;
                $array['pet']       = $pet;
                $array['people']    = $people;
                $array['vehicle']   = $vehicle;
            }else{
                $array['error'] = "Você não pode acessar a propriedade de outra pessoa.";
            }
        }else{
            $array['error'] = "Essa propriedade não existe.";
        }

        return $array;
    }

    public function addPerson(Request $request, $id){
        $array = ['error' => ''];

        $user = auth()->user();
        $propertyExists = Unit::find($id);

        if($propertyExists){
            $isOwner = Unit::where('id', $id)->where('id_owner', $user['id'])->count();
            if($isOwner > 0){
                $data = $request->only('name', 'birthdate');
                $rules = ['name' => 'required', 'birthdate' => 'required|date'];
        
                $validator = Validator::make($data, $rules);

                if(!$validator->fails()){
                    $name                   = $request->input('name');
                    $birthdate              = $request->input('birthdate');

                    $newPerson              = new UnitPeople;
                    $newPerson->id_unit     = $id;
                    $newPerson->name        = $name;
                    $newPerson->birthdate   = $birthdate;
                    $newPerson->save();
                }else{
                    $array['error'] = $validator->errors()->first();
                    return $array;
                }
            }else{
                $array['error'] = "Você não pode acessar a propriedade de outra pessoa.";
                return $array;
            }
        }else{
            $array['error'] = "Essa propriedade não existe.";
            return $array;
        }
        
        return $array;
    }

    public function removePerson(Request $request, $id){
        $array = ['error' => ''];

        $user = auth()->user();
        $propertyExists = Unit::find($id);

        if($propertyExists){
            $isOwner = Unit::where('id', $id)->where('id_owner', $user['id'])->count();
            if($isOwner > 0){
                $personId = $request->input('id');
                $personExists = UnitPet::find($personId);

                if($personExists){
                    $personDel = UnitPeople::where('id', $personId)
                    ->where('id_unit', $id)->delete();

                    if(!$personDel){
                        $array['error'] = "Você só pode deletar uma pessoa da propriedade que ela está cadastrada.";
                        return $array;
                    }
                }else{
                    $array['error'] = "Essa pessoa não consta em nosso sistema.";
                    return $array;
                }
            }else{
                $array['error'] = "Você não pode acessar a propriedade de outra pessoa.";
                return $array;
            }
        }else{
            $array['error'] = "Essa propriedade não existe.";
            return $array;
        }
        
        return $array;
    }
    
    public function addPet(Request $request, $id){
        $array = ['error' => ''];

        $user = auth()->user();
        $propertyExists = Unit::find($id);

        if($propertyExists){
            $isOwner = Unit::where('id', $id)->where('id_owner', $user['id'])->count();
            if($isOwner > 0){
                $data = $request->only('name', 'race');
                $rules = ['name' => 'required', 'race' => 'required'];
        
                $validator = Validator::make($data, $rules);

                if(!$validator->fails()){
                    $name              = $request->input('name');
                    $race              = $request->input('race');

                    $newPet            = new UnitPet;
                    $newPet->id_unit   = $id;
                    $newPet->name      = $name;
                    $newPet->race      = $race;
                    $newPet->save();
                }else{
                    $array['error'] = $validator->errors()->first();
                    return $array;
                }
            }else{
                $array['error'] = "Você não pode acessar a propriedade de outra pessoa.";
                return $array;
            }
        }else{
            $array['error'] = "Essa propriedade não existe.";
            return $array;
        }
        
        return $array;
    }

    public function removePet(Request $request, $id){
        $array = ['error' => ''];

        $user = auth()->user();
        $propertyExists = Unit::find($id);

        if($propertyExists){
            $isOwner = Unit::where('id', $id)->where('id_owner', $user['id'])->count();
            if($isOwner > 0){
                $petId = $request->input('id');
                $petExists = UnitPet::find($petId);
                
                if($petExists){
                    $petDel = UnitPet::where('id', $petId)
                    ->where('id_unit', $id)->delete();

                    if(!$petDel){
                        $array['error'] = "Você só pode deletar um pet da propriedade que ele está cadastrado.";
                        return $array;
                    }
                }else{
                    $array['error'] = "Esse pet não consta em nosso sistema.";
                    return $array;
                }
            }else{
                $array['error'] = "Você não pode acessar a propriedade de outra pessoa.";
                return $array;
            }
        }else{
            $array['error'] = "Essa propriedade não existe.";
            return $array;
        }
        
        return $array;
    }

    public function addVehicle(Request $request, $id){
        $array = ['error' => ''];

        $user = auth()->user();
        $propertyExists = Unit::find($id);

        if($propertyExists){
            $isOwner = Unit::where('id', $id)->where('id_owner', $user['id'])->count();
            if($isOwner > 0){
                $data = $request->only('title', 'color', 'plate');
                $rules = ['title' => 'required', 'color' => 'required', 'plate' => 'required'];
        
                $validator = Validator::make($data, $rules);

                if(!$validator->fails()){
                    $title              = $request->input('title');
                    $color              = $request->input('color');
                    $plate              = $request->input('plate');

                    $newVehicle             = new UnitVehicle;
                    $newVehicle->id_unit    = $id;
                    $newVehicle->title      = $title;
                    $newVehicle->color      = $color;
                    $newVehicle->plate      = $plate;
                    $newVehicle->save();
                }else{
                    $array['error'] = $validator->errors()->first();
                    return $array;
                }
            }else{
                $array['error'] = "Você não pode acessar a propriedade de outra pessoa.";
                return $array;
            }
        }else{
            $array['error'] = "Essa propriedade não existe.";
            return $array;
        }
        
        return $array;
    }

    public function removeVehicle(Request $request, $id){
        $array = ['error' => ''];

        $user = auth()->user();
        $propertyExists = Unit::find($id);

        if($propertyExists){
            $isOwner = Unit::where('id', $id)->where('id_owner', $user['id'])->count();
            if($isOwner > 0){
                $vehicleId = $request->input('id');
                $vehicleExists = UnitVehicle::find($vehicleId);
                
                if($vehicleExists){
                    $vehicleDel = UnitVehicle::where('id', $vehicleId)
                    ->where('id_unit', $id)->delete();

                    if(!$vehicleDel){
                        $array['error'] = "Você só pode deletar um veículo da propriedade que ele está cadastrado.";
                        return $array;
                    }
                }else{
                    $array['error'] = "Esse veículo não consta em nosso sistema.";
                    return $array;
                }
            }else{
                $array['error'] = "Você não pode acessar a propriedade de outra pessoa.";
                return $array;
            }
        }else{
            $array['error'] = "Essa propriedade não existe.";
            return $array;
        }
        
        return $array;
    }
}
