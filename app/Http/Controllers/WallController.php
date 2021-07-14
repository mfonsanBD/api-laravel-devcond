<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wall;
use App\Models\WallLike;
use Illuminate\Http\Request;

class WallController extends Controller
{
    public function getAll(){
        $array = ['error' => '', 'list' => []];

        $user = auth()->user();

        $walls = Wall::all();

        foreach($walls as $wallkey => $wallvalue){
            $walls[$wallkey]['likes'] = 0;
            $walls[$wallkey]['liked'] = false;

            $likes = WallLike::where('id_wall', $wallvalue['id'])->count();
            $walls[$wallkey]['likes'] = $likes;

            $meLikes = WallLike::where('id_wall', $wallvalue['id'])
            ->where('id_user', $user['id'])->count();

            if($meLikes > 0){
                $walls[$wallkey]['liked'] = true;
            }
        }

        $array['list'] = $walls;

        return $array;
    }

    public function like($id){
        $array = ['error' => ''];

        $user = auth()->user();
        
        $meLikes = WallLike::where('id_wall', $id)
        ->where('id_user', $user['id'])->count();

        if($meLikes > 0){
            WallLike::where('id_wall', $id)
            ->where('id_user', $user['id'])->delete();
            $array['liked'] = false;
        }else{
            $newWallLike = new WallLike;
            $newWallLike->id_wall = $id;
            $newWallLike->id_user = $user['id'];
            $newWallLike->save();
            $array['liked'] = true;
        }

        $array['likes'] = WallLike::where('id_wall', $id)->count();

        return $array;
    }
}
