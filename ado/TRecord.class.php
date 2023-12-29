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
}
?>
