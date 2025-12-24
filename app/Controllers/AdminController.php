<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/School.php';

class AdminController extends Controller {
    
    public function dashboard() {
        checkAuth('admin');
        
        $userModel = new User();
        $schoolModel = new School();
        
        $semedUsers = $userModel->getByRole('semed');
        $coordinators = $userModel->getByRole('coordinator');
        $professors = $userModel->getByRole('professor');
        $schools = $schoolModel->all();
        
        // Simple counts
        $stats = [
            'semed' => count($semedUsers),
            'coordinators' => count($coordinators),
            'professors' => count($professors),
            'schools' => count($schools)
        ];
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'semedUsers' => $semedUsers,
            'coordinators' => $coordinators,
            'professors' => $professors,
            'schools' => $schools
        ]);
    }
    
    public function storeUser() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = $_POST;
            
            // Password handling
            if (empty($data['password'])) {
                $data['password'] = '123456'; // Default
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $userModel->create($data);
            $_SESSION['success'] = "Usuário criado com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function updateUser() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $id = $_POST['id'];
            $data = $_POST;
            
            // Do not update password here unless specific route/logic, mostly just profile info
            unset($data['password']); 
            unset($data['id']);
            
            $userModel->update($id, $data);
            $_SESSION['success'] = "Usuário atualizado com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function deleteUser() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $userModel = new User();
            // Prevent deleting self? (Normally yes, but simplistic for MVP)
            $userModel->delete($id);
            $_SESSION['success'] = "Usuário excluído com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function resetPassword() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $userModel = new User();
            $userModel->update($id, ['password' => password_hash('123456', PASSWORD_DEFAULT)]);
            $_SESSION['success'] = "Senha resetada para '123456' com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    // --- School Management (mirrored capability) ---
    public function storeSchool() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $schoolModel = new School();
            $schoolModel->create($_POST);
            $_SESSION['success'] = "Escola criada com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function deleteSchool() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $schoolModel = new School();
            $schoolModel->delete($id);
             $_SESSION['success'] = "Escola excluída com sucesso!";
        }
        redirect('admin/dashboard');
    }
}
