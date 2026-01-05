<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/School.php';

class AdminController extends Controller {
    
    public function dashboard() {
        checkAuth('admin');
        
        $userModel = new User();
        $schoolModel = new School();
        
        // Stats for cards
        $stats = [
            'semed' => count($userModel->getByRole('semed')),
            'coordinators' => count($userModel->getByRole('coordinator')),
            'professors' => count($userModel->getByRole('professor')),
            'schools' => count($schoolModel->all())
        ];
        
        // Only SEMED users needed for main dashboard now
        $semedUsers = $userModel->getByRole('semed');
        $schools = $schoolModel->getAvailableSchools(); // Only show unassigned schools for new registration
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'semedUsers' => $semedUsers,
            'schools' => $schools
        ]);
    }

    public function schools() {
        checkAuth('admin');
        $schoolModel = new School();
        $schools = $schoolModel->all();
        $this->view('admin/schools', ['schools' => $schools]);
    }

    public function coordinators() {
        checkAuth('admin');
        $userModel = new User();
        $coordinators = $userModel->getByRole('coordinator');
        $this->view('admin/coordinators', ['coordinators' => $coordinators]);
    }

    public function professors() {
        checkAuth('admin');
        $userModel = new User();
        $professors = $userModel->getByRole('professor');
        $this->view('admin/professors', ['professors' => $professors]);
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
            
            // Extract schools
            $schools = $data['schools'] ?? [];
            unset($data['schools']);

            // Create user (returns PDOStatement, need ID. But model returns bool/stmt. 
            // Standard PDO insert doesn't return ID easily unless we use lastInsertId on connection.
            // Assuming User model extends Model which has access to db.
            // Let's modify User::create to return ID or handle it here.
            // Given current Model::create usually returns bool/stmt, we might need to find by email to get ID or fix Model.
            // Checking User::create... it returns $stmt.
            $userModel->create($data);
            
            // Hacky way to get ID if create doesn't return it: fetch by email
            $newUser = $userModel->findByEmail($data['email']);
            if ($newUser && !empty($schools)) {
                $userModel->assignSchools($newUser['id'], $schools);
            }

            $_SESSION['success'] = "Usuário criado com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function editUser() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('admin/dashboard');
        
        $userModel = new User();
        $user = $userModel->findById($id);
        if (!$user) redirect('admin/dashboard');
        
        $schoolModel = new School();
        // Get all schools available for assignment + schools ALREADY assigned to this user
        $availableSchools = $schoolModel->getAvailableSchools($id);
        
        // Get currently assigned school IDs for pre-selection
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($id);
        
        $this->view('admin/user_edit', [
            'user' => $user,
            'schools' => $availableSchools,
            'assignedSchoolIds' => $assignedSchoolIds
        ]);
    }
    
    public function updateUser() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $id = $_POST['id'];
            $data = $_POST;
            
            // Schools
            $schools = $data['schools'] ?? [];
            unset($data['schools']);

            // Do not update password here unless specific route/logic, mostly just profile info
            unset($data['password']); 
            unset($data['id']);
            
            $userModel->update($id, $data);
            
            // Always update assignments if it's a SEMED user (or others if we expand)
            // Ideally check role, but assigning empty array clears it which is fine.
            $userModel->assignSchools($id, $schools);

            $_SESSION['success'] = "Usuário atualizado com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function deleteUser() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        $redirect = 'admin/dashboard';
        
        if ($id) {
            $userModel = new User();
            $user = $userModel->findById($id);
            if ($user) {
                if ($user['role'] == 'coordinator') $redirect = 'admin/coordinators';
                elseif ($user['role'] == 'professor') $redirect = 'admin/professors';
                
                $userModel->delete($id);
                $_SESSION['success'] = "Usuário excluído com sucesso!";
            }
        }
        redirect($redirect);
    }
    
    public function resetPassword() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        $redirect = 'admin/dashboard';
        
        if ($id) {
            $userModel = new User();
            $user = $userModel->findById($id);
            if ($user) {
                if ($user['role'] == 'coordinator') $redirect = 'admin/coordinators';
                elseif ($user['role'] == 'professor') $redirect = 'admin/professors';
                
                $userModel->update($id, ['password' => password_hash('123456', PASSWORD_DEFAULT)]);
                $_SESSION['success'] = "Senha resetada para '123456' com sucesso!";
            }
        }
        redirect($redirect);
    }
    
    // --- School Management (mirrored capability) ---
    // --- School Management (mirrored capability) ---
    public function storeSchool() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $schoolModel = new School();
            $schoolModel->create($_POST);
            $_SESSION['success'] = "Escola criada com sucesso!";
        }
        redirect('admin/schools');
    }

    public function editSchool() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('admin/schools');
        }
        
        $schoolModel = new School();
        $school = $schoolModel->findById($id);
        
        if (!$school) {
            $_SESSION['error'] = "Escola não encontrada.";
            redirect('admin/schools');
        }
        
        $this->view('admin/school_edit', ['school' => $school]);
    }

    public function updateSchool() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $schoolModel = new School();
            $schoolModel->update($id, $_POST);
            $_SESSION['success'] = "Escola atualizada com sucesso!";
        }
        redirect('admin/schools');
    }
    
    public function deleteSchool() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $schoolModel = new School();
            $schoolModel->delete($id);
             $_SESSION['success'] = "Escola excluída com sucesso!";
        }
        redirect('admin/schools');
    }

    public function reports() {
        checkAuth('admin');
        
        $type = $_GET['type'] ?? 'general';
        $id = $_GET['id'] ?? null;
        
        $userModel = new User();
        $schoolModel = new School();
        
        $reportData = [];
        $title = "Relatórios de Gestão";
        
        if ($type === 'school' && $id) {
            $school = $schoolModel->findById($id);
            if ($school) {
                $title = "Relatório: " . $school['name'];
                $reportData['school'] = $school;
                $reportData['semed_users'] = $schoolModel->getSemedUsers($id);
                $reportData['coordinators'] = $schoolModel->getCoordinators($id);
                $reportData['professors'] = $schoolModel->getProfessors($id);
            }
        } elseif ($type === 'semed_user' && $id) {
            $user = $userModel->findById($id);
            if ($user) {
                $title = "Portfólio: " . $user['name'];
                $reportData['user'] = $user;
                $reportData['schools'] = $userModel->getManagedSchools($id);
            }
        } elseif ($type === 'general') {
            // General Lists
            $reportData['schools'] = $schoolModel->all(); 
            foreach ($reportData['schools'] as &$s) {
                $s['managers'] = $schoolModel->getSemedUsers($s['id']);
            }
            
            $reportData['semed_users'] = $userModel->getByRole('semed');
            foreach ($reportData['semed_users'] as &$u) {
                $u['school_count'] = count($userModel->getAssignedSchoolIds($u['id']));
            }
        }
        
        $allSchools = $schoolModel->all();
        $allSemedUsers = $userModel->getByRole('semed');
        
        $this->view('admin/reports', [
            'type' => $type,
            'reportData' => $reportData,
            'title' => $title,
            'allSchools' => $allSchools,
            'allSemedUsers' => $allSemedUsers,
            'selectedId' => $id
        ]);
    }
}
