<?php
require_once __DIR__ . '/../../config/database.php';

class Veiculo {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll($filters = []) {
        $sql = "SELECT v.*, m.nome as marca_nome, c.nome as categoria_nome 
                FROM veiculos v 
                JOIN marcas m ON v.marca_id = m.id 
                JOIN categorias c ON v.categoria_id = c.id 
                WHERE v.ativo = 1";
        $params = [];

        if (!empty($filters['marca_id'])) {
            $sql .= " AND v.marca_id = ?";
            $params[] = $filters['marca_id'];
        }

        if (!empty($filters['categoria_id'])) {
            $sql .= " AND v.categoria_id = ?";
            $params[] = $filters['categoria_id'];
        }

        if (!empty($filters['preco_min'])) {
            $sql .= " AND v.preco >= ?";
            $params[] = $filters['preco_min'];
        }

        if (!empty($filters['preco_max'])) {
            $sql .= " AND v.preco <= ?";
            $params[] = $filters['preco_max'];
        }

        if (!empty($filters['destaque'])) {
            $sql .= " AND v.destaque = 1";
        }

        $sql .= " ORDER BY v.preco DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT v.*, m.nome as marca_nome, c.nome as categoria_nome,
                e.quantidade as estoque_quantidade, e.localizacao
                FROM veiculos v 
                JOIN marcas m ON v.marca_id = m.id 
                JOIN categorias c ON v.categoria_id = c.id
                LEFT JOIN estoque e ON v.id = e.veiculo_id AND e.status = 'disponivel'
                WHERE v.id = ? AND v.ativo = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO veiculos (
                    marca_id, categoria_id, modelo, ano, preco, cor, 
                    quilometragem, combustivel, motor, potencia, 
                    transmissao, tracao, portas, lugares, descricao, 
                    imagem_url, destaque
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['marca_id'],
            $data['categoria_id'],
            $data['modelo'],
            $data['ano'],
            $data['preco'],
            $data['cor'] ?? null,
            $data['quilometragem'] ?? 0,
            $data['combustivel'] ?? null,
            $data['motor'] ?? null,
            $data['potencia'] ?? null,
            $data['transmissao'] ?? null,
            $data['tracao'] ?? null,
            $data['portas'] ?? null,
            $data['lugares'] ?? null,
            $data['descricao'] ?? null,
            $data['imagem_url'] ?? null,
            $data['destaque'] ?? 0
        ]);

        return $this->db->lastInsertId();
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
        $sql = "UPDATE veiculos SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "UPDATE veiculos SET ativo = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getDestaques() {
        $sql = "SELECT v.*, m.nome as marca_nome, c.nome as categoria_nome 
                FROM veiculos v 
                JOIN marcas m ON v.marca_id = m.id 
                JOIN categorias c ON v.categoria_id = c.id 
                WHERE v.ativo = 1 AND v.destaque = 1 
                ORDER BY v.criado_em DESC 
                LIMIT 6";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search($term) {
        $sql = "SELECT v.*, m.nome as marca_nome, c.nome as categoria_nome 
                FROM veiculos v 
                JOIN marcas m ON v.marca_id = m.id 
                JOIN categorias c ON v.categoria_id = c.id 
                WHERE v.ativo = 1 
                AND (v.modelo LIKE ? OR m.nome LIKE ? OR v.descricao LIKE ?)
                ORDER BY v.preco DESC";
        
        $term = "%$term%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$term, $term, $term]);
        return $stmt->fetchAll();
    }
}