<?php

/**
 * Classe TRepository
 * Esta classe provê os métodos necessários para manipular coleções de objtos.
 */
final class TRepository
{
    private $class; // nome da classe manipulada pelo repositório

    /**
     * Método __construct()
     * INstancia um Repositório de objetos
     * @Param $class = Classe dos Objetos
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Método load()
     * Recuperar um conjunto de objetos (collection) da base de dados
     * através de um critério de seleção, e instanciá-los em memória
     * @param $criteria = objeto do tipo TCriteria
     */
    public function load(TCriteria $criteria)
    {
        // instancia a instrução SELECT
        $sql = new TSqlSelect;
        $sql->addColumn('*');
        $sql->setEntity(constant($this->class . '::TABLENAME'));
        // atribui o critério passado como parâmetro
        $sql->setCriteria($criteria);

        // obtém transação ativa
        if ($conn = TTransaction::get()) {
            // registra mensagem de log
            TTransaction::log($sql->getInstruction());

            //executa a consulta no banco de dados
            $result = $conn->Query($sql->getInstruction());
            $results = array();

            if ($result) {
                // percorre os resultados da consulta, retornando um objeto
                while ($row = $result->getchObject($this->class)) {
                    // armazena no array $results
                    $results[] = $row;
                }
            }
            return $results;
        } else {
            // se não tiver transação, retorna uma exceção
            throw new Exception('Não há transação ativa!!');
        }
    }

    /**
     * Método delete()
     * Excluir um conjunto de objetos (collection) da base de dados
     * através de um critério de seleção.
     * @param $criteria = objeto do tipo TCriteria
     */
    public function delete(TCriteria $criteria)
    {
        // instancia instrução de DELETE
        $sql = new TSqlDelete;
        $sql->getEntity(constant($this->class . '::TABLENAME'));
        // atribui o critério passado como parâmetro
        $sql->setCriteria($criteria);

        // obtém transação ativa
        if ($conn = TTransaction::get()) {
            // registra mensagem de log
            TTransaction::log($sql->getInstruction());
            // executa instrução de DELETE
            $result = $conn->exec($sql->getInstruction());
            return $result;
        } else {
            // se não tiver transação, retorna uma exceção
            throw new Exception('Não há transação ativa!!');
        }
    }

    /**
     * Método count()
     * Retorna a quantidade de objetos da base de dados
     * que satisfazem um determinado critério de seleção
     * @param $criteria = objeto de tipo TCriteria
     */
    public function count(TCriteria $criteria)
    {
        // instancia instrução SELECT
        $sql = new TSqlSelect;
        $sql->addColumn('count(*)');
        $sql->setEntity(constant($this->class . '::TABLENAME'));
        // atribui o critério passado como parâmetro
        $sql->setCriteria($criteria);

        // obtém transação ativa
        if ($conn = TTransaction::get()) {
            // registra mensagem de log
            TTransaction::log($sql->getInstruction());
            // executa instrução de SELECT
            $result = $conn->Query($sql->getInstruction());
            if ($result) {
                $row = $result->fetch();
            }
            // retorna o resultado
            return $row[0];
        } else {
            // se não tiver transação, retorna uma exceção
            throw new Exception('Não há transação ativa!!');
        }
    }
}
?>
