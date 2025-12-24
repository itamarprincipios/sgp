<?php
require_once __DIR__ . '/../Core/Model.php';

class RankingModel extends Model {
    
    public function getProfessorRanking($filter = 'annual', $year = null) {
        if (!$year) $year = date('Y');
        $where = "YEAR(d.submitted_at) = :year";
        $params = ['year' => $year];

        if ($filter === 'monthly') {
            $where .= " AND MONTH(d.submitted_at) = :month";
            $params['month'] = date('m');
        } elseif ($filter === 'bimestral') {
            $bimestre = ceil(date('m') / 2);
            $months = [($bimestre * 2) - 1, $bimestre * 2];
            $where .= " AND MONTH(d.submitted_at) IN (" . implode(',', $months) . ")";
        }

        $sql = "SELECT u.name as professor_name, u.whatsapp, s.name as school_name, SUM(d.score_final) as total_points
                FROM users u
                JOIN schools s ON u.school_id = s.id
                JOIN documents d ON u.id = d.user_id
                WHERE u.role = 'professor' AND $where AND d.status = 'aprovado'
                GROUP BY u.id
                ORDER BY total_points DESC
                LIMIT 10";
        
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getSchoolRanking($filter = 'annual', $year = null) {
        if (!$year) $year = date('Y');
        $where = "YEAR(d.submitted_at) = :year";
        $params = ['year' => $year];

        if ($filter === 'monthly') {
            $where .= " AND MONTH(d.submitted_at) = :month";
            $params['month'] = date('m');
        }

        $sql = "SELECT s.name as school_name, 
                       COUNT(d.id) as total_docs,
                       (SUM(CASE WHEN d.submitted_at <= p.deadline THEN 1 ELSE 0 END) / COUNT(d.id)) * 100 as punctuality_percentage
                FROM schools s
                JOIN users u ON s.id = u.school_id
                JOIN documents d ON u.id = d.user_id
                JOIN periods p ON d.period_id = p.id
                WHERE $where AND d.status = 'aprovado'
                GROUP BY s.id
                ORDER BY punctuality_percentage DESC
                LIMIT 10";
        
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getCoordinatorRanking($filter = 'annual', $year = null) {
        // Coordenador pontualidade baseada na média da escola ou no fato de aprovarem rápido?
        // O requisito diz "Coordenadores mais pontuais". Vou assumir pontuação da escola vinculada.
        if (!$year) $year = date('Y');
        $where = "YEAR(d.submitted_at) = :year";
        $params = ['year' => $year];

        $sql = "SELECT uc.name as coordinator_name, uc.whatsapp, s.name as school_name, 
                       COUNT(d.id) as total_docs,
                       (SUM(CASE WHEN d.submitted_at <= p.deadline THEN 1 ELSE 0 END) / COUNT(d.id)) * 100 as punctuality_percentage
                FROM users uc
                JOIN schools s ON uc.school_id = s.id
                JOIN users up ON s.id = up.school_id
                JOIN documents d ON up.id = d.user_id
                JOIN periods p ON d.period_id = p.id
                WHERE uc.role = 'coordinator' AND up.role = 'professor' AND $where AND d.status = 'aprovado'
                GROUP BY uc.id
                ORDER BY punctuality_percentage DESC
                LIMIT 10";
        
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getMedalsForUser($userId) {
        // Lógica de cálculo de medalhas (pode ser consultada sob demanda ou salva no banco via cron)
        $this->updateMedals($userId);
        $sql = "SELECT * FROM user_medals WHERE user_id = :user_id ORDER BY created_at DESC";
        return $this->db->query($sql, ['user_id' => $userId])->fetchAll();
    }

    private function updateMedals($userId) {
        $bimestre = ceil(date('m') / 2);
        $year = date('Y');
        
        // 1. Obter todos os envios do bimestre (excluindo rejeitados)
        $months = [($bimestre * 2) - 1, $bimestre * 2];
        $sql = "SELECT d.* FROM documents d 
                WHERE d.user_id = :user_id 
                AND status != 'rejeitado'
                AND YEAR(d.submitted_at) = :year 
                AND MONTH(d.submitted_at) IN (" . implode(',', $months) . ")";
        $docs = $this->db->query($sql, ['user_id' => $userId, 'year' => $year])->fetchAll();
        
        if (empty($docs)) return;

        $total = count($docs);
        $on_time = 0;
        $total_base_score = 0;
        
        foreach ($docs as $d) {
            if ($d['penalty_delay'] == 0) $on_time++;
            $total_base_score += (float)$d['score_base'];
        }

        $avg_score = $total_base_score / $total;
        $percent_on_time = ($on_time / $total) * 100;

        $medalsToAdd = [];

        // Critérios
        if ($percent_on_time == 100) $medalsToAdd[] = '🥇 Pontualidade Ouro';
        elseif ($total - $on_time <= 1) $medalsToAdd[] = '🥈 Pontualidade Prata';
        
        if ($total > 0) $medalsToAdd[] = '🥉 Pontualidade Bronze'; // Todos realizados
        if ($avg_score >= 16) $medalsToAdd[] = '⏰ Entrega Antecipada';

        // Critério: Regularidade (3 bimestres seguidos sem atraso)
        if ($this->checkRegularity($userId, $year, $bimestre)) {
            $medalsToAdd[] = '🔁 Regularidade';
        }

        // Persistir (evitando duplicidade para o mesmo bimestre/tipo)
        foreach ($medalsToAdd as $medal) {
            $checkSql = "SELECT id FROM user_medals WHERE user_id = :uid AND medal_type = :type AND reference_date = :ref";
            $ref = "$year-$bimestre-01";
            $exists = $this->db->query($checkSql, ['uid' => $userId, 'type' => $medal, 'ref' => $ref])->fetch();
            
            if (!$exists) {
                $this->db->query("INSERT INTO user_medals (user_id, medal_type, period_type, reference_date) VALUES (:uid, :type, 'bimestre', :ref)", [
                    'uid' => $userId,
                    'type' => $medal,
                    'ref' => $ref
                ]);
            }
        }
    }

    private function checkRegularity($userId, $year, $currentBimestre) {
        // Se for o 1º ou 2º bimestre do ano, precisamos olhar o ano anterior? 
        // Vamos simplificar para 3 bimestres dentro do mesmo fluxo histórico.
        $countConsecutive = 0;
        
        for ($i = 0; $i < 3; $i++) {
            $b = $currentBimestre - $i;
            $y = $year;
            if ($b <= 0) {
                $b += 6; // Assume 6 bimestres/ano ou ajusta conforme realidade
                $y -= 1;
            }

            $months = [($b * 2) - 1, $b * 2];
            $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN penalty_delay > 0 THEN 1 ELSE 0 END) as delays
                    FROM documents 
                    WHERE user_id = :uid AND YEAR(submitted_at) = :y AND MONTH(submitted_at) IN (" . implode(',', $months) . ")";
            $res = $this->db->query($sql, ['uid' => $userId, 'y' => $y])->fetch();

            if ($res['total'] > 0 && $res['delays'] == 0) {
                $countConsecutive++;
            } else {
                break;
            }
        }

        return ($countConsecutive >= 3);
    }
}
