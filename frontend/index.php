<?php
require_once 'config.php';

$api = new ApiClient();
$result = $api->getProjects();

$title = 'Lista de Projetos';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .nav a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .content {
            padding: 40px;
        }

        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 600;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-danger {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
        }

        .btn-success {
            background: linear-gradient(45deg, #51cf66, #40c057);
        }

        .project-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .project-title {
            font-size: 1.5em;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .project-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .project-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-ativo {
            background: #d4edda;
            color: #155724;
        }

        .status-pausado {
            background: #fff3cd;
            color: #856404;
        }

        .status-finalizado {
            background: #f8d7da;
            color: #721c24;
        }

        .project-actions {
            display: flex;
            gap: 10px;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Gerenciador de Projetos 🚀</h1>
            <nav class="nav">
                <a href="index.php">Todos os Projetos</a>
                <a href="create.php">Novo Projeto</a>
            </nav>
        </div>
        <div class="content">

<?php if (isset($_GET['message'])): ?>
    <?php $messageType = $_GET['type'] ?? 'success'; ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($_GET['message']) ?>
    </div>
<?php endif; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="color: #333; font-size: 2em;">Meus Projetos</h2>
    <a href="create.php" class="btn btn-success">+ Novo Projeto</a>
</div>

<?php if ($result['status'] === 200 && !empty($result['data'])): ?>
    <?php foreach ($result['data'] as $project): ?>
        <div class="project-card">
            <div class="project-title"><?= htmlspecialchars($project['name']) ?></div>
            <div class="project-description">
                <?= htmlspecialchars($project['description'] ?? 'Sem descrição') ?>
            </div>

            <div class="project-meta">
                <span class="status-badge status-<?= $project['status'] ?>">
                    <?= ucfirst($project['status']) ?>
                </span>
                <small style="color: #888;">
                    Criado em: <?= date('d/m/Y H:i', strtotime($project['created_at'])) ?>
                </small>
            </div>

            <div class="project-actions">
                <a href="view.php?id=<?= $project['id'] ?>" class="btn">Ver Detalhes</a>
                <a href="edit.php?id=<?= $project['id'] ?>" class="btn">Editar</a>
                <a href="delete.php?id=<?= $project['id'] ?>" class="btn btn-danger"
                   onclick="return confirm('Tem certeza que deseja excluir este projeto?')">Excluir</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php elseif ($result['status'] !== 200): ?>
    <div class="alert alert-error">
        <strong>Erro de conexão:</strong>
        <?= isset($result['data']['error']) ? $result['data']['error'] : 'Não foi possível conectar com a API' ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <h3>Nenhum projeto encontrado</h3>
        <p>Comece criando seu primeiro projeto!</p>
        <a href="create.php" class="btn btn-success" style="margin-top: 20px;">Criar Primeiro Projeto</a>
    </div>
<?php endif; ?>

        </div>
    </div>
</body>
</html>