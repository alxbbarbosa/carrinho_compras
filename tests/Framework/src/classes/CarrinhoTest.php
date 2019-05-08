<?php
namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

require __DIR__ . '/../../../../vendor/autoload.php';

use App\Models\Produto as Produto;
use App\Models\Carrinho as CarrinhoModel;
use App\Models\ItemCarrinho as ItemCarrinho;
use \PHPUnit\Framework\TestCase as PHPUnit;
use \Abbarbosa\Gadgets\CarrinhoCompras\Classes\Connection as Connection;
use \Abbarbosa\Gadgets\CarrinhoCompras\Classes\Carrinho as Carrinho;
use \Abbarbosa\Gadgets\CarrinhoCompras\Classes\CarrinhoDatabase as CarrinhoDatabase;

class CarrinhoTest extends PHPUnit
{

    /**
     * @TODO: Teste
     * Teste de operações do carrinho: adionando 3 produtos
     * Definindo valor do Frete, Parcelamento e Juros
     * Salvado no banco de dados
     * Recuperando um carrinho salvo no banco de dados
     * Removendo itens da lista
     * Excluíndo um registro de carrinho do banco
     * Salvando novamente com menos itens
     * */
    public function testCarrinho()
    {
        /* Configurar Acesso ao Banco */
        Connection::setCredentials('postgres', 'P@ssw0rd');
        Produto::setConnection(Connection::getInstance('carrinho_compras'));

        // Truncate na tabela e ver se o método está fazendo corretamente
        $truncate = Produto::truncate();
        $this->assertEquals(true, $truncate);
        // Limpar tabela de carrinho
        ItemCarrinho::truncate();
        CarrinhoModel::truncate();
        $this->assertCount(0, CarrinhoModel::all());

        /* Configurar cenário */
        $produtos = [
            1 => [
                'sku' => '7896102509410',
                'descricao' => 'MOLHO TOM POUCH QUERO 340G TRADICIONAL',
                'preco' => '1.39'
            ],
            2 => [
                'sku' => '7898080641304',
                'descricao' => 'LEITE LV NILZA 1LT SEMIDESNATADO',
                'preco' => '2.97'
            ],
            3 => [
                'sku' => '7896036095904',
                'descricao' => 'Molho de Tomate POMAROLA Tradicional Lata 340g',
                'preco' => '2.29'
            ],
        ];
        $valor_frete = 56.20;
        $valor_total_sem_frete = (1.39 + 2.97 + 2.29);
        $valor_total_com_frete = $valor_total_sem_frete + $valor_frete;

        $carrinho = new Carrinho;

        /* Inserir os produtos e validar */
        foreach ($produtos as $key => $data) {
            $produto = Produto::create($data);
            $this->assertInstanceOf(Produto::class, $produto);
        }

        $produtos = Produto::all();
        $this->assertCount(3, $produtos);

        foreach ($produtos as $p) {
            $carrinho->adicionar($p, 1);
        }
        // Testar a quantidade de itens
        $this->assertEquals(3, $carrinho->getQuantidadeitens());
        // Testar o valor total a vista dos itens somados no carrinho
        $this->assertEquals($valor_total_sem_frete, $carrinho->getTotal());

        // Testar valor total a vista com frete
        $carrinho->setFrete($valor_frete);
        $this->assertEquals($valor_total_com_frete, $carrinho->getTotal());

        //Testar parcelamento
        $carrinho->setParcelas(2);
        $carrinho->setTaxaDeJuros(2.5);
        $this->assertEquals(65.216574074074, $carrinho->getTotalParcelado(), 'Valor do total parcelado');
        $this->assertEquals(32.608287037037, $carrinho->getValorParcela(), 'Valor das parcelas');
        $this->assertEquals(2, $carrinho->getNumeroParcelas(), 'Valor das parcelas');

        // Testar salvar carrinho no banco
        $carrinho_database = new CarrinhoDatabase;
        $carrinho_database->setModels(new CarrinhoModel, new Produto, new ItemCarrinho);
        $this->assertInstanceOf(CarrinhoDatabase::class, $carrinho_database);
        $carrinho_database->save($carrinho);
        // destruir carrinho
        $carrinho = null;
        $this->assertNull($carrinho);

        // Sobrescrever a referencia do objeto carrinho para novo objeto
        $carrinho = new Carrinho;
        $this->assertEquals(0, $carrinho->getQuantidadeitens());

        // Popular carrinho com três itens
        $carrinho = $carrinho_database->load(1, $carrinho);
        $this->assertEquals(3, $carrinho->getQuantidadeitens());
        $this->assertEquals(65.216574074074, $carrinho->getTotalParcelado(), 'Valor do total parcelado');
        $this->assertEquals(32.608287037037, $carrinho->getValorParcela(), 'Valor das parcelas');
        $this->assertEquals(2, $carrinho->getNumeroParcelas(), 'Valor das parcelas');

        // Retirando produtos da lista de itens do carrinho
        // Verificar se existe um produto
        $prod = Produto::find(2);
        $this->assertInstanceOf(Produto::class, $prod);

        // Verifica se existe o produto no carrinho na segunda posição
        $posicao = $carrinho->existeNoCarrinho($prod);
        $this->assertEquals(2, $posicao);

        // Remover o produto e verificar se ficou dois itens
        $carrinho->remover($posicao);
        $this->assertEquals(2, $carrinho->getQuantidadeitens());
        $this->assertEquals(($valor_total_com_frete - 2.97), $carrinho->getTotal());
        $this->assertEquals(62.134740740741, $carrinho->getTotalParcelado());
        //$this->assertEquals(50, $carrinho->getTotalParcelado()); // Causar erros

        //Excluir do banco de dados o carrinho
        $carrinho_database->delete($carrinho->getId());
        $count = CarrinhoModel::find($carrinho->getId());
        $this->assertNull($count);
        $this->assertCount(0, ItemCarrinho::all(['carrinho_id', $carrinho->getId()]));

        // Salvar com novas configurações
        $carrinho = $carrinho_database->save($carrinho);
        $this->assertEquals(2, $carrinho->getQuantidadeitens());
        $this->assertCount(2, ItemCarrinho::all(['carrinho_id', $carrinho->getId()]));

        // Limpar tabela de carrinho
        CarrinhoModel::truncate();
        $this->assertCount(0, CarrinhoModel::all());
    }
}
