<?php

require_once __DIR__ . '/../Core/Model.php';

class ClassModel extends Model {
    public function create($schoolId, $name) {
        $sql = "INSERT INTO classes (school_id, name) VALUES (:school_id, :name)";
        return $this->db->query($sql, ['school_id' => $schoolId, 'name' => $name]);
    }

    public function getBySchoolId($schoolId) {
        $sql = "SELECT * FROM classes WHERE school_id = :school_id ORDER BY name ASC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }

    public function getBySchoolIdWithProfessor($schoolId) {
        $sql = "SELECT c.*, u.name as professor_name 
                FROM classes c
                LEFT JOIN users u ON u.class_id = c.id AND u.role = 'professor' AND (u.is_physical_education = 0 OR u.is_physical_education IS NULL)
                WHERE c.school_id = :school_id 
                ORDER BY c.name ASC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }
    public function findById($id) {
        return $this->db->query("SELECT * FROM classes WHERE id = :id", ['id' => $id])->fetch();
    }

    public function delete($id) {
        $sql = "DELETE FROM classes WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }

    public function update($id, $name) {
        $sql = "UPDATE classes SET name = :name WHERE id = :id";
        return $this->db->query($sql, ['id' => $id, 'name' => $name]);
    }
}
