<?php
namespace App\Models;

require __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase as PHPUnit;
use \Abbarbosa\Gadgets\CarrinhoCompras\Classes\Connection as Connection;
use App\Models\Produto as Produto;

class ProdutosTest extends PHPUnit
{

    /**
     * Testar os métodos da Model Produtos:
     * -create()   : Testando criação de elementos utilizando um array associativo
     * -save()     : Para novos registros
     * -save()     : Para atualização de registros
     * -find()     : Encontrar um registro
     * -all()      : Encontrar todos os registros ou nada
     * -delete()   : Se o resgistro está sendo excluído corretamente
     * -truncate() : Se está truncando os dados corretamente
     * */
    public function testCriarProdutos()
    {
        /* Cenário */
        $quantidade_produtos = 3;

        $detalhes = [
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
        // Configurar Acesso ao Banco
        Connection::setCredentials('postgres', 'P@ssw0rd');
        Produto::setConnection(Connection::getInstance('carrinho_compras'));


        // Truncate na tabela e ver se o método está fazendo corretamente
        $truncate = Produto::truncate();
        $this->assertEquals(true, $truncate);
        // Fazer um teste para ver se a quantiadade de retorno é zero
        $vazio = Produto::all();
        $this->assertCount(0, $vazio);


        // Criar produtos e validar se são criados corretamente
        for ($i = 1; $i <= $quantidade_produtos; $i++) {
            /* Aplicação */
            $produto = new Produto;
            $produto->descricao = $detalhes[$i]['descricao'];
            $produto->preco = $detalhes[$i]['preco'];
            $produto->sku = $detalhes[$i]['sku'];
            $produto->detalhes = "Este é o produto {$i} que foi salvo no banco de dados, o Model está perfeito!";
            $produto = $produto->save();


            /* Validação */
            $this->assertEquals($detalhes[$i]['descricao'], $produto->descricao);
            $this->assertEquals($detalhes[$i]['preco'], $produto->preco);
            $this->assertEquals($detalhes[$i]['sku'], $produto->sku);
        }
        // Verifica se está encontrando corretamente a quantidade de registros
        $produtos = Produto::all();
        $this->assertCount($quantidade_produtos, $produtos);

        for ($i = 1; $i <= $quantidade_produtos; $i++) {
            /* Aplicação */
            $produto = Produto::find($i);
            // Verifica se foi gerado corratamente uma instância de produto
            $this->assertInstanceOf(Produto::class, $produto);
            /* Validação: se dados são preenchidos corretamente */
            $this->assertEquals($detalhes[$i]['descricao'], $produto->descricao);
            $this->assertEquals($detalhes[$i]['preco'], $produto->preco);
            $this->assertEquals($detalhes[$i]['sku'], $produto->sku);

            // Verificar se o update foi feito corretamente
            $produto->descricao = 'Descrição foi alterada';
            $produto->save();
            $this->assertEquals('Descrição foi alterada', $produto->descricao);

            // Verificar se registros são excluidos com êxito
            $produto->delete();
            $produto = Produto::find($i);
            $this->assertNull($produto);
        }

        foreach ($detalhes as $key => $data) {
            $produto = Produto::create($data);
            $this->assertInstanceOf(Produto::class, $produto);
        }
    }
}
