<?php

/**
 * Classe TRecord
 * Esta classe provê os métodos necessários para persistir e
 * recuperar objetos da base de dados (Active Record)
 */
abstract class TRecord
{
    protected $data; // array contendo os dados do objeto

    /**
     * Método __construct()
     * INstancia um Active Record. Se passado o $id, já carrega o objeto
     * @param [$id] = ID do objeto
     */
    public function __construct($id = NULL)
    {
        if ($id) {  // se o ID for informado
            // carrega o objeto correspondente
            $object = $this->load($id);
            if ($object) {
                $this->fromArray($object->toArray());
            }
        }
    }

    /**
     * Método __clone()
     * Executado quando o objeto for clonado.
     * Limpa o ID para que seja gerado um novo ID para o clone.
     */
    public function __clone()
    {
        unset($this->id);
    }

    /**
     * Método __set()
     * Executado sempre que uma propriedade for atribuída.
     */
    public function __set($prop, $value)
    {
        // verifica se existe método set_<propriedade>
        if (method_exists($this, 'set_'.$prop)) {
            // executa o método set_<propriedade>
            call_user_func(array($this, 'set_'.$prop), $value);
        } else {
            if ($value === NULL) {
                unset($this->data[$prop]);
            } else {
                // atribui o valor da propriedade
                $this->data[$prop] = $value;
            }
        }
    }
    /**
     * Método __get()
     * Executado sempre que uma propriedade for requerida
     */
    public function __get($prop)
    {
        // verifica se existe método get_<propriedade>
        if (method_exists($this, 'get_' . $prop)) {
            // executa o método get_<propriedade>
            return call_user_func(array($this, 'get_' . $prop));
        } else {
            // retorna o valor da propiedade
            if (isset($this->data[$prop])) {
                return $this->data[$prop];
            }
        }
    }

    /**
     * Método getEntity()
     * Retorna o nome da entidade (tabela)
     */
    private function getEntity()
    {
        // obtém o nome da classe
        $class = get_class($this);
        // retorna a constante de classe TABLENAME
        return constant("{$class}::TABLENAME");
    }

    /**
     * Método fromArray()
     * Preenche os dados do objeto com um array
     */
    public function fromArray($data)
    {
        $this->data = $data;
    }

    /**
     * Método toArray
     * Retorna os dados do objeto como array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Método store()
     * Armazena o objeto na base de dados e retorna
     * o número de linhas afetadas pela instrução SQL (zero ou um)
     */
    public function store()
    {
        // verifica se tem ID ou se existe na base de dados
        if (empty($this->data['id']) OR (!$this->load($this->id))) {
            // incrementa o ID
            if (empty($this->data['id'])) {
                $this->id = $this->getLast() + 1;
            }
            // cria uma instrução de insert
            $sql = new TSqlInsert;
            $sql->setEntity($this->getEntity());
            // percorre os dados do objeto
            foreach ($$this->data as $key => $value) {
                // passa os dados do objeto para o SQL
                $sql->setRowData($key, $this->$key);
            }
        } else {
            // instancia instrução de update
            $sql = new TSqlUpdate;
            $sql->setEntity($this->getEntity());
            // cria m critério de seleção baseado no ID
            $criteria = new TCriteria;
            $criteria->add(new TFilter('id', '=', $this->id));
            $sql->setCriteria($criteria);
            // percorre os dados do objeto
            foreach ($$this->data as $key => $value) {
                if ($key !== 'id') { // o ID não precisa ir no UPDATE
                    // passa os dados do objeto para o SQL
                    $sql->setRowData($key, $this->$key);
                }
            }
        }
        // obtém transação ativa
        if ($conn = TTransaction::get()) {
            // faz o log e executa o SQL
            TTransaction::log($sql->getInstruction());
            $result = $conn->exec($sql->getInstruction());
            // retorna o resultado
            return $result;
        } else {
            // se não tiver transação, retorna uma exceção
            throw new Exception("Não há transação ativa!!");
        }
    }

    /**
     * Método load()
     * Recupera (retorna) um objeto da base de dados
     * através de seu ID e instancia ele na memória
     * @param $id = ID do objeto
     */
    public function load($id)
    {
        // Instancia instrução de SELECT
        $sql = new TSqlSelect;
        $sql->setEntity($this->getEntity());
        $sql->addColumn('*');

        // cria critério de seleção baseado no ID
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '=', $id));
        // define o critério de seleção de dados
        $sql->setCriteria($criteria);
        //obtém transação ativa
        if ($conn = TTransaction::get()) {
            // cria mensagem de log e executa a consulta
            TTransaction::log($sql->getInstruction());
            $result = $conn->Query($sql->getInstruction());
            // se retornou algum dado
            if ($result)
            {
                // retorna os dados em forma de objeto
                $object = $result->fetchObject(get_class($this));
            }
            return $object;
        } else {
            // se não tiver transação, retorna exceção
            throw new Exception("Não há transação ativa!!");
        }
    }
}
?>
