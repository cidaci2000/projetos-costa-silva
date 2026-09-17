<?php
// models/User.php

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Autenticar admin
     */
    public function authenticateAdmin($email, $password) {
        $sql = "SELECT * FROM usuarios_admin WHERE email = :email AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['senha_hash'])) {
            return $user;
        }
        return false;
    }
    
    /**
     * Autenticar cliente
     */
    public function authenticateCliente($email, $password) {
        $sql = "SELECT * FROM clientes WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['senha_hash'])) {
            return $user;
        }
        return false;
    }
    
    /**
     * Cadastrar cliente
     */
    public function createCliente($data) {
        // Verificar se email já existe
        $sql = "SELECT id FROM clientes WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $data['email']]);
        if ($stmt->fetch()) {
            return ['error' => 'E-mail já cadastrado!'];
        }
        
        // Verificar se CPF já existe
        if (!empty($data['cpf'])) {
            $sql = "SELECT id FROM clientes WHERE cpf = :cpf";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['cpf' => $data['cpf']]);
            if ($stmt->fetch()) {
                return ['error' => 'CPF já cadastrado!'];
            }
        }
        
        $sql = "INSERT INTO clientes (
            nome, email, telefone, cpf, endereco, cidade, estado, cep, senha_hash
        ) VALUES (
            :nome, :email, :telefone, :cpf, :endereco, :cidade, :estado, :cep, :senha_hash
        )";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'telefone' => $data['telefone'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'endereco' => $data['endereco'] ?? null,
            'cidade' => $data['cidade'] ?? null,
            'estado' => $data['estado'] ?? null,
            'cep' => $data['cep'] ?? null,
            'senha_hash' => password_hash($data['senha'], PASSWORD_DEFAULT)
        ]);
        
        if ($success) {
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        }
        return ['error' => 'Erro ao cadastrar. Tente novamente.'];
    }
    
    /**
     * Buscar cliente por ID
     */
    public function findClienteById($id) {
        $sql = "SELECT id, nome, email, telefone, cpf, endereco, cidade, estado, cep 
                FROM clientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Atualizar cliente
     */
    public function updateCliente($id, $data) {
        $sql = "UPDATE clientes SET 
            nome = :nome,
            telefone = :telefone,
            endereco = :endereco,
            cidade = :cidade,
            estado = :estado,
            cep = :cep
        WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    /**
     * Buscar admin por ID
     */
    public function findAdminById($id) {
        $sql = "SELECT id, nome, email, nivel FROM usuarios_admin WHERE id = :id AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Criar novo admin (apenas para setup)
     */
    public function createAdmin($data) {
        $sql = "INSERT INTO usuarios_admin (nome, email, senha_hash, nivel) 
                VALUES (:nome, :email, :senha_hash, :nivel)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha_hash' => password_hash($data['senha'], PASSWORD_DEFAULT),
            'nivel' => $data['nivel'] ?? 'admin'
        ]);
    }
}