<?php
/**
 * Classe TTransaction
 * Esta classe provê os métodos necessários para manipular transações
 */
final class TTransaction
{
    private static $conn; // conexão ativa
    private static $logger; // objeto de LOG

    /**
     * Método __construct()
     * Está declarado como private para impedir que se crie instâncias de TTransaction
     */
    private function __construct() { }

    /**
     * Método open()
     * Abre uma transação em uma conexão ao BD
     * @param $database = nome do banco de dados
     */
    public static function open($database)
    {
        // abre uma conexào e armazena na propriedade estática $conn
        if (empty(self::$conn)) {
            self::$conn = TConnection::open($database);
            // inicia a transação
            self::$conn->beginTransaction();
            // desliga o log de SQL
            self::$logger = NULL;
        }
    }

    /**
     * Método get()
     * Retorna a conexão ativa da transação
     */
    public static function get()
    {
        // retorna a conexão ativa
        return self::$conn;
    }

    /**
     * Método rollback()
     * Desfaz todas as operações realizadas na transação
     */
    public static function rollback()
    {
        if (self::$conn) {
            // desfaz as operações realizadas durante a transação
            self::$conn->rollback();
            self::$conn = NULL;
        }
    }

    /**
     * Método close()
     * Aplica todas operações realizadas e fecha a transação
     */
    public static function close()
    {
        if (self::$conn) {
            // aplica as operaes realizadas
            // durante a transação
            self::$conn->commit();
            self::$conn = NULL;
        }
    }

    /**
     * Método setLogger()
     * Define qual estratégia (algoritimo de LOG será usado)
     */
    public static function setLogger(TLogger $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Método log()
     * Armazena uma mensagem no arquivo de LOG
     * baseada na estatégia ($logger) atual
     */
    public static function log($message)
    {
        // verifica se existe um logger
        if (self::$logger) {
            self::$logger->write($message);
        }
    }
}
?>
