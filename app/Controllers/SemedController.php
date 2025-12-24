<?php

require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/School.php';

class SemedController extends Controller {
    public function dashboard() {
        checkAuth('semed');
        $user = auth();
        
        $docModel = new Document();
        $stats = $docModel->getGlobalStats();

        require_once __DIR__ . '/../Models/RankingModel.php';
        $rankingModel = new RankingModel();
        
        $filter = $_GET['filter'] ?? 'annual';
        $rankSchools = $rankingModel->getSchoolRanking($filter);
        $rankProfessors = $rankingModel->getProfessorRanking($filter);
        $rankCoordinators = $rankingModel->getCoordinatorRanking($filter);
        
        $chartData = $docModel->getDocumentStatsBySchool();
        $monthlyData = $docModel->getMonthlyStats();
        
        $this->view('dashboard/semed', [
            'user' => $user,
            'stats' => $stats,
            'rankSchools' => $rankSchools,
            'rankProfessors' => $rankProfessors,
            'rankCoordinators' => $rankCoordinators,
            'chartData' => $chartData,
            'monthlyData' => $monthlyData,
            'filter' => $filter
        ]);
    }
    public function schools() {
        checkAuth('semed');
        $schoolModel = new School();
        $schools = $schoolModel->all();
        $this->view('dashboard/semed_schools', ['schools' => $schools]);
    }

    public function storeSchool() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $schoolModel = new School();
            $schoolModel->create($_POST);
            $_SESSION['success'] = "Escola cadastrada com sucesso!";
        }
        redirect('semed/schools');
    }

    public function editSchool() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        $schoolModel = new School();
        $school = $schoolModel->findById($id);
        $this->view('dashboard/semed_school_edit', ['school' => $school]);
    }

    public function updateSchool() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $schoolModel = new School();
            $schoolModel->update($id, $_POST);
            $_SESSION['success'] = "Escola atualizada com sucesso!";
        }
        redirect('semed/schools');
    }

    public function deleteSchool() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $schoolModel = new School();
            // Check if there are users associated with this school
            $userModel = new User();
            $usersInSchool = $userModel->getBySchoolId($id);
            
            if (!empty($usersInSchool)) {
                $_SESSION['error'] = "Não é possível excluir esta escola pois existem usuários vinculados a ela.";
            } else {
                $schoolModel->delete($id);
                $_SESSION['success'] = "Escola excluída com sucesso!";
            }
        }
        redirect('semed/schools');
    }

    // --- COORDINATOR MANAGEMENT ---
    public function coordinators() {
        checkAuth('semed');
        $userModel = new User();
        $coordinators = $userModel->getByRole('coordinator');
        $schoolModel = new School();
        $schools = $schoolModel->all();
        $this->view('dashboard/semed_coordinators', [
            'coordinators' => $coordinators,
            'schools' => $schools
        ]);
    }

    public function storeCoordinator() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = $_POST;
            $data['role'] = 'coordinator';
            $data['password'] = password_hash('123456', PASSWORD_DEFAULT); // Default password
            $userModel->create($data);
            $_SESSION['success'] = "Coordenador cadastrado com sucesso! Senha padrão: 123456";
        }
        redirect('semed/coordinators');
    }

    public function editCoordinator() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        $userModel = new User();
        $coordinator = $userModel->findById($id);
        $schoolModel = new School();
        $schools = $schoolModel->all();
        $this->view('dashboard/semed_coordinator_edit', [
            'coordinator' => $coordinator,
            'schools' => $schools
        ]);
    }

    public function updateCoordinator() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $userModel = new User();
            $userModel->update($id, $_POST);
            $_SESSION['success'] = "Coordenador atualizado com sucesso!";
        }
        redirect('semed/coordinators');
    }

    public function resetPassword() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $userModel = new User();
            $userModel->update($id, ['password' => password_hash('123456', PASSWORD_DEFAULT)]);
            $_SESSION['success'] = "Senha redefinida para '123456' com sucesso!";
        }
        $role = $_GET['role'] ?? 'coordinator';
        redirect('semed/' . ($role == 'coordinator' ? 'coordinators' : 'professors'));
    }

    public function plannings() {
        checkAuth('semed');
        $docModel = new Document();
        $schoolModel = new School();
        
        $filters = [
            'school_id' => $_GET['school_id'] ?? null,
            'bimester' => $_GET['bimester'] ?? null,
            'status' => $_GET['status'] ?? null,
            'professor_id' => $_GET['professor_id'] ?? null
        ];

        
        $documents = $docModel->getAllWithFilters($filters);
        $schools = $schoolModel->all();
        
        $professors = [];
        $userModel = new User();
        
        if (!empty($filters['school_id'])) {
             $professors = $userModel->getBySchoolId($filters['school_id']);
        } else {
             // Fetch all professors for global filter
             $professors = $userModel->getByRole('professor');
        }

        // Calculate statistics for the chart
        $statusCounts = [
            'aprovado' => 0,
            'ajustado' => 0,
            'rejeitado' => 0,
            'enviado' => 0, // Aguardando/Enviado
            'total' => 0
        ];

        foreach ($documents as $doc) {
            $status = $doc['status'];
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            } else {
                // Determine if it fits in fallback categories if exact status match fails
                // Assuming standard statuses are used, but handling weird cases just in case
                if ($status == 'entregue') $statusCounts['enviado']++;
                else $statusCounts['enviado']++; // Fallback for pending
            }
            $statusCounts['total']++;
        }
        
        $this->view('dashboard/semed_plannings', [
            'documents' => $documents,
            'schools' => $schools,
            'professors' => $professors,
            'filters' => $filters,
            'statusCounts' => $statusCounts
        ]);
    }

    public function reports() {
        checkAuth('semed');
        $type = $_GET['type'] ?? 'submissions';
        $schoolId = $_GET['school_id'] ?? null;
        $professorId = $_GET['professor_id'] ?? null;
        $period = $_GET['period'] ?? 'annual';
        
        $docModel = new Document();
        $schoolModel = new School();
        $userModel = new User();

        $schools = $schoolModel->all();
        $professors = [];
        
        $data = [];
        
        if ($schoolId) {
            $professors = $userModel->getBySchoolId($schoolId);
        }

        if ($professorId) {
            // Detailed professor report
            $data = $docModel->getProfessorStats($professorId, $period);
        } elseif ($type === 'pendencies') {
            // Get professors with pending/delayed documents
            $data = $docModel->getGlobalPendencies($schoolId);
        } elseif ($type === 'punctuality') {
            // Get averaging scores per school
            $data = $docModel->getSchoolPunctuality();
        } else {
            // Default: Submissions summary
            $data = $docModel->getSubmissionsReport($schoolId);
        }
        
        $this->view('dashboard/semed_reports', [
            'type' => $type,
            'data' => $data,
            'schools' => $schools,
            'professors' => $professors,
            'schoolId' => $schoolId,
            'professorId' => $professorId,
            'period' => $period
        ]);
    }

    public function changePassword() {
         checkAuth('semed');
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $newPass = $_POST['password'];
             $user = auth();
             
             require_once __DIR__ . '/../Models/User.php';
             $userModel = new User();
             $userModel->updatePassword($user['id'], password_hash($newPass, PASSWORD_DEFAULT));
             
             $_SESSION['success'] = "Sua senha foi alterada com sucesso!";
             redirect('semed/dashboard');
         }
    }
}
