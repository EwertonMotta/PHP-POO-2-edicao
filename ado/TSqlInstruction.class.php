<?php
/**
 * Classe TSqlInstruction
 * Esta classe provê os métodos em comum entre todas as instruções
 * SQL (ISELECT, INSERT, DELETE, UPDATE)
 */
abstract class TSqlInstruction
{
    protected $sql; // armazena a instrução SQL
    protected $criteria; // armazena o objeto critério
    protected $entity;

    /**
     * Método setEntity()
     * Define o nome da entidade (tabela) manipulada pela instrução SQL
     * @param $entity = tabela
     */
    final public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Método getEntity()
     * retorna o nome da entidade (tabela)
     */
    final public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Método setCriteria()
     * Define um critério de seleção dos dados através da composição de um objeto
     * do tipo TCriteria, que oferece uma interface para definição de critérios
     * @param $criteria = objeto do tipo TCriteria
     */
    public function setCriteria(TCriteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Método getInstruction()
     * Declarando-o como <abstract> obrigamos sua declaração nas classes filhas,
     * uma vez que seu comportamento será distinto em cada uma delas, configurando polimorfismo.
     */
    abstract function getInstruction();
}
?>
