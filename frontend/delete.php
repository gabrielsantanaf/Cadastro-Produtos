<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?message=ID do projeto inválido&type=error');
    exit;
}

$projectId = (int)$_GET['id'];
$api = new ApiClient();
$errors = [];

$result = $api->getProject($projectId);

if ($result['status'] !== 200) {
    header('Location: index.php?message=Projeto não encontrado&type=error');
    exit;
}

$project = $result['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        $deleteResult = $api->deleteProject($projectId);

        if ($deleteResult['status'] === 200) {
            header('Location: index.php?message=Projeto excluído com sucesso!&type=success');
            exit;
        } else {
            $errors[] = isset($deleteResult['data']['error']) ? $deleteResult['data']['error'] : 'Erro ao excluir projeto';
        }
    } else {
        header('Location: view.php?id=' . $projectId);
        exit;
    }
}

$title = 'Excluir Projeto';
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
            max-width: 700px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #dc3545, #c82333);
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

        .warning-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #dc3545;
        }

        .warning-icon {
            text-align: center;
            font-size: 4em;
            margin-bottom: 20px;
            color: #dc3545;
        }

        .warning-message {
            text-align: center;
            margin-bottom: 30px;
        }

        .warning-message h2 {
            color: #dc3545;
            font-size: 1.8em;
            margin-bottom: 15px;
        }

        .warning-message p {
            color: #666;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .project-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            border-left: 4px solid #dc3545;
        }

        .project-info h3 {
            color: #721c24;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .info-row {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 120px;
        }

        .info-value {
            color: #333;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
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

        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 600;
            margin-right: 15px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #5a6268);
        }

        .form-actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .danger-text {
            color: #dc3545;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗑️ Excluir Projeto</h1>
            <nav class="nav">
                <a href="view.php?id=<?= $projectId ?>">← Ver Detalhes</a>
                <a href="index.php">📋 Lista de Projetos</a>
            </nav>
        </div>
        <div class="content">

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Erro:</strong>
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

            <div class="warning-container">
                <div class="warning-icon">⚠️</div>

                <div class="warning-message">
                    <h2>Atenção! Ação Irreversível</h2>
                    <p>Você está prestes a excluir permanentemente este projeto.</p>
                    <p class="danger-text">Esta ação não pode ser desfeita!</p>
                </div>

                <div class="project-info">
                    <h3>📋 Dados do Projeto a ser Excluído:</h3>

                    <div class="info-row">
                        <span class="info-label">Nome:</span>
                        <span class="info-value"><?= htmlspecialchars($project['name']) ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Descrição:</span>
                        <span class="info-value"><?= htmlspecialchars($project['description'] ?? 'Sem descrição') ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-<?= $project['status'] ?>">
                                <?= ucfirst($project['status']) ?>
                            </span>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Criado em:</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($project['created_at'])) ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">ID:</span>
                        <span class="info-value">#<?= $project['id'] ?></span>
                    </div>
                </div>

                <form method="POST" action="" onsubmit="return confirmDelete()">
                    <div class="form-actions">
                        <button type="submit" name="confirm_delete" value="yes" class="btn btn-danger">
                            🗑️ Sim, Excluir Permanentemente
                        </button>
                        <a href="view.php?id=<?= $projectId ?>" class="btn btn-secondary">
                            ❌ Cancelar e Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm(
                'ÚLTIMA CONFIRMAÇÃO!\n\n' +
                'Tem ABSOLUTA CERTEZA que deseja excluir o projeto "<?= addslashes($project['name']) ?>"?\n\n' +
                'Esta ação é IRREVERSÍVEL!'
            );
        }
    </script>
</body>
</html>