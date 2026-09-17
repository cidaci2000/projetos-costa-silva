<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $cliente;

    public function __construct() {
        $this->cliente = new Cliente();
    }

    public function listar() {
        $clientes = $this->cliente->findAll();
        
        return [
            'success' => true,
            'data' => $clientes,
            'total' => count($clientes)
        ];
    }

    public function buscar($id) {
        $cliente = $this->cliente->findById($id);
        
        if (!$cliente) {
            return [
                'success' => false,
                'message' => 'Cliente não encontrado'
            ];
        }

        return [
            'success' => true,
            'data' => $cliente
        ];
    }

    public function criar($data) {
        if (empty($data['nome']) || empty($data['email'])) {
            return [
                'success' => false,
                'message' => 'Campos obrigatórios: nome, email'
            ];
        }

        // Verificar se email já existe
        $existing = $this->cliente->findByEmail($data['email']);
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Email já cadastrado'
            ];
        }

        $id = $this->cliente->create($data);
        
        return [
            'success' => true,
            'message' => 'Cliente criado com sucesso',
            'data' => ['id' => $id]
        ];
    }

    public function atualizar($id, $data) {
        $cliente = $this->cliente->findById($id);
        
        if (!$cliente) {
            return [
                'success' => false,
                'message' => 'Cliente não encontrado'
            ];
        }

        $this->cliente->update($id, $data);
        
        return [
            'success' => true,
            'message' => 'Cliente atualizado com sucesso'
        ];
    }

    public function deletar($id) {
        $cliente = $this->cliente->findById($id);
        
        if (!$cliente) {
            return [
                'success' => false,
                'message' => 'Cliente não encontrado'
            ];
        }

        $this->cliente->delete($id);
        
        return [
            'success' => true,
            'message' => 'Cliente removido com sucesso'
        ];
    }

    public function pedidos($id) {
        $cliente = $this->cliente->findById($id);
        
        if (!$cliente) {
            return [
                'success' => false,
                'message' => 'Cliente não encontrado'
            ];
        }

        $pedidos = $this->cliente->getPedidos($id);
        
        return [
            'success' => true,
            'data' => $pedidos,
            'total' => count($pedidos)
        ];
    }

    public function favoritos($id) {
        $cliente = $this->cliente->findById($id);
        
        if (!$cliente) {
            return [
                'success' => false,
                'message' => 'Cliente não encontrado'
            ];
        }

        $favoritos = $this->cliente->getFavoritos($id);
        
        return [
            'success' => true,
            'data' => $favoritos,
            'total' => count($favoritos)
        ];
    }
}