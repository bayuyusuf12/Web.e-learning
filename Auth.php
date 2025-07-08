<?php namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $userModel = new \App\Models\UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'isLoggedIn' => true,
                'username'   => $user['username'],
                'role'       => $user['role'],
                'user_id' => $user['id']
            ]);

            if ($user['role'] === 'admin') {
                return redirect()->to('/admin');
            } elseif ($user['role'] === 'guru') {
                return redirect()->to('/guru');
            } else {
                return redirect()->to('/user');
            }

        }

        return redirect()->back()->with('error', 'Login gagal!');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
