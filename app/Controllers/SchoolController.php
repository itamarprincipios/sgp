<?php

require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/User.php';

require_once __DIR__ . '/../Models/Planning.php';
require_once __DIR__ . '/../Models/School.php';
require_once __DIR__ . '/../Models/ClassModel.php';

class SchoolController extends Controller {
    public function dashboard() {
        checkAuth('coordinator');
        $user = auth();
        $schoolId = $user['school_id'];
        
        // Data for Tabs
        // Tab 1: Plannings (Created by Coordinator)
        $planningModel = new Planning();
        $plannings = $planningModel->getBySchoolId($schoolId);
        $pendingSubmissions = $planningModel->getPendingSubmissions($schoolId);

        // School Info
        $schoolModel = new School();
        $school = $schoolModel->findById($schoolId);

        // Tab 2: Recent Uploads (All docs)
        $docModel = new Document();
        
        $filters = [
            'period_id' => $_GET['period_id'] ?? '',
            'professor_id' => $_GET['professor_id'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];
        
        $documents = $docModel->getBySchoolIdWithFilters($schoolId, $filters);
        
        $newUploadsCount = 0;
        $lastViewed = $_SESSION['last_viewed_uploads'] ?? null;
        
        foreach ($documents as $d) {
            if (in_array($d['status'], ['enviado', 'atrasado'])) {
                // If never viewed or submitted after simple timestamp check
                if (!$lastViewed || strtotime($d['submitted_at']) > $lastViewed) {
                    $newUploadsCount++;
                }
            }
        }
        
        // Tab 3: Classes
        $classModel = new ClassModel();
        $classes = $classModel->getBySchoolIdWithProfessor($schoolId);

        // Tab 4: Professors
        $userModel = new User();
        $professors = $userModel->getProfessorsBySchoolWithClass($schoolId);

        $this->view('dashboard/school', [
            'user' => $user,
            'school' => $school,
            'plannings' => $plannings,
            'documents' => $documents,
            'classes' => $classes,
            'professors' => $professors,
            'pendingSubmissions' => $pendingSubmissions,
            'newUploadsCount' => $newUploadsCount,
            'filters' => $filters
        ]);
    }

    public function createPlanning() {
        checkAuth('coordinator');
        $this->view('dashboard/planning_create');
    }

    public function storePlanning() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $start_date = $_POST['start_date']; // Vigencia YYYY-MM-DD
            
            // Deadline: 1 dia antes da vigência, às 23:59:59
            $deadline = date('Y-m-d 23:59:59', strtotime($start_date . ' - 1 day'));
            // Abertura: 7 dias antes da vigência, às 00:00:00
            $opening_date = date('Y-m-d 00:00:00', strtotime($start_date . ' - 7 days'));

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'start_date' => $start_date . ' 00:00:00',
                'end_date' => $_POST['end_date'],
                'deadline' => $deadline,
                'opening_date' => $opening_date,
                'school_id' => auth()['school_id'],
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0
            ];

            $planning = new Planning();
            $planning->create($data);

