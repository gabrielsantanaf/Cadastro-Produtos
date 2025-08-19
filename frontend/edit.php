<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?message=ID do projeto inválido&type=error');
    exit;
}

$projectId = (int)$_GET['id'];
$api = new ApiClient();
$errors = [];
$success = false;

$result = $api->getProject($projectId);

if ($result['status'] !== 200) {
    header('Location: index.php?message=Projeto não encontrado&type=error');
    exit;
}

$project = $result['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if (empty($name)) {
        $errors[] = 'O nome do projeto é obrigatório';
    } elseif (strlen($name) < 1) {
        $errors[] = 'O nome deve ter pelo menos 1 caractere';
    }

    if (empty($description)) {
        $errors[] = 'A descrição é obrigatória';
    } elseif (strlen($description) < 3) {
        $errors[] = 'A descrição deve ter pelo menos 3 caracteres';
    }

    if (!in_array($status, ['ativo', 'pausado', 'finalizado'])) {
        $errors[] = 'Status inválido';
    }

    if (empty($errors)) {
        $projectData = [
            'name' => $name,
            'description' => $description,
            'status' => $status
        ];

        $updateResult = $api->updateProject($projectId, $projectData);

        if ($updateResult['status'] === 200) {
            $success = true;
            header('Location: view.php?id=' . $projectId . '&message=Projeto atualizado com sucesso!&type=success');
            exit;
        } else {
            $errors[] = isset($updateResult['data']['error']) ? $updateResult['data']['error'] : 'Erro ao atualizar projeto';
        }
    }
} else {
    $_POST['name'] = $project['name'];
    $_POST['description'] = $project['description'];
    $_POST['status'] = $project['status'];
}

$title = 'Editar Projeto';
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
            max-width: 600px;
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

        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
        }

        .project-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #ffc107;
        }

        .project-info h3 {
            color: #856404;
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        .project-info p {
            color: #666;
            margin-bottom: 5px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-control.error {
            border-color: #dc3545;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        select.form-control {
            cursor: pointer;
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
            margin-right: 10px;
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Editar Projeto</h1>
            <nav class="nav">
                <a href="view.php?id=<?= $projectId ?>">← Ver Detalhes</a>
                <a href="index.php">📋 Lista de Projetos</a>
            </nav>
        </div>
        <div class="content">

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Erro(s) encontrado(s):</strong>
        <ul style="margin: 10px 0 0 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <strong>Sucesso!</strong> Projeto atualizado com sucesso!
    </div>
<?php endif; ?>

            <div class="project-info">
                <h3>📋 Informações Atuais do Projeto</h3>
                <p><strong>ID:</strong> #<?= $project['id'] ?></p>
                <p><strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($project['created_at'])) ?></p>
            </div>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Nome do Projeto <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control <?= in_array('O nome do projeto é obrigatório', $errors) || in_array('O nome deve ter pelo menos 1 caractere', $errors) ? 'error' : '' ?>"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            placeholder="Digite o nome do projeto..."
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">
                            Descrição <span class="required">*</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control <?= in_array('A descrição é obrigatória', $errors) || in_array('A descrição deve ter pelo menos 3 caracteres', $errors) ? 'error' : '' ?>"
                            placeholder="Descreva os detalhes do projeto..."
                            required
                        ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="ativo" <?= ($_POST['status'] ?? '') === 'ativo' ? 'selected' : '' ?>>
                                🟢 Ativo
                            </option>
                            <option value="pausado" <?= ($_POST['status'] ?? '') === 'pausado' ? 'selected' : '' ?>>
                                🟡 Pausado
                            </option>
                            <option value="finalizado" <?= ($_POST['status'] ?? '') === 'finalizado' ? 'selected' : '' ?>>
                                🔴 Finalizado
                            </option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning">
                            💾 Salvar Alterações
                        </button>
                        <a href="view.php?id=<?= $projectId ?>" class="btn btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>