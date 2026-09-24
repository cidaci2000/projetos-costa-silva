<?php
/**
 * Baixa imagens de exemplo do Unsplash para a pasta uploads/
 * Rode UMA VEZ: http://localhost/reyvie/seed_images.php
 */
require_once 'includes/functions.php';

$imagens = [
    'base_hd.jpg'        => 'https://images.unsplash.com/photo-1631214540242-3cd8c4b3b1a8?w=800',
    'corretivo.jpg'      => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=800',
    'batom_matte.jpg'    => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=800',
    'paleta_nude.jpg'    => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=800',
    'blush_rose.jpg'     => 'https://images.unsplash.com/photo-1596704017254-9b121068fb31?w=800',
    'delineador.jpg'     => 'https://images.unsplash.com/photo-1583241800698-9c2e0a3f3c9c?w=800',
    'po_compacto.jpg'    => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800',
    'corretivo_verde.jpg'=> 'https://images.unsplash.com/photo-1590156206657-aec5f6b7f9d1?w=800',
    'gloss.jpg'          => 'https://images.unsplash.com/photo-1588514912908-8f58917f5f5e?w=800',
];

if (!is_dir('uploads')) mkdir('uploads', 0755, true);

foreach ($imagens as $nome => $url) {
    $destino = 'uploads/' . $nome;
    if (file_exists($destino)) {
        echo "✅ Já existe: $nome<br>";
        continue;
    }
    $conteudo = @file_get_contents($url);
    if ($conteudo) {
        file_put_contents($destino, $conteudo);
        echo "⬇️  Baixado: $nome<br>";
    } else {
        echo "❌ Falhou: $nome<br>";
    }
}
echo "<br><strong>Concluído!</strong> Agora atualize os produtos no admin para usar as imagens locais.";