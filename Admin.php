<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $data['users'] = $userModel->findAll(); // tampilkan semua user

        return view('admin/index', $data);
    }


    public function createUser()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();

            $data = [
                'username' => $this->request->getPost('username'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'     => $this->request->getPost('role'),
                'email'    => $this->request->getPost('email'),
            ];

            dd($data);
            if (!$validation->setRules([
                'username' => 'required|is_unique[users.username]',
                'password' => 'required|min_length[4]',
                'role'     => 'required|in_list[user,guru,admin]',
                'email'    => 'permit_empty|valid_email'
            ])->run($data)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $userModel = new \App\Models\UserModel();
            $userModel->insert($data);

            return redirect()->to('/admin')->with('success', 'User berhasil ditambahkan.');
        }

        return view('admin/create_user');
    }

}
