<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Setting;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $settings = [];

        $dbsettings = Setting::get();

        foreach($dbsettings as $dbsetting) {
            $settings[ $dbsetting['name'] ] = $dbsetting['content'];
        }

        return view('admin.settings.index', [
            'settings' => $settings
        ]);
    }

    public function save(Request $request) {
        $data = $request->only([
            'title', 'subtitle', 'email', 'bgcolor', 'textcolor'
        ]);

       // print_r($data); --> ao clicar em salvar mosta uma tela com arrays preencidos com informações do banco de dados

       $validator = $this->validator($data);

       if($validator->fails()){
           // redirecinar
           return redirect()->route('settings')
           ->withErrors($validator);

       }
       // se não retornar nada salvar alteração
       foreach($data as $item => $value) {
        Setting::where('name', $item)->update([
            'content' => $value
        ]);
       }
         return redirect()->route('settings')
         ->with('warning', 'Informações alterada com sucesso !');
    }

    // função de validação acima

    protected function validator($data) {
        return Validator::make($data, [
            'title' => ['string', 'max:100'],
            'subtitle' => ['string', 'max:100'],
            'email' => ['string', 'email'],
            'bgcolor' => ['string', 'regex:/#[A-Z0-9]{6}/i'],
            'textcolor' => ['string', 'regex:/#[A-Z0-9]{6}/i']
        ]);
    }
    
        
    }
