<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Unidadesmedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    //

    public function getUnidadesMedida(){
        $unidadesmedida = Unidadesmedida::all();

        return response()->json([
            'status_code' => 200,
            'message' => 'Unidades de medidas',
            'data' => $unidadesmedida
        ]);
    }

    public function AltaProducto(Request $request){
        $validator = Validator::make($request->all(),[
            'nombre' => 'required',
            'precio' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'valormedida' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'idunidadesmedida' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['status_code'=> 400, 'message'=>'Campos incorrectos']);
        }

        $producto = $request->id == 0 ? new Producto(): (Producto::find($request->id)!= null ?Producto::find($request->id): new Producto());

        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio;
        $producto->valormedida = $request->valormedida;
        $producto->idunidadesmedida= $request->idunidadesmedida;

        $producto->save();

        // if($request->id == 0 ){
        //     $inventario = new Inventario();
        //     $inventario->idproductos= $producto->id;
        //     $inventario->save();
        // }
        return response()->json([
            'status_code' => 200,
            'message' => 'Producto guardado correctamente',
            'data' => $producto
        ]);
    }

    public function EliminarProducto(Request $request){
     
        if($request->id > 0){
            $producto = Producto::find($request->id);  

            if($producto != null) {
                $producto->delete();
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Producto eliminado correctamente'
                ]);
            }else{
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Producto id no existe.'
                ]);
            }
            
        }
        else{
            return response()->json([
                'status_code' => 200,
                'message' => 'Producto id incorrecto.'
            ]);
        }

    }

    public function getProducto($id){
     
        $producto = Producto::find($id);  
        if( $producto != null ){
            return response()->json([
                'status_code' => 200,
                'message' => 'Producto por id encontrado',
                'data' => $producto
            ]);
        }else{
            return response()->json([
                'status_code' => 200,
                'message' => 'Producto no encontrado',
                'data' => $producto
            ]);
        }       
    }

    public function getProductos($descripcion){
        
        $filter = explode('-',$descripcion)[1];
        
        // if( $filter == ''){
        //     $producto = Producto::all();  
        // }else{
        //     $producto = Producto::where('nombre','like','%'. $filter .'%')->get();            
        // }
        $queryResult = DB::select('call sp_obtenerproductos("'.$filter.'")');
        $result = collect($queryResult);
        if( $result != null ){
            return response()->json([
                'status_code' => 200,
                'message' => 'Producto por id encontrado',
                'data' => $result
            ]);
        }else{
            return response()->json([
                'status_code' => 200,
                'message' => 'Producto no encontrado',
                'data' => $result
            ]);
        }
      
    }    
}
