<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $authentication;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authentication = service('authentication');
    }

    public function login()
    {
        $data['title'] = 'Login';
        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $remember = $this->request->getPost('remember');

            // Validation rules
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|min_length[8]'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                if ($this->authentication->login($email, $password)) {
                    session()->setFlashdata('success', 'Welcome back! You have successfully logged in.');
                    
                    // Redirect to intended page or dashboard
                    $redirect = session()->get('redirect_url') ?? is_admin()? 'admin': 'dashboard';
                    session()->remove('redirect_url');
                    
                    return redirect()->to($redirect);
                } else {
                    session()->setFlashdata('error', 'Invalid email or password. Please try again.');
                    return redirect()->back()->withInput();
                }
            }
        }

        return $this->renderView('auth/login', $data);
    }

    public function register()
    {
        $data['title'] = 'Register';

        if ($this->request->getMethod() === 'POST') {
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'password_confirm' => $this->request->getPost('password_confirm')
            ];

            // Validation rules
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                // Remove password_confirm before saving
                unset($userData['password_confirm']);
                
                if ($this->authentication->register($userData)) {
                    session()->setFlashdata('success', 'Registration successful! Please login to your account.');
                    return redirect()->to('login');
                } else {
                    session()->setFlashdata('error', 'Registration failed. Please try again.');
                    return redirect()->back()->withInput();
                }
            }
        }

        return $this->renderView('auth/register', $data);
    }

    public function logout()
    {
        $this->authentication->logout();
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to('/');
    }

    public function forgotPassword()
    {
        $data['title'] = 'Forgot Password';

        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');

            $rules = ['email' => 'required|valid_email'];
            
            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                $user = $this->userModel->getUserByEmail($email);
                
                if ($user) {
                    // In a real application, you would send a password reset email here
                    session()->setFlashdata('success', 'If that email exists in our system, we have sent a password reset link.');
                } else {
                    // Don't reveal if email exists or not for security
                    session()->setFlashdata('success', 'If that email exists in our system, we have sent a password reset link.');
                }
                
                return redirect()->to('login');
            }
        }

        return $this->renderView('auth/forgot_password', $data);
    }
}