<?php
require_once __DIR__ . '/../models/Estoque.php';

class EstoqueController {
    private $estoque;

    public function __construct() {
        $this->estoque = new Estoque();
    }

    public function listar() {
        $estoque = $this->estoque->findAll();
        
        return [
            'success' => true,
            'data' => $estoque,
            'total' => count($estoque)
        ];
    }

    public function buscar($id) {
        $item = $this->estoque->findById($id);
        
        if (!$item) {
            return [
                'success' => false,
                'message' => 'Item de estoque não encontrado'
            ];
        }

        return [
            'success' => true,
            'data' => $item
        ];
    }

    public function atualizar($id, $data) {
        $item = $this->estoque->findById($id);
        
        if (!$item) {
            return [
                'success' => false,
                'message' => 'Item de estoque não encontrado'
            ];
        }

        $this->estoque->update($id, $data);
        
        return [
            'success' => true,
            'message' => 'Estoque atualizado com sucesso'
        ];
    }

    public function alertas() {
        $alertas = $this->estoque->getAlertas();
        
        return [
            'success' => true,
            'data' => $alertas,
            'total' => count($alertas)
        ];
    }

    public function adicionar($data) {
        if (empty($data['veiculo_id']) || empty($data['quantidade']) || empty($data['localizacao'])) {
            return [
                'success' => false,
                'message' => 'Campos obrigatórios: veiculo_id, quantidade, localizacao'
            ];
        }

        $this->estoque->add($data['veiculo_id'], $data['quantidade'], $data['localizacao']);
        
        return [
            'success' => true,
            'message' => 'Estoque atualizado com sucesso'
        ];
    }
}