<?php
require_once 'config.php';

$api = new ApiClient();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name']),
        'description' => trim($_POST['description']),
        'status' => $_POST['status']
    ];

    if (empty($data['name'])) {
        $error = 'Nome do projeto é obrigatório';
    } else {
        $result = $api->createProject($data);

        if ($result['status'] === 200 || $result['status'] === 201) {
            header('Location: index.php?message=Projeto criado com sucesso!&type=success');
            exit;
        } else {
            $error = 'Erro ao criar projeto: ' . ($result['data']['detail'] ?? 'Erro desconhecido');
        }
    }
}

$title = 'Criar Novo Projeto';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        /* [Same CSS styles as index.php] */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: rgba(255, 255, 255, 0.95); border-radius: 15px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .header { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .nav { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
        .nav a { color: white; text-decoration: none; padding: 10px 20px; background: rgba(255, 255, 255, 0.2); border-radius: 25px; transition: all 0.3s ease; }
        .nav a:hover { background: rgba(255, 255, 255, 0.3); transform: translateY(-2px); }
        .content { padding: 40px; }
        .btn { background: linear-gradient(45deg, #667eea, #764ba2); color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.3s ease; font-size: 14px; font-weight: 600; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .btn-success { background: linear-gradient(45deg, #51cf66, #40c057); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        .form-control { width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 16px; transition: border-color 0.3s ease; }
        .form-control:focus { outline: none; border-color: #667eea; }
        textarea.form-control { resize: vertical; min-height: 120px; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Gerenciador de Projetos</h1>
            <nav class="nav">
                <a href="index.php">Todos os Projetos</a>
                <a href="create.php">Novo Projeto</a>
            </nav>
        </div>
        <div class="content">

<h2 style="color: #333; margin-bottom: 30px; font-size: 2em;">Criar Novo Projeto</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" style="max-width: 600px;">
    <div class="form-group">
        <label for="name">Nome do Projeto *</label>
        <input type="text" id="name" name="name" class="form-control"
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Descrição</label>
        <textarea id="description" name="description" class="form-control"
                  placeholder="Descreva os objetivos e detalhes do projeto..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status" class="form-control">
            <option value="ativo" <?= ($_POST['status'] ?? 'ativo') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
            <option value="pausado" <?= ($_POST['status'] ?? '') === 'pausado' ? 'selected' : '' ?>>Pausado</option>
            <option value="finalizado" <?= ($_POST['status'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
        </select>
    </div>

    <div style="display: flex; gap: 15px; margin-top: 30px;">
        <button type="submit" class="btn btn-success">Criar Projeto</button>
        <a href="index.php" class="btn">Cancelar</a>
    </div>
</form>

        </div>
    </div>
</body>
</html>