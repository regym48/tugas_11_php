<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\tbl_karyawan;
class Karyawan extends Controller
{
    public function getData(){
        $data = DB::table('tbl_karyawan')->get();
        if(count($data) > 0){
            $res['message'] = "Berhasil";
            $res['value'] = $data;
            return response($res);
        }else{
            echo csrf_token();
            $res['message'] = "Kosong";
            return response($res);
        }
    }

    public function store(Request $request){
        $this->validate($request, [
            'file' => 'required|max:2048'
        ]);
        //menyimpan data ke variabel file
        $file = $request->file('file');
        $nama_file = time()."_".$file -> getClientOriginalName();
        //isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'data_file';
        if($file->move($tujuan_upload,$nama_file)){
            $data = tbl_karyawan::create([
            'nama' => $request ->nama,
            'jabatan' => $request ->jabatan,
            'umur' => $request ->umur,
            'alamat' => $request ->alamat,
            'foto' => $nama_file
            ]);
            $res['message'] = "Berhasil";
            $res['value'] = $data;
            return response($res);
        }
    }
    public function update (Request $request){
        if(!empty($request->file)){
            $this->validate($request, [
                'file' => 'required|max:2048'
            ]);
            //menyimpan data ke variabel file
            $file = $request->file('file');
            $nama_file = time()."_".$file -> getClientOriginalName();
            //isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'data_file';
            $file->move($tujuan_upload,$nama_file);
            $data = DB::table('tbl_karyawan')->where('id',$request->id)->get();
            foreach ($data as $karyawan){
                @unlink(public_path('data_file/'.$karyawan->foto));
                $ket = DB::table('tbl_karyawan')->where('id',$request->id)->update([
                    'nama' => $request ->nama,
                    'jabatan' => $request ->jabatan,
                    'umur' => $request ->umur,
                    'alamat' => $request ->alamat,
                    'foto' => $nama_file
                ]);
                $res['message'] = "Berhasil";
                $res['value'] = $ket;
                return response($res);
            }
        }else{
            $data = DB::table('tbl_karyawan')->where('id',$request->id)->get();
            foreach ($data as $karyawan){
                $ket = DB::table('tbl_karyawan')->where('id',$request->id)->update([
                    'nama' => $request ->nama,
                    'jabatan' => $request ->jabatan,
                    'umur' => $request ->umur,
                    'alamat' => $request ->alamat
                ]);
                $res['message'] = "Berhasil";
                $res['value'] = $ket;
                return response($res);
            }
        }
    }
    public function hapus($id){
        $data = DB::table('tbl_karyawan')->where('id', $id)->get();
        foreach ($data as $karyawan){
            if(file_exists(public_path('data_file/'.$karyawan->foto))){
                @unlink(public_path('data_file/'.$karyawan->foto));
                DB::table('tbl_karyawan')->where('id',$id)->delete();
                $res['message'] = "Berhasil";
                return response($res);
            }else{
                $res['message'] = "Kosong";
                return response($res);
            }
        }
    }
}
