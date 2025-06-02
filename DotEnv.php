<?php
/**
 * Classe simples DotEnv para carregar variáveis de ambiente do arquivo .env
 */
class DotEnv
{
    /**
     * O diretório onde o arquivo .env pode ser localizado.
     *
     * @var string
     */
    protected $path;

    /**
     * Construtor
     *
     * @param string $path Caminho para o arquivo .env
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s não existe', $path));
        }
        $this->path = $path;
    }

    /**
     * Carregar variáveis de ambiente do arquivo .env
     *
     * @return void
     */
    public function load(): void
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('O arquivo %s não pode ser lido', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignorar comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Analisar a linha
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remover aspas se presentes
            if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            }

            // Definir variável de ambiente
            if (!array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
            }
        }
    }
}