            redirect('school/dashboard'); // Could add ?tab=planning param to open correct tab
        }
    }

    public function viewPlanning() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard');
        
        $planningModel = new Planning();
        $planning = $planningModel->findById($id);
        $schoolId = auth()['school_id'];
        
        // Security check
        if (!$planning || $planning['school_id'] != $schoolId) redirect('school/dashboard');

        // Get details (pass if it's PE planning or regular)
        $details = $planningModel->getPlanningStats($id, $schoolId, $planning['is_physical_education'] ?? 0);

        // Group by Class
        $groupedData = [];
        foreach ($details as $row) {
            $groupedData[$row['class_name']][] = $row;
        }

        $this->view('dashboard/planning_detail', [
            'planning' => $planning,
            'groupedData' => $groupedData
        ]);
    }

    public function editPlanning() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard');

        $planningModel = new Planning();
        $planning = $planningModel->findById($id);

        if (!$planning || $planning['school_id'] != auth()['school_id']) redirect('school/dashboard');

        $this->view('dashboard/planning_edit', ['planning' => $planning]);
    }

    public function updatePlanning() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $start_date = $_POST['start_date'];
            
            // Deadline: 1 dia antes da vigência, às 23:59:59
            $deadline = date('Y-m-d 23:59:59', strtotime($start_date . ' - 1 day'));
            // Abertura: 7 dias antes da vigência, às 00:00:00
            $opening_date = date('Y-m-d 00:00:00', strtotime($start_date . ' - 7 days'));

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'deadline' => $deadline,
                'opening_date' => $opening_date,
                'start_date' => $start_date . ' 00:00:00',
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0
            ];

            $planningModel = new Planning();
            $planningModel->update($id, $data);

            redirect('school/dashboard');
        }
    }

    public function deletePlanning() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $planningModel = new Planning();
            $planning = $planningModel->findById($id);
            if ($planning && $planning['school_id'] == auth()['school_id']) {
                $planningModel->delete($id);
            }
        }
        redirect('school/dashboard');
    }

    // --- Classes CRUD ---
    public function storeClass() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $schoolId = auth()['school_id'];
            $classModel = new ClassModel();
            $classModel->create($schoolId, $name);
            redirect('school/dashboard');
        }
    }

    public function editClass() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard');

        $classModel = new ClassModel();
        $class = $classModel->findById($id);

        if (!$class || $class['school_id'] != auth()['school_id']) redirect('school/dashboard');

        $this->view('dashboard/class_edit', ['class' => $class]);
    }

    public function updateClass() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];

            $classModel = new ClassModel();
            $class = $classModel->findById($id);
            
            if ($class && $class['school_id'] == auth()['school_id']) {
                $classModel->update($id, $name);
            }

            redirect('school/dashboard');
        }
    }

    public function deleteClass() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $classModel = new ClassModel();
            $class = $classModel->findById($id);
            // Ensuring we compare values correctly
            if ($class && (int)$class['school_id'] === (int)auth()['school_id']) {
                $classModel->delete($id);
            }
        }
        redirect('school/dashboard');
    }

    // --- Professor CRUD ---
    public function storeProfessor() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            // Basic validation skipped for MVP
            $data = [
                'school_id' => auth()['school_id'],
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => password_hash('professor123', PASSWORD_DEFAULT), // Default password fixed
                'whatsapp' => $_POST['whatsapp'],
                'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0
            ];
            $userModel->createProfessor($data);
            redirect('school/dashboard'); // OR ?tab=professors
        }
    }

    public function editProfessor() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        if(!$id) redirect('school/dashboard');

        $userModel = new User();
        $professor = $userModel->findById($id);
        
        $classModel = new ClassModel();
        $classes = $classModel->getBySchoolId(auth()['school_id']);

        $this->view('dashboard/professor_edit', [
            'professor' => $professor,
            'classes' => $classes
        ]);
    }

    public function updateProfessor() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'whatsapp' => $_POST['whatsapp'],
                'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0
            ];
            $userModel->update($_POST['id'], $data);
            $_SESSION['success'] = "Professor atualizado com sucesso!";
            redirect('school/dashboard');
        }
    }

    public function deleteProfessor() {
        checkAuth('coordinator');
        if (isset($_GET['id'])) {
           $userModel = new User();
           $userModel->delete($_GET['id']); // Add security check
           redirect('school/dashboard');
        }
    }

    public function reviewDocument() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $statusInput = $_POST['status']; // 'aprovado', 'ajustado', 'rejeitado'

            $docModel = new Document();
            $doc = $docModel->findById($id);

            if ($doc) {
                $updateData = ['id' => $id];
                $rejection_count = (int)$doc['rejection_count'];
                $penalty_resubmission = (int)$doc['penalty_resubmission'];

                if ($statusInput === 'rejeitado') {
                    $rejection_count++;
                    if ($rejection_count == 2) $penalty_resubmission = 2;
                    elseif ($rejection_count == 3) $penalty_resubmission = 7;
                    elseif ($rejection_count >= 4) $penalty_resubmission = 10;

                    $updateData['status'] = 'rejeitado';
                    $updateData['rejection_count'] = $rejection_count;
                    $updateData['rejected_at'] = date('Y-m-d H:i:s');
                    $updateData['penalty_resubmission'] = $penalty_resubmission;
                } else {
                    $status = ($statusInput === 'ajustado') ? 'ajustado' : 'aprovado';
                    $updateData['status'] = $status;
                }

                // Recalculate Final Score
                $score_base = (float)$doc['score_base'];
                $penalty_delay = (int)$doc['penalty_delay'];
                $updateData['score_final'] = max(0, $score_base - $penalty_delay - $penalty_resubmission);

                if ($statusInput === 'rejeitado') {
                    $_SESSION['success'] = "Planejamento devolvido para correção com sucesso!";
                } else {
                    $msg = ($statusInput === 'ajustado') ? "Planejamento aprovado com ajustes com sucesso!" : "Planejamento aprovado com sucesso!";
                    $_SESSION['success'] = $msg;
                }

                $docModel->updateStatus($id, $updateData);

                redirect('school/planning/view?id=' . $doc['period_id']);
            } else {
                // If document ID not found, redirect back
                redirect('school/dashboard');
            }
        }
    }

    public function associateToBimester() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        $bimester = $_GET['bimester'] ?? null;
        
        if ($id && $bimester !== null) {
            $planningModel = new Planning();
            $planningModel->updateBimester($id, $bimester);
            $_SESSION['success'] = "Planejamento organizado no " . $bimester . "º Bimestre!";
        }
        
        redirect('school/dashboard');
    }

    public function resetProfessorPassword() {
        checkAuth('coordinator');
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $userModel = new User();
            $professor = $userModel->findById($id);
            
            // Security check: ensure the professor belongs to the coordinator's school
            $coordinator = auth();
            if ($professor && $professor['school_id'] == $coordinator['school_id'] && $professor['role'] == 'professor') {
                $userModel->update($id, ['password' => password_hash('professor123', PASSWORD_DEFAULT)]);
                $_SESSION['success'] = "Senha do professor resetada para 'professor123' com sucesso!";
            } else {
                $_SESSION['error'] = "Você não tem permissão para resetar a senha deste usuário.";
            }
        }
        
        redirect('school/dashboard');
    }

    public function markUploadsAsViewed() {
        checkAuth('coordinator');
        $_SESSION['last_viewed_uploads'] = time();
        echo json_encode(['status' => 'success']);
        exit;
    }

    public function changePassword() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPass = $_POST['password'];
            $user = auth();
            
            $userModel = new User();
            $userModel->updatePassword($user['id'], password_hash($newPass, PASSWORD_DEFAULT));
            
            $_SESSION['success'] = "Sua senha foi alterada com sucesso!";
            redirect('school/dashboard');
        }
    }
}


