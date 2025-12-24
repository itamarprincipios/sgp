<?php

class School extends Model {
    public function all() {
        return $this->db->query("SELECT * FROM schools ORDER BY name ASC")->fetchAll();
    }

    public function findById($id) {
        return $this->db->query("SELECT * FROM schools WHERE id = :id", ['id' => $id])->fetch();
    }

    public function create($data) {
        return $this->db->query("INSERT INTO schools (name, inep_code, director_name, director_phone) VALUES (:name, :inep_code, :director_name, :director_phone)", [
            'name' => $data['name'],
            'inep_code' => $data['inep_code'] ?? null,
            'director_name' => $data['director_name'] ?? null,
            'director_phone' => $data['director_phone'] ?? null
        ]);
    }

    public function update($id, $data) {
        return $this->db->query("UPDATE schools SET name = :name, inep_code = :inep_code, director_name = :director_name, director_phone = :director_phone WHERE id = :id", [
            'id' => $id,
            'name' => $data['name'],
            'inep_code' => $data['inep_code'] ?? null,
            'director_name' => $data['director_name'] ?? null,
            'director_phone' => $data['director_phone'] ?? null
        ]);
    }

    public function delete($id) {
        // Warning: Deleting a school will affect all related users and data.
        // Implementation might prefer 'deactivating' instead of hard delete.
        return $this->db->query("DELETE FROM schools WHERE id = :id", ['id' => $id]);
    }
}
