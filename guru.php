<?php namespace App\Controllers;

use App\Models\MateriModel;

class Guru extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'guru') {
            return redirect()->to('/login');
        }

        return view('guru/index');
    }

    public function formUploadMateri()
    {
        return view('guru/upload_materi');
    }

   public function uploadMateri()
{
    $file = $this->request->getFile('file');
    $judul = $this->request->getPost('judul');

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $newName = $file->getRandomName();
        $file->move('writable/uploads/materi/', $newName);

        // ✅ Inisialisasi model dulu
        $materiModel = new \App\Models\MateriModel();

        $materiModel->insert([
            'judul'     => $judul,
            'file'      => $newName,
            'guru_id'   => session()->get('user_id'), // Pastikan key ini sesuai di session
            'created_at'=> date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/guru/materi')->with('success', 'Materi berhasil diupload!');
    }

    return redirect()->back()->with('error', 'Gagal mengupload materi.');
}


    public function listMateri()
    {
        $materiModel = new MateriModel();
        $data['materi'] = $materiModel->where('guru_id', session()->get('user_id'))->findAll();

        return view('guru/list_materi', $data);
    }
    public function daftarSiswa()
    {
        // Misalnya semua user dengan role 'user' dianggap siswa
        $userModel = new \App\Models\UserModel();
        $data['siswa'] = $userModel->where('role', 'user')->findAll();

        return view('guru/daftar_siswa', $data);
    }

    public function daftarTugas()
    {
        $tugasModel = new \App\Models\TugasModel();
        $data['tugas'] = $tugasModel->orderBy('created_at', 'DESC')->findAll();

        return view('guru/daftar_tugas', $data);
    }
    // Guru.php
    public function tugasSiswa()
    {
        $tugasModel = new \App\Models\TugasModel();
        $userModel = new \App\Models\UserModel();

        $tugasList = $tugasModel->findAll();

        foreach ($tugasList as &$tugas) {
            // Tambahkan nama siswa
            $siswa = $userModel->find($tugas['siswa_id']);
            $tugas['nama_siswa'] = $siswa['username'] ?? 'Tidak diketahui';

            // Pastikan uploaded_at tidak null
            if (!isset($tugas['uploaded_at'])) {
                $tugas['uploaded_at'] = null;
            }
        }

        return view('guru/daftar_tugas', ['tugas' => $tugasList]);
    }




}
