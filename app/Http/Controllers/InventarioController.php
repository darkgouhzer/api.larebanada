<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\Entradasdetalle;
use App\Models\Inventario;
use App\Models\Salida;
use App\Models\Salidasdetalle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InventarioController extends Controller
{
    public function Entrada(Request $request){
        $validator = Validator::make($request->all(),[
            'cantidad' => 'required|numeric',
            'idproductos' => 'required|numeric',
            'idsucursales' => 'required|numeric',
            'idrepostera' => 'required|numeric',
            'users_id' => 'required|numeric',
            'catmotivoentradas_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['status_code'=> 400, 'message'=>'Campos incorrectos','request'=>$request->idrepostera]);
        }
        try{
            DB::beginTransaction();
            $entrada = new Entrada();
     
            $entrada->idsucursales= $request->idsucursales;
            $entrada->idrepostera= $request->idrepostera;
            $entrada->users_id= $request->users_id;
            $entrada->catmotivoentradas_id= $request->catmotivoentradas_id;
            
            $entrada->save();           

            $entradasdetalle = new Entradasdetalle();
            $entradasdetalle->entradas_id = $entrada->id;
            $entradasdetalle->productos_id = $request->idproductos;
            $entradasdetalle->cantidad = $request->cantidad;
            $entradasdetalle->save();

            $counter = Inventario::where('idproductos',$entradasdetalle->productos_id)->get();
            if(count($counter) >0){
                $inventario = Inventario::find($counter[0]->id);
                $inventario->cantidad = $inventario->cantidad + $entradasdetalle->cantidad;
                $inventario->save();
            }else{
                $inventario = new Inventario();
                $inventario->cantidad = $entradasdetalle->cantidad;
                $inventario->idproductos = $entradasdetalle->productos_id;
                $inventario->save();
            }
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Entrada registrada correctamente'
            ]);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json([
                'status_code' => 400,
                'message' => $ex
            ]);
        }
    }

    public function Salida(Request $request){
        $validator = Validator::make($request->all(),[
            'cantidad' => 'required|numeric',
            'idproductos' => 'required|numeric',
            'idsucursales' => 'required|numeric',
            'users_id' => 'required|numeric',
            'catmotivosalidas_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['status_code'=> 400, 'message'=>'Campos incorrectos']);
        }
        if($request->cantidad <= 0){
            return response()->json(['status_code'=> 400, 'message'=>'Cantidad incorrecta']);
        }
        try{
            DB::beginTransaction();

            $salida = new Salida();
            $salida->idsucursales= $request->idsucursales;
            $salida->users_id= $request->users_id;
            $salida->catmotivosalidas_id=$request->catmotivosalidas_id;
            $salida->save();
    
            $salidasdetalle = new Salidasdetalle();
            $salidasdetalle->salidas_id = $salida->id;
            $salidasdetalle->productos_id = $request->idproductos;
            $salidasdetalle->cantidad = $request->cantidad;
            $salidasdetalle->save();

            $counter = Inventario::where('idproductos',$salidasdetalle->productos_id)->get();
            if(count($counter) >0){
                $inventario = Inventario::find($counter[0]->id);
                if($inventario->cantidad >= $salidasdetalle->cantidad){
                    $inventario->cantidad = $inventario->cantidad - $salidasdetalle->cantidad;
                    $inventario->save();
                    DB::commit();
                }else{
                    DB::rollBack();
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Inventario insuficiente'
                    ]);
                }
              
            }else{
                DB::rollBack();
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Inventario insuficiente'
                ]);
            }
           
            return response()->json([
                'status_code' => 200,
                'message' => 'Salida registrada correctamente'
            ]);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json([
                'status_code' => 400,
                'message' => $ex
            ]);
        }
    }

    public function getAllEntradas(Request $request){
        
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');

        $result = DB::select('CALL sp_obtenerentradasinventario(?,?)',[$desde,$hasta]);
        return response()->json([
            'status_code' => 200,
            'message' => 'Salida registrada correctamente',
            'data' => $result

        ]);  

    }
}
