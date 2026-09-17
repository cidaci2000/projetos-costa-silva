<?php
// models/Product.php

require_once __DIR__ . '/../config/database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar todos os produtos
     */
    public function findAll($apenasAtivos = true) {
        $sql = "SELECT p.*, c.nome as categoria_nome, c.icone as categoria_icone,
                COALESCE(p.preco_promocional, p.preco) as preco_atual
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id";
        
        if ($apenasAtivos) {
            $sql .= " WHERE p.ativo = 1";
        }
        
        $sql .= " ORDER BY p.destaque DESC, p.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar produto por ID
     */
    public function findById($id) {
        $sql = "SELECT p.*, c.nome as categoria_nome 
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar produtos em destaque
     */
    public function findDestaques() {
        $sql = "SELECT p.*, c.nome as categoria_nome,
                COALESCE(p.preco_promocional, p.preco) as preco_atual
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.ativo = 1 AND p.destaque = 1
                ORDER BY p.created_at DESC
                LIMIT 8";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Criar novo produto
     */
    public function create($data) {
        $sql = "INSERT INTO produtos (
            categoria_id, nome, descricao, preco, preco_promocional, 
            estoque, imagem, icone, badge, destaque, ativo
        ) VALUES (
            :categoria_id, :nome, :descricao, :preco, :preco_promocional,
            :estoque, :imagem, :icone, :badge, :destaque, :ativo
        )";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    /**
     * Atualizar produto
     */
    public function update($id, $data) {
        $sql = "UPDATE produtos SET 
            categoria_id = :categoria_id,
            nome = :nome,
            descricao = :descricao,
            preco = :preco,
            preco_promocional = :preco_promocional,
            estoque = :estoque,
            imagem = :imagem,
            icone = :icone,
            badge = :badge,
            destaque = :destaque,
            ativo = :ativo
        WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    /**
     * Deletar produto
     */
    public function delete($id) {
        $sql = "DELETE FROM produtos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Buscar todas as categorias
     */
    public function getCategorias() {
        $sql = "SELECT * FROM categorias ORDER BY nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar produtos com baixo estoque
     */
    public function findLowStock($limite = 10) {
        $sql = "SELECT p.*, c.nome as categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.ativo = 1 AND p.estoque <= :limite
                ORDER BY p.estoque ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['limite' => $limite]);
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar produtos em promoção
     */
    public function findOnSale() {
        $sql = "SELECT p.*, c.nome as categoria_nome,
                COALESCE(p.preco_promocional, p.preco) as preco_atual
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.ativo = 1 
                AND p.preco_promocional IS NOT NULL 
                AND p.preco_promocional < p.preco
                ORDER BY p.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}