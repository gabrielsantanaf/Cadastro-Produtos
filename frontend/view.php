<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?message=ID do projeto inválido&type=error');
    exit;
}

$projectId = (int)$_GET['id'];
$api = new ApiClient();
$result = $api->getProject($projectId);

$title = 'Visualizar Projeto';
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
            max-width: 800px;
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
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #5a6268);
        }

        .btn-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800);
        }

        .btn-danger {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
        }

        .project-details {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
        }

        .project-title {
            font-size: 2.5em;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .detail-row {
            margin-bottom: 25px;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 1.1em;
            margin-bottom: 8px;
            display: block;
        }

        .detail-value {
            color: #333;
            font-size: 1em;
            line-height: 1.6;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
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

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Detalhes do Projeto</h1>
            <nav class="nav">
                <a href="index.php">← Voltar à Lista</a>
                <a href="create.php">+ Novo Projeto</a>
            </nav>
        </div>
        <div class="content">

<?php if ($result['status'] === 200 && isset($result['data'])): ?>
    <?php $project = $result['data']; ?>
    <div class="project-details">
        <div class="project-title"><?= htmlspecialchars($project['name']) ?></div>

        <div class="detail-row">
            <span class="detail-label">📝 Descrição</span>
            <div class="detail-value">
                <?= htmlspecialchars($project['description'] ?? 'Sem descrição disponível') ?>
            </div>
        </div>

        <div class="detail-row">
            <span class="detail-label">📊 Status</span>
            <div class="detail-value">
                <span class="status-badge status-<?= $project['status'] ?>">
                    <?= ucfirst($project['status']) ?>
                </span>
            </div>
        </div>

        <div class="detail-row">
            <span class="detail-label">📅 Data de Criação</span>
            <div class="detail-value">
                <?= date('d/m/Y H:i', strtotime($project['created_at'])) ?>
            </div>
        </div>

        <div class="detail-row">
            <span class="detail-label">🆔 ID do Projeto</span>
            <div class="detail-value">
                #<?= $project['id'] ?>
            </div>
        </div>

        <div class="actions">
            <a href="edit.php?id=<?= $project['id'] ?>" class="btn btn-warning">
                ✏️ Editar Projeto
            </a>
            <a href="delete.php?id=<?= $project['id'] ?>" class="btn btn-danger"
               onclick="return confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita!')">
                🗑️ Excluir Projeto
            </a>
            <a href="index.php" class="btn btn-secondary">
                ← Voltar à Lista
            </a>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-error">
        <strong>Erro:</strong>
        <?php if ($result['status'] === 404): ?>
            Projeto não encontrado.
        <?php else: ?>
            <?= isset($result['data']['error']) ? htmlspecialchars($result['data']['error']) : 'Erro ao carregar o projeto' ?>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="index.php" class="btn btn-secondary">← Voltar à Lista</a>
    </div>
<?php endif; ?>

        </div>
    </div>
</body>
</html>