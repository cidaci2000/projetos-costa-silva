<?php
require_once __DIR__ . '/../../config/database.php';

class Pedido {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll() {
        $sql = "SELECT p.*, 
                       c.nome as cliente_nome, 
                       f.nome as funcionario_nome,
                       (SELECT COUNT(*) FROM itens_pedido WHERE pedido_id = p.id) as total_itens
                FROM pedidos p 
                JOIN clientes c ON p.cliente_id = c.id 
                JOIN funcionarios f ON p.funcionario_id = f.id 
                ORDER BY p.data_pedido DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT p.*, 
                       c.nome as cliente_nome, 
                       c.email as cliente_email,
                       f.nome as funcionario_nome
                FROM pedidos p 
                JOIN clientes c ON p.cliente_id = c.id 
                JOIN funcionarios f ON p.funcionario_id = f.id 
                WHERE p.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getItens($pedido_id) {
        $sql = "SELECT ip.*, 
                       v.modelo, 
                       v.ano,
                       m.nome as marca_nome
                FROM itens_pedido ip 
                JOIN veiculos v ON ip.veiculo_id = v.id 
                JOIN marcas m ON v.marca_id = m.id 
                WHERE ip.pedido_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Criar pedido
            $sql = "INSERT INTO pedidos (
                        cliente_id, funcionario_id, data_pedido, 
                        data_entrega_prevista, forma_pagamento, parcelas, 
                        desconto, observacoes, valor_total
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['cliente_id'],
                $data['funcionario_id'],
                $data['data_pedido'] ?? date('Y-m-d H:i:s'),
                $data['data_entrega_prevista'] ?? null,
                $data['forma_pagamento'] ?? null,
                $data['parcelas'] ?? 1,
                $data['desconto'] ?? 0,
                $data['observacoes'] ?? null,
                $data['valor_total'] ?? 0
            ]);

            $pedido_id = $this->db->lastInsertId();

            // Inserir itens do pedido
            if (!empty($data['itens'])) {
                foreach ($data['itens'] as $item) {
                    $sql = "INSERT INTO itens_pedido (
                                pedido_id, veiculo_id, quantidade, 
                                preco_unitario, desconto_unitario, subtotal
                            ) VALUES (?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $pedido_id,
                        $item['veiculo_id'],
                        $item['quantidade'] ?? 1,
                        $item['preco_unitario'],
                        $item['desconto_unitario'] ?? 0,
                        ($item['preco_unitario'] - ($item['desconto_unitario'] ?? 0)) * ($item['quantidade'] ?? 1)
                    ]);

                    // Atualizar estoque
                    $sql = "UPDATE estoque SET quantidade = quantidade - ? 
                            WHERE veiculo_id = ? AND status = 'disponivel'";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$item['quantidade'] ?? 1, $item['veiculo_id']]);
                }

                // Atualizar valor total do pedido
                $sql = "UPDATE pedidos 
                        SET valor_total = (
                            SELECT COALESCE(SUM(subtotal), 0) 
                            FROM itens_pedido 
                            WHERE pedido_id = ?
                        ) - desconto 
                        WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$pedido_id, $pedido_id]);
            }

            $this->db->commit();
            return $pedido_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE pedidos SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM pedidos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getByStatus($status) {
        $sql = "SELECT p.*, 
                       c.nome as cliente_nome, 
                       f.nome as funcionario_nome
                FROM pedidos p 
                JOIN clientes c ON p.cliente_id = c.id 
                JOIN funcionarios f ON p.funcionario_id = f.id 
                WHERE p.status = ? 
                ORDER BY p.data_pedido DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function getUltimos($limit = 10) {
        $sql = "SELECT p.*, 
                       c.nome as cliente_nome
                FROM pedidos p 
                JOIN clientes c ON p.cliente_id = c.id 
                ORDER BY p.data_pedido DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}