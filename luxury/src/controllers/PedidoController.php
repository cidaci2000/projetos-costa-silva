<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Veiculo.php';

class PedidoController {
    private $pedido;

    public function __construct() {
        $this->pedido = new Pedido();
    }

    public function listar() {
        $pedidos = $this->pedido->findAll();
        
        return [
            'success' => true,
            'data' => $pedidos,
            'total' => count($pedidos)
        ];
    }

    public function buscar($id) {
        $pedido = $this->pedido->findById($id);
        
        if (!$pedido) {
            return [
                'success' => false,
                'message' => 'Pedido não encontrado'
            ];
        }

        $itens = $this->pedido->getItens($id);
        $pedido['itens'] = $itens;

        return [
            'success' => true,
            'data' => $pedido
        ];
    }

    public function criar($data) {
        if (empty($data['cliente_id']) || empty($data['funcionario_id']) || empty($data['itens'])) {
            return [
                'success' => false,
                'message' => 'Campos obrigatórios: cliente_id, funcionario_id, itens'
            ];
        }

        try {
            $id = $this->pedido->create($data);
            
            return [
                'success' => true,
                'message' => 'Pedido criado com sucesso',
                'data' => ['id' => $id]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao criar pedido: ' . $e->getMessage()
            ];
        }
    }

    public function atualizarStatus($id, $data) {
        if (empty($data['status'])) {
            return [
                'success' => false,
                'message' => 'Status é obrigatório'
            ];
        }

        $pedido = $this->pedido->findById($id);
        
        if (!$pedido) {
            return [
                'success' => false,
                'message' => 'Pedido não encontrado'
            ];
        }

        $this->pedido->updateStatus($id, $data['status']);
        
        return [
            'success' => true,
            'message' => 'Status do pedido atualizado com sucesso'
        ];
    }

    public function deletar($id) {
        $pedido = $this->pedido->findById($id);
        
        if (!$pedido) {
            return [
                'success' => false,
                'message' => 'Pedido não encontrado'
            ];
        }

        $this->pedido->delete($id);
        
        return [
            'success' => true,
            'message' => 'Pedido removido com sucesso'
        ];
    }

    public function porStatus() {
        $status = $_GET['status'] ?? 'pendente';
        $pedidos = $this->pedido->getByStatus($status);
        
        return [
            'success' => true,
            'data' => $pedidos,
            'total' => count($pedidos)
        ];
    }

    public function ultimos() {
        $limit = $_GET['limit'] ?? 10;
        $pedidos = $this->pedido->getUltimos($limit);
        
        return [
            'success' => true,
            'data' => $pedidos,
            'total' => count($pedidos)
        ];
    }
}
