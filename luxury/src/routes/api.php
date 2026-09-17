<?php
/**
 * Rotas da API
 */

// Função para rotear as requisições
function route($method, $uri, $requestBody) {
    $response = ['success' => false, 'message' => 'Rota não encontrada'];
    
    // Remover prefixo /api
    $uri = str_replace('/api', '', $uri);
    $path = explode('/', trim($uri, '/'));
    $resource = $path[0] ?? '';
    $id = $path[1] ?? null;

    // Controllers
    $veiculoController = new VeiculoController();
    $clienteController = new ClienteController();
    $pedidoController = new PedidoController();
    $estoqueController = new EstoqueController();

    // Rotas de Veículos
    if ($resource === 'veiculos') {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $veiculoController->buscar($id);
                } else if (isset($_GET['destaque']) && $_GET['destaque'] == 1) {
                    $response = $veiculoController->destaques();
                } else if (isset($_GET['q'])) {
                    $response = $veiculoController->pesquisar();
                } else {
                    $response = $veiculoController->listar();
                }
                break;
            case 'POST':
                $response = $veiculoController->criar($requestBody);
                break;
            case 'PUT':
                if ($id) {
                    $response = $veiculoController->atualizar($id, $requestBody);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $response = $veiculoController->deletar($id);
                }
                break;
        }
    }

    // Rotas de Clientes
    elseif ($resource === 'clientes') {
        switch ($method) {
            case 'GET':
                if ($id) {
                    if (isset($_GET['pedidos'])) {
                        $response = $clienteController->pedidos($id);
                    } elseif (isset($_GET['favoritos'])) {
                        $response = $clienteController->favoritos($id);
                    } else {
                        $response = $clienteController->buscar($id);
                    }
                } else {
                    $response = $clienteController->listar();
                }
                break;
            case 'POST':
                $response = $clienteController->criar($requestBody);
                break;
            case 'PUT':
                if ($id) {
                    $response = $clienteController->atualizar($id, $requestBody);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $response = $clienteController->deletar($id);
                }
                break;
        }
    }

    // Rotas de Pedidos
    elseif ($resource === 'pedidos') {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $pedidoController->buscar($id);
                } elseif (isset($_GET['status'])) {
                    $response = $pedidoController->porStatus();
                } elseif (isset($_GET['ultimos'])) {
                    $response = $pedidoController->ultimos();
                } else {
                    $response = $pedidoController->listar();
                }
                break;
            case 'POST':
                $response = $pedidoController->criar($requestBody);
                break;
            case 'PUT':
                if ($id && isset($requestBody['status'])) {
                    $response = $pedidoController->atualizarStatus($id, $requestBody);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $response = $pedidoController->deletar($id);
                }
                break;
        }
    }

    // Rotas de Estoque
    elseif ($resource === 'estoque') {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $response = $estoqueController->buscar($id);
                } elseif (isset($_GET['alertas'])) {
                    $response = $estoqueController->alertas();
                } else {
                    $response = $estoqueController->listar();
                }
                break;
            case 'POST':
                $response = $estoqueController->adicionar($requestBody);
                break;
            case 'PUT':
                if ($id) {
                    $response = $estoqueController->atualizar($id, $requestBody);
                }
                break;
        }
    }

    return $response;
}