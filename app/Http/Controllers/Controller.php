<?php

namespace App\Http\Controllers;

use App\Models\Prom;
use App\Models\Pasw;
use App\Models\Reco;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function get_promodata(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'pass' => 'required'
        ]);
        if($validator->fails()){
            return response()->json(['status'=>false]);
        }

        DB::beginTransaction();
        try {
            $query1=Pasw::where('password',$this->encryptHash($data->pass,212))->first();
            if(!$query1){
                $query1=Pasw::where('password',$this->encryptHash($data->pass,112))->first();
                if(!$query1){
                    return response()->json(['status'=>false,'pass'=>true]);
                }
            }
            $query=Prom::where('status','y');
            $query->where('kuota','>',0)->orwhere('kuota','!=','unl');
            if($query1->id == 1){
                $query=$query->where('istimewa','y');
            }else{
                $query=$query->where('istimewa','n');
            }
            $query=$query->inRandomOrder()->first();
            // return $query;
            if(!$query){
                return response()->json(['status'=>false]);
            }
            if($query->jenis == 'n'){
                return response()->json(['status'=>false]);
            }
            Reco::create([
                'proms_id' => $query->id
            ]);
            $promo=$query->promo;
            if($query->kuota == 0){
                return response()->json(['status'=>false]);
            }
            if($query->kuota != 'unl'){
                $query->update(['kuota' => (int)$query->kuota - 1]);
            }

            DB::commit();
            return response()->json(['status'=>true,'message'=>$promo]);
                
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false]);
        }
    }

    public function add_istimewa($data)
    {
        $cek1=Pasw::find(2);
        if($cek1){
            if($cek1->password_text == $data){
                return 'pass duplikat';
            }
        }
        $cek=Pasw::find(1);
        if($cek){
            $cek->update([
                'password_text' => $data,
                'password' => $this->encryptHash($data,212)
            ]);
        }else{
            Pasw::create([
                'id' => 1,
                'password_text' => $data,
                'password' => $this->encryptHash($data,212),
                'key' => 212
            ]);
        }
        return 'sukses';
    }

    public function add_biasa($data)
    {
        $cek1=Pasw::find(1);
        if($cek1){
            if($cek1->password_text == $data){
                return 'pass duplikat';
            }
        }
        $cek=Pasw::find(2);
        if($cek){
            $cek->update([
                'password_text' => $data,
                'password' => $this->encryptHash($data,112)
            ]);
        }else{
            Pasw::create([
                'id' => 2,
                'password_text' => $data,
                'password' => $this->encryptHash($data,112),
                'key' => 112
            ]);
        }
        return 'sukses';
    }

    public function add_promo(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'promo' => 'required',
            'jenis' => 'required',
            'jenis_promo' => 'required',
            'kuota' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status'=>false]);
        }
        $promo=json_decode($data->osa,true);
        if($data->jenis == 'biasa'){
            Prom::create([
                'promo' => $data->promo,
                'jenis' => $data->jenis_promo,
                'kuota' => $data->kuota,
            ]);
        }else if($data->jenis == 'istimewa'){
            Prom::create([
                'promo' => $data->promo,
                'istimewa' => 'y',
                'jenis' => $data->jenis_promo,
                'kuota' => $data->kuota,
            ]);
        }else{
            return 'salah jenis';
        }
        return 'sukses';
    }

    public function edit_promo(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'promo' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status'=>false]);
        }
        $promo=json_decode($data->promo,true);
        foreach ($promo as $key) {
            // return $key['id'];
            Prom::find($key['id'])->update($key);
        }
        return 'sukses';
    }

    public function get_promo()
    {
        return Prom::all();
    }

    public function delete_promo($id)
    {
        $cek=Prom::find($id);
        if($cek){
            $cek->delete();
            return 'sukses';
        }else{
            return 'gagal';
        }
    }

    public function get_pass()
    {
        return Pasw::all();
    }

    public function encryptHash($pass, $key)
    {
        $hashPass = hash('sha256', $pass);
        $encryptPass = hash('sha256', $key . $hashPass);
        return $encryptPass;
    }
}
