<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['form', 'url', 'auth'];
    protected $data = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Load helpers
        helper($this->helpers);

        // Load site settings or common data
        $this->data['site_title'] = 'CodeIgniter Blog';
        $this->data['current_user'] = service('authentication')->getUser();
        
        // Load categories for navigation
        $categoryModel = new \App\Models\CategoryModel();
        $this->data['categories'] = $categoryModel->findAll();
    }

    protected function renderView($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        return view('templates/header', $data)
             . view($view, $data)
             . view('templates/footer', $data);
    }

    protected function jsonResponse($success, $message, $data = [], $code = 200)
    {
        return $this->response->setStatusCode($code)
                            ->setJSON([
                                'success' => $success,
                                'message' => $message,
                                'data' => $data
                            ]);
    }
}