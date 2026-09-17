<?php
require_once __DIR__ . '/../models/Veiculo.php';

class VeiculoController {
    private $veiculo;

    public function __construct() {
        $this->veiculo = new Veiculo();
    }

    public function listar() {
        $filters = [];
        
        if (isset($_GET['marca_id'])) $filters['marca_id'] = $_GET['marca_id'];
        if (isset($_GET['categoria_id'])) $filters['categoria_id'] = $_GET['categoria_id'];
        if (isset($_GET['preco_min'])) $filters['preco_min'] = $_GET['preco_min'];
        if (isset($_GET['preco_max'])) $filters['preco_max'] = $_GET['preco_max'];
        if (isset($_GET['destaque'])) $filters['destaque'] = $_GET['destaque'];

        $veiculos = $this->veiculo->findAll($filters);
        
        return [
            'success' => true,
            'data' => $veiculos,
            'total' => count($veiculos)
        ];
    }

    public function buscar($id) {
        $veiculo = $this->veiculo->findById($id);
        
        if (!$veiculo) {
            return [
                'success' => false,
                'message' => 'Veículo não encontrado'
            ];
        }

        return [
            'success' => true,
            'data' => $veiculo
        ];
    }

    public function criar($data) {
        // Validações
        if (empty($data['marca_id']) || empty($data['categoria_id']) || 
            empty($data['modelo']) || empty($data['ano']) || empty($data['preco'])) {
            return [
                'success' => false,
                'message' => 'Campos obrigatórios: marca_id, categoria_id, modelo, ano, preco'
            ];
        }

        $id = $this->veiculo->create($data);
        
        return [
            'success' => true,
            'message' => 'Veículo criado com sucesso',
            'data' => ['id' => $id]
        ];
    }

    public function atualizar($id, $data) {
        $veiculo = $this->veiculo->findById($id);
        
        if (!$veiculo) {
            return [
                'success' => false,
                'message' => 'Veículo não encontrado'
            ];
        }

        $this->veiculo->update($id, $data);
        
        return [
            'success' => true,
            'message' => 'Veículo atualizado com sucesso'
        ];
    }

    public function deletar($id) {
        $veiculo = $this->veiculo->findById($id);
        
        if (!$veiculo) {
            return [
                'success' => false,
                'message' => 'Veículo não encontrado'
            ];
        }

        $this->veiculo->delete($id);
        
        return [
            'success' => true,
            'message' => 'Veículo removido com sucesso'
        ];
    }

    public function destaques() {
        $veiculos = $this->veiculo->getDestaques();
        
        return [
            'success' => true,
            'data' => $veiculos,
            'total' => count($veiculos)
        ];
    }

    public function pesquisar() {
        $term = $_GET['q'] ?? '';
        
        if (empty($term)) {
            return [
                'success' => false,
                'message' => 'Termo de pesquisa não informado'
            ];
        }

        $veiculos = $this->veiculo->search($term);
        
        return [
            'success' => true,
            'data' => $veiculos,
            'total' => count($veiculos)
        ];
    }
}