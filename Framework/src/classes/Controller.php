<?php
namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

class Controller
{
    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    protected function model()
    {
        return $this->model;
    }
}
