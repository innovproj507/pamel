<?php

namespace Core;

class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    public function find($id)
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function where($column, $value)
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE {$column} = ?", [$value]);
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }
}
