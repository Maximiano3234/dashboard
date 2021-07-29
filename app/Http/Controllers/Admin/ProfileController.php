<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\User;


class ProfileController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index()    {
        $loggeId = intval( Auth::id() );

        $user = User::find($loggeId);

        if($user) {
            return view('admin.profile.index', [
                'user' => $user
            ]);
        }
        
        return redirect()->route('admin');
    }

    public function save(Request $request)
    {
        $loggeId = intval( Auth::id() );
        $user = User::find($loggeId);
        if($user) { 
            // metodo para verificar e depois alterar usuario
            $data = $request->only([
                'name',
                'email',
                'password',
                'password_confirmation'
            ]);
            // validação
            $validator = Validator::make([
                'name' => $data['name'],
                'email' => $data['email']
            ], [                            //regras
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'max:100']
            ]);

            // 1. Alteração do nome
            $user->name = $data['name'];

            // 2. Alteração do email
            // 2.1 Primeiro verificamos se o email foi alterado
            if($user->email != $data['email']) {
                // 2.2 Verificamos se o novo email já existe
                $hasEmail = User::where('email', $data['email'])->get();
                // 2.3 Se não existir, nós alteramos.
                if(count($hasEmail) === 0) {
                    $user->email = $data['email'];
                } else {
                    $validator->errors()->add('email', __('validation.unique', [
                        'atribute' => 'email'
                    ]));                    
                }
                
            }
            
            // 3. Alteração da senha
            // 3.1 Verifica se o usuário digitou senha
            if(!empty($data['password'])) {
                if(strlen($data['password']) >=4) { 
                // 3.2 Verifica se a confirmação esta ok
                if($data['password'] === $data['password_confirmation']) {
                        // 3.3 Altera a senha
                        $user->password = Hash::make($data['password']);
                        
                } else {
                    $validator->errors()->add('password', __('validation.confirmed', [
                        'atribute' => 'password'
                    ]));               
                }
            } else {
                $validator->errors()->add('password', __('validation.min.string', [
                    'atribute' => 'password',
                    'min' => 4
                ]));               
            }
        }
        if(count( $validator->errors() ) > 0) {
            return redirect()->route('profile', [
                'user' => $loggeId
            ])->withErrors($validator);
        }
            

           $user->save();

           return redirect()->route('profile')
            ->with('warning', 'Usuário alterado com sucesso !');

        }

        return redirect()->route('profile');
    }
}
