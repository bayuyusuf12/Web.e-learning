<?php namespace App\Controllers;

use App\Models\MateriModel;

class User extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'user') {
            return redirect()->to('/login');
        }

        return view('user/index');
    }

    public function materi()
        {
            $materiModel = new \App\Models\MateriModel();
            $data['materi'] = $materiModel->orderBy('created_at', 'DESC')->findAll();

            return view('user/materi', $data);
        }


    public function tugas()
    {
        return view('user/tugas'); // buat nanti
    }

    public function kumpulTugas()
    {
        $file = $this->request->getFile('file');
        $judul = $this->request->getPost('judul');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('writable/uploads/tugas/', $newName);

            $tugasModel = new \App\Models\TugasModel();
            $tugasModel->insert([
                'judul'       => $judul,
                'file'        => $newName,
                'siswa_id'    => session()->get('user_id'), // Pastikan ini ada di sesi login
                'guru_id'     => null, // karena belum ditentukan
                'uploaded_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/user/tugas')->with('success', 'Tugas berhasil dikumpulkan!');
        }

        return redirect()->back()->with('error', 'Gagal mengupload tugas.');
    }



    public function progress()
    {
        return view('user/progress'); // buat nanti
    }

    

}
