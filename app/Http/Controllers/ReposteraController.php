<?php

namespace App\Http\Controllers;

use App\Models\Repostera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReposteraController extends Controller
{
    public function AltaRepostera(Request $request){
        $validator = Validator::make($request->all(),[
            'nombre' => 'required',
            'apellidos' => 'required',
            'activa' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['status_code'=> 400, 'message'=>'Campos incorrectos']);
        }

        $repostera = $request->id == 0 ? new Repostera(): Repostera::find($request->id);

        $repostera->nombre = $request->nombre;
        $repostera->apellidos = $request->apellidos;
        $repostera->activa = $request->activa;

        $repostera->save();
        return response()->json([
            'status_code' => 200,
            'message' => 'Repostera guardada correctamente'
        ]);
    }

    public function EliminarRepostera(Request $request){
     
        if($request->id > 0){
            $repostera = Repostera::find($request->id);  

            if($repostera != null) {
                $repostera->delete();
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Repostera eliminada correctamente'
                ]);
            }else{
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Repostera id no existe.'
                ]);
            }
            
        }
        else{
            return response()->json([
                'status_code' => 200,
                'message' => 'Repostera id incorrecto.'
            ]);
        }

    }

    public function getRepostera($id){
     
        $repostera = Repostera::find($id);  
        if( $repostera != null ){
            return response()->json([
                'status_code' => 200,
                'message' => 'Repostera por id encontrada',
                'data' => $repostera
            ]);
        }else{
            return response()->json([
                'status_code' => 200,
                'message' => 'Repostera no encontrada',
                'data' => $repostera
            ]);
        }       
    }

    public function getReposteras($descripcion){
        
        $filter = explode('-',$descripcion)[1];
        
        if( $filter == ''){
            $repostera = Repostera::all();  
        }else{
            $repostera = Repostera::where('nombre','like','%'. $filter .'%')->get();            
        }

        if( $repostera != null ){
            return response()->json([
                'status_code' => 200,
                'message' => 'Repostera por id encontrada',
                'data' => $repostera
            ]);
        }else{
            return response()->json([
                'status_code' => 200,
                'message' => 'Repostera no encontrada',
                'data' => $repostera
            ]);
        }
      
    }
}
