<?php
require_once __DIR__ . '/../../config/database.php';

class Estoque {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll() {
        $sql = "SELECT e.*, 
                       v.modelo, 
                       v.ano,
                       m.nome as marca_nome
                FROM estoque e 
                JOIN veiculos v ON e.veiculo_id = v.id 
                JOIN marcas m ON v.marca_id = m.id 
                ORDER BY v.modelo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT e.*, 
                       v.modelo, 
                       v.ano,
                       m.nome as marca_nome
                FROM estoque e 
                JOIN veiculos v ON e.veiculo_id = v.id 
                JOIN marcas m ON v.marca_id = m.id 
                WHERE e.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByVeiculo($veiculo_id) {
        $sql = "SELECT * FROM estoque WHERE veiculo_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$veiculo_id]);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'criado_em') {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }

        $params[] = $id;
        $sql = "UPDATE estoque SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getAlertas() {
        $sql = "SELECT e.*, 
                       v.modelo, 
                       m.nome as marca_nome,
                       CASE 
                           WHEN e.quantidade = 0 THEN 'SEM ESTOQUE'
                           WHEN e.quantidade <= 2 THEN 'ESTOQUE BAIXO'
                           ELSE 'ESTOQUE OK'
                       END as alerta
                FROM estoque e 
                JOIN veiculos v ON e.veiculo_id = v.id 
                JOIN marcas m ON v.marca_id = m.id 
                WHERE e.quantidade <= 2 
                ORDER BY e.quantidade";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function add($veiculo_id, $quantidade, $localizacao) {
        $sql = "INSERT INTO estoque (veiculo_id, quantidade, localizacao, status) 
                VALUES (?, ?, ?, 'disponivel') 
                ON DUPLICATE KEY UPDATE quantidade = quantidade + ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$veiculo_id, $quantidade, $localizacao, $quantidade]);
    }

    public function remove($veiculo_id, $quantidade) {
        $sql = "UPDATE estoque SET quantidade = quantidade - ? 
                WHERE veiculo_id = ? AND quantidade >= ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantidade, $veiculo_id, $quantidade]);
    }
}