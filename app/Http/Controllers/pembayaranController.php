<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Spp;
use Illuminate\Http\Request;

class pembayaranController extends Controller
{
    public function index(Request $request)
    {
        $keyword = null;

        if($request->cari != null){
            $siswa = Siswa::with('user')->whereRelation('user', 'name','like', "%$request->cari%")->orderBy('kelas_id', 'ASC')->get();
            $keyword =$request->cari;

        }else{

            $siswa = Siswa::orderBy('kelas_id', 'ASC')->get();
        }

        return view('pembayaran.index', compact('siswa', 'keyword'));
    }

    public function transaksi($id)
    {
        $dibayar = 0;
        $tagihan = 0;
        $pembayaranSpp = [];

       try{
        $siswa = Siswa::findOrFail($id);
        $pembayaran = Pembayaran::where('siswa_id' ,$id)->get();
        $spp = Spp::get();

        foreach ($pembayaran as $data)
        {
            $dibayar += $data->jumlah_bayar;
        }

        foreach ($spp as $data)
        {
            $tagihan += $data->nominal;

           $total = Pembayaran::where('spp_id', $data->id)->where('siswa_id', $id)->sum('jumlah_bayar');
           $pembayaranSpp[] = $total;
        }

        $total = [
            'total_dibayar' => $dibayar,
            'total_belumdibayar' => $tagihan - $dibayar
        ];

       }catch(\Exception $e){
        return redirect('pembayaran')->with('gagal', 'pembayaran gagal');
       }

       return view('pembayaran.transaksi', compact('spp', 'pembayaran', 'siswa', 'total', 'pembayaranSpp'));
    }

    public function simpan(Request $request)
    {

        try {
          Pembayaran::create([
                'user_id' => auth()->user()->id,
                'siswa_id' => $request->siswa_id,
                'spp_id' => $request->spp_id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar' => $request->jumlah_bayar,

            ]);


            return redirect()->back()->with('sukses', "Transaksi berhasil" );


        }catch(\Exception $e)
        {

            return redirect()->back()->with('gagal', "Transaksi Gagal" .$e->getMessage() );
        }
    }
    // /////////////////////////edit

    public function edit($id)
    {
        try{
            $pembayaran = Pembayaran::findOrFail($id);
            $spp = Spp::all();
            $siswa = Siswa::all();
            $user = User::all();


            return view('pembayaran.edit', compact('pembayaran', 'spp', 'siswa', 'user'));

            return redirect('pembayaran')->with('sukses' ,'data berhasil');
        }catch(\Exception $e){
            return redirect('pembayaran')->with('gagal' ,'data gagal');
        }
    }

    public function update(Request $request)
    {
        // dd($request);

        try{
            Pembayaran::where('id', $request->id)->update([
                'jumlah_bayar' => $request->jumlah_bayar,
            ]);

            return redirect('pembayaran')->with('sukses' ,'data berhasil');
        }catch(\Exception $e){
            return redirect('pembayaran')->with('gagal' ,'data gagal');
        }

    }

    public function hapus($id)
    {
        try{
            Pembayaran::findOrFail($id);
            Pembayaran::destroy($id);

            return redirect("pembayaran")->with('sukses' ,'data telah di hapus');
        }catch(\Exception $e){
            return redirect("pembayaran")->with('gagal' ,'data gagal di hapus');
        }
    }

   


/////////////////////////  CETAK HISTORY ALL
    public function cetak() {
        $pembayaran = pembayaran::all();

        return view('pembayaran.cetak', compact('pembayaran'));
}
// //////////////////cetak history siswa
    public function cetakid ($id) {
        $siswa = Siswa::findOrFail($id);

            $pembayaran = pembayaran::where('siswa_id', $id)->get();
        return view('pembayaran.cetakid', compact('pembayaran', 'siswa')); 
    }
     //////////history
     public function history(){
        $user_id = auth()->user()->id;
        $siswa = Siswa::where('user_id', $user_id)->first();
        $pembayaran = Pembayaran::where('siswa_id', $siswa->id)->get();
        return view('history',compact('siswa', 'pembayaran'));
     }
}


