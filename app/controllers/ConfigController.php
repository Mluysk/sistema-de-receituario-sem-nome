<?php
class ConfigController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(): void
    {
        $unidades = $this->pdo->query('SELECT * FROM unidades_padrao ORDER BY nome_unidade')->fetchAll();
        $perfis = $this->pdo->query('SELECT * FROM custos_perfis ORDER BY nome')->fetchAll();
        $perfilAtualId = $_SESSION['perfil_custos_id'] ?? ($perfis[0]['id'] ?? null);
        $perfilAtual = null;
        foreach ($perfis as $perfil) {
            if ((int) $perfil['id'] === (int) $perfilAtualId) {
                $perfilAtual = $perfil;
                break;
            }
        }
        $percentConfig = $this->getProfilePercents($perfilAtualId);
        View::render('config/index', [
            'unidades' => $unidades,
            'perfis' => $perfis,
            'perfilAtual' => $perfilAtual,
            'perfilAtualId' => $perfilAtualId,
            'percentConfig' => $percentConfig,
        ]);
    }

    public function store(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $nome = trim($_POST['nome_unidade'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        if ($nome === '') {
            $_SESSION['flash_error'] = 'Informe o nome da unidade.';
            redirect('?page=configuracoes');
        }
        $stmt = $this->pdo->prepare('INSERT INTO unidades_padrao (nome_unidade, descricao, data_cadastro) VALUES (?, ?, NOW())');
        $stmt->execute([$nome, $descricao]);
        $_SESSION['flash_success'] = 'Unidade cadastrada.';
        redirect('?page=configuracoes');
    }

    public function update(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome_unidade'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        if ($id <= 0 || $nome === '') {
            $_SESSION['flash_error'] = 'Dados inválidos.';
            redirect('?page=configuracoes');
        }
        $stmt = $this->pdo->prepare('UPDATE unidades_padrao SET nome_unidade = ?, descricao = ? WHERE id = ?');
        $stmt->execute([$nome, $descricao, $id]);
        $_SESSION['flash_success'] = 'Unidade atualizada.';
        redirect('?page=configuracoes');
    }

    public function destroy(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('?page=configuracoes');
        }
        $stmt = $this->pdo->prepare('DELETE FROM unidades_padrao WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Unidade removida.';
        redirect('?page=configuracoes');
    }

    public function setProfile(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $perfilId = (int) ($_POST['perfil_id'] ?? 0);
        $_SESSION['perfil_custos_id'] = $perfilId > 0 ? $perfilId : null;
        error_log('perfil_custos_setado=' . $perfilId);
        header('Content-Type: application/json');
        echo json_encode([
            'perfil_id' => $perfilId,
            'percentConfig' => $this->getProfilePercents($perfilId),
        ]);
    }

    public function profile(): void
    {
        $perfilId = (int) ($_GET['perfil_id'] ?? 0);
        error_log('perfil_custos_lido_config=' . $perfilId);
        header('Content-Type: application/json');
        echo json_encode($this->getProfilePercents($perfilId));
    }

    private function getProfilePercents(?int $perfilId): array
    {
        $defaults = [
            'agua_luz' => 0,
            'imposto' => 0,
            'sobre_valor' => 0,
            'sobre_custo_bruto' => 0,
            'taxa_cartao' => 0,
            'lucro' => 0,
            'total' => 0,
        ];
        if (!$perfilId) {
            return $defaults;
        }

        $stmt = $this->pdo->prepare('SELECT etiqueta, valor_percentual FROM custos_itens WHERE perfil_id = ? AND tipo = "percentual"');
        $stmt->execute([$perfilId]);
        $rows = $stmt->fetchAll();
        error_log('perfil_custos_lido_percentuais=' . $perfilId . ' dados=' . json_encode($rows));

        $map = [
            'agua e luz' => 'agua_luz',
            'imposto' => 'imposto',
            'sobre o valor' => 'sobre_valor',
            'sobre o custo bruto' => 'sobre_custo_bruto',
            'taxa de cartao' => 'taxa_cartao',
            'taxa de cartão' => 'taxa_cartao',
            'lucro' => 'lucro',
        ];

        foreach ($rows as $row) {
            $label = strtolower(trim($row['etiqueta']));
            if (isset($map[$label])) {
                $defaults[$map[$label]] = (float) $row['valor_percentual'];
            }
        }

        $defaults['total'] = array_sum([
            $defaults['agua_luz'],
            $defaults['imposto'],
            $defaults['sobre_valor'],
            $defaults['sobre_custo_bruto'],
            $defaults['taxa_cartao'],
            $defaults['lucro'],
        ]);

        return $defaults;
    }
}
