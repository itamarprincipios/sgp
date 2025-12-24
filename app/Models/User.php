<?php

require_once __DIR__ . '/../Core/Model.php';

class User extends Model {
    public function findByEmail($email) {
        $stmt = $this->db->query("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->query("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function createProfessor($data) {
        $sql = "INSERT INTO users (school_id, name, email, password, role, whatsapp, class_id, is_physical_education) 
                VALUES (:school_id, :name, :email, :password, 'professor', :whatsapp, :class_id, :is_physical_education)";
        // Password should be hashed before passed here or here. Assuming hashed.
        return $this->db->query($sql, $data);
    }

    public function getProfessorsBySchoolWithClass($schoolId) {
        $sql = "SELECT u.*, c.name as class_name 
                FROM users u 
                LEFT JOIN classes c ON u.class_id = c.id 
                WHERE u.school_id = :school_id AND u.role = 'professor' 
                ORDER BY c.name ASC, u.name ASC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }

    public function getByRole($role) {
        $sql = "SELECT u.*, s.name as school_name 
                FROM users u 
                LEFT JOIN schools s ON u.school_id = s.id 
                WHERE u.role = :role 
                ORDER BY u.name ASC";
        return $this->db->query($sql, ['role' => $role])->fetchAll();
    }

    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($f) { return ":$f"; }, $fields);
        
        $sql = "INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        return $this->db->query($sql, $data);
    }

    public function update($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
            }
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $data['id'] = $id;
        return $this->db->query($sql, $data);
    }

    public function getBySchoolId($schoolId) {
        $sql = "SELECT * FROM users WHERE school_id = :school_id ORDER BY name ASC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }
}
