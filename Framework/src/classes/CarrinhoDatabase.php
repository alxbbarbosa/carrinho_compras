<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iModel;
use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinho;
use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinhoDatabase;

class CarrinhoDatabase implements iCarrinhoDatabase
{
    protected $model_carrinho;
    protected $model_item;
    protected $model_produto;

    public function setModels(iModel $carrinho, iModel $produto, iModel $itens): iCarrinhoDatabase
    {
        $this->model_produto = $produto;
        $this->model_item = $itens;
        $this->model_carrinho = $carrinho;
        return $this;
    }

    public function setModelItensCarrinho(iModel $model): iCarrinhoDatabase
    {
        $this->model_item = $model;
        return $this;
    }


    public function delete(int $id): bool
    {
        if (isset($this->model_carrinho) && isset($this->model_item)) {
            $itens = $this->model_item->all(['carrinho_id', $id]);
            foreach ($itens as $item) {
                $item->delete();
            }
            $this->model_carrinho->find($id)->delete();
            
            return true;
        }
        return false;
    }

    public function load(int $id, iCarrinho $carrinho): ?iCarrinho
    {
        if (isset($this->model_carrinho) && isset($this->model_item)) {
            $class = get_class($carrinho);
            $found = $this->model_carrinho->find($id);
            if ($found) {
                $carrinho = new $class($found->id, $found->cliente_id, $found->data_compra);

                $carrinho->setTaxaDeJuros($found->juros);
                $carrinho->setParcelas($found->parcelas);
                $carrinho->setFrete($found->frete);
                $carrinho->setDescontoValor($found->desconto_valor);
                $carrinho->setDescontoPorcentagem($found->desconto_porcento);

                foreach ($this->model_item->all(['carrinho_id', $carrinho->getId()]) as $key => $item) {
                    $produto = $this->model_produto->find($item->produto_id);
                    if ($produto) {
                        $carrinho->adicionar($produto, $item->quantidade);
                    }
                }
                return $carrinho;
            }
        }
        return null;
    }

    public function save(iCarrinho $carrinho): ?iCarrinho
    {
        if (isset($this->model_carrinho) && isset($this->model_item)) {
            $model = null;
            if (!is_null($carrinho->getId())) {
                $model = $this->model_carrinho->find($carrinho->getId());
            }
            if (is_null($model)) {
                $class = get_class($this->model_carrinho);
                $model = new $class;
            }

            $model->data_compra = $carrinho->getDataCompra();
            $model->cliente_id =  $carrinho->getClienteId();
            $model->juros  =  $carrinho->getTaxaDeJuros();
            $model->parcelas  =  $carrinho->getNumeroParcelas();
            $model->total  =  $carrinho->getTotal();
            $model->total_com_parcelas =  $carrinho->getTotalParcelado();
            $model->valor_parcela =  $carrinho->getValorParcela();
            $model->frete  =  $carrinho->getFrete();
            $model->desconto_valor =  $carrinho->getDescontoValor();
            $model->desconto_porcento =  $carrinho->getDescontoPorcento();

            if (($result = $model->save()) !== null) {
                foreach ($carrinho->getitens() as $key => $item) {
                    $class = get_class($this->model_item);
                    $model_item = new $class;
                    $model_item->carrinho_id = $result->id;
                    $model_item->produto_id =  $item->produto->getId();
                    $model_item->quantidade =  $item->quantidade;
                    $model_item->save();
                }
                $carrinho->setId($model->id);
                
                return $carrinho;
            }
        }
        return null;
    }
}
