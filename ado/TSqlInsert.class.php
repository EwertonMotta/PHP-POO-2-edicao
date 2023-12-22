<?php
/**
 * Classe TSqlInsert
 * Esta classe provê meios para manipulação de ums instrução de INSERT no banco de dados
 */
final class TSqlInsert extends TSqlInstruction
{
    private $columnValues;

    /**
     * Método setRowData()
     * Atribui valores à determinadas colunas no banco de dados que serão inseridas
     * @param $column = coluna da tabela
     * @param $valur = valor a ser armazenado
     */
    public function setRowData($column, $value)
    {
        // verifica se é um dado escalar (string, inteiro, ...)
        if (is_scalar($value)) {
            if(is_string($value) and (!empty($value))) {
                // adiciona \ em aspas
                $value = addslashes($value);
                // caso seja uma string
                $this->columnValues[$column] = "'$value'";
            }
            else if (is_bool($value)) {
                // caso seja um boolean
                $this->columnValues[$column] = $value ? 'TRUE' : 'FALSE';
            }
            else if ($value !== '') {
                // caso seja outro tipo de dado
                $this->columnValues[$column] = $value;
            }
            else {
                // caso seja NULL
                $this->columnValues[$column] = 'NULL';
            }
        }
    }

    /**
     * Método setCriteria()
     * Não existe no contexto dessa classe, logo, irá lançar um aerro se for executado
     */
    public function setCriteria(TCriteria $criteria)
    {
        // lanca o erro
        throw new Exception("Cannot call setCriteria from " . __CLASS__);
    }

    /**
     * Método getInstruction()
     * retorna a instrução INSERT em forma de string.
     */
    public function getInstruction()
    {
        $this->sql = "INSERT INTO {$this->entity} (";
        //monta uma string contendo os nomes e colunas
        $columns = implode(', ', array_keys($this->columnValues));
        //monta uma string contendo os valores
        $values = implode(', ' , array_values($this->columnValues));
        $this->sql .= $columns . ')';
        $this->sql .= " VALUES ({$values})";
        return $this->sql;
    }
}
?>
