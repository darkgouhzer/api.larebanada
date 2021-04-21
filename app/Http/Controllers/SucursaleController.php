<?php

namespace App\Http\Controllers;

use App\Models\Sucursale;
use Illuminate\Http\Request;

class SucursaleController extends Controller
{
    //
    public function getSucursales(){
        $sucursales = Sucursale::all();

        return $sucursales;
    }

}
