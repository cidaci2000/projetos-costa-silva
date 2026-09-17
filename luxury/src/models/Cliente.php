<?php
require_once __DIR__ . '/../../config/database.php';

class Cliente {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll() {
        $sql = "SELECT * FROM clientes WHERE ativo = 1 ORDER BY nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM clientes WHERE id = ? AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM clientes WHERE email = ? AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO clientes (
                    nome, email, telefone, cpf_cnpj, data_nascimento, 
                    genero, endereco, cidade, estado, cep, pais, 
                    senha_hash, recebe_ofertas
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nome'],
            $data['email'],
            $data['telefone'] ?? null,
            $data['cpf_cnpj'] ?? null,
            $data['data_nascimento'] ?? null,
            $data['genero'] ?? null,
            $data['endereco'] ?? null,
            $data['cidade'] ?? null,
            $data['estado'] ?? null,
            $data['cep'] ?? null,
            $data['pais'] ?? 'Brasil',
            $data['senha_hash'] ?? null,
            $data['recebe_ofertas'] ?? 0
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'cliente_desde' && $key !== 'criado_em') {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }

        $params[] = $id;
        $sql = "UPDATE clientes SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "UPDATE clientes SET ativo = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getPedidos($cliente_id) {
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM itens_pedido WHERE pedido_id = p.id) as total_itens
                FROM pedidos p 
                WHERE p.cliente_id = ? 
                ORDER BY p.data_pedido DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cliente_id]);
        return $stmt->fetchAll();
    }

    public function getFavoritos($cliente_id) {
        $sql = "SELECT v.*, m.nome as marca_nome 
                FROM favoritos f 
                JOIN veiculos v ON f.veiculo_id = v.id 
                JOIN marcas m ON v.marca_id = m.id 
                WHERE f.cliente_id = ? AND v.ativo = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cliente_id]);
        return $stmt->fetchAll();
    }
}