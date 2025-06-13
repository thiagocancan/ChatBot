<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../services/DashboardService.php';

/**
 * Admin Controller
 */
class AdminController extends Controller {
    private $dashboardService;
    
    public function __construct() {
        parent::__construct();
        $this->dashboardService = new DashboardService();
    }
    
    public function setup() {
        $this->requireAdmin();
        
        $message = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['api_key']) && !empty($_POST['api_key'])) {
                $_SESSION['GEMINI_API_KEY'] = $_POST['api_key'];
                
                if (isset($_POST['ai_role']) && !empty($_POST['ai_role'])) {
                    $_SESSION['AI_ROLE'] = $_POST['ai_role'];
                    file_put_contents(__DIR__ . '/../../ai_role.txt', $_POST['ai_role']);
                }
                
                if (isset($_POST['ai_provider']) && !empty($_POST['ai_provider'])) {
                    $_SESSION['AI_PROVIDER'] = $_POST['ai_provider'];
                }
                
                $message = 'Configurações salvas com sucesso!';
                $success = true;
                
                header('refresh:2;url=' . $this->basePath . '/');
            } else {
                $message = 'Por favor, forneça uma chave de API válida.';
            }
        }
        
        // Get current values
        $currentApiKey = $_SESSION['GEMINI_API_KEY'] ?? '';
        $currentAiRole = getAIRole();
        $currentAiProvider = getAIProvider();
        
        $this->view('admin/setup', [
            'message' => $message,
            'success' => $success,
            'currentApiKey' => $currentApiKey,
            'currentAiRole' => $currentAiRole,
            'currentAiProvider' => $currentAiProvider
        ]);
    }
    
    public function dashboard() {
        $this->requireAdmin();
        
        // Get dashboard data
        $data = $this->dashboardService->getDashboardData();
        
        $this->view('admin/dashboard', $data);
    }
}
