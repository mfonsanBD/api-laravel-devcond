<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function unauthorized(){
        return response()->json(['error'=>'Você não tem autorização para acessar esta página.'], 401);
    }

    public function register(Request $request){
        $array = ['error' => ''];

        $data = $request->only('name', 'email', 'cpf', 'password', 'password_confirmation');
        $rules = [
            'name'                  => 'required|min:3',
            'email'                 => 'required|email|unique:users,email',
            'cpf'                   => 'required|digits:11|unique:users,cpf',
            'password'              => 'required|min:3',
            'password_confirmation' => 'required|same:password'
        ];

        $validator = Validator::make($data, $rules);

        if(!$validator->fails()){
            $name       = $request->input('name');
            $email      = $request->input('email');
            $cpf        = $request->input('cpf');
            $password   = password_hash($request->input('password'), PASSWORD_DEFAULT);

            $newUser = new User;
            $newUser->name     = $name;
            $newUser->email    = $email;
            $newUser->cpf      = $cpf;
            $newUser->password = $password;
            $newUser->save();

            $token = auth()->attempt([
                'cpf' => $cpf,
                'password' => $request->input('password')
            ]);

            if(!$token){
                $array['error'] = "Ocorreu um erro no sistema. Tente novamente mais tarde.";
                return $array;
            }

            $array['token'] = $token;

            $user = auth()->user();
            $array['user'] = $user;

            $properties = Unit::select('id', 'name')
            ->where('id_owner', $user['id'])->get();

            $array['user']['properties'] = $properties;
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function login(Request $request){
        $array = ['error' => ''];
        $data = $request->only('cpf', 'password');
        $validator = Validator::make($data, [
            'cpf' => 'required|digits:11',
            'password' => 'required'
        ]);

        if(!$validator->fails()){
            $cpf        = $request->input('cpf');
            $password   = $request->input('password');
            $token = auth()->attempt([
                'cpf' => $cpf,
                'password' => $request->input('password')
            ]);

            if(!$token){
                $array['error'] = "CPF e/ou senha incorretos.";
                return $array;
            }

            $array['token'] = $token;

            $user = auth()->user();
            $array['user'] = $user;

            $properties = Unit::select('id', 'name')
            ->where('id_owner', $user['id'])->get();

            $array['user']['properties'] = $properties;
        }else{
            $array['erros'] = $validator->errors()->first();
        }
        return $array;
    }

    public function validateToken(){
        $array = ['error' => ''];

        $user = auth()->user();
        $array['user'] = $user;

        $properties = Unit::select('id', 'name')
        ->where('id_owner', $user['id'])->get();

        $array['user']['properties'] = $properties;

        return $array;
    }

    public function logout(){
        $array = ['error' => ''];

        auth()->logout();
        
        return $array;
    }
}
