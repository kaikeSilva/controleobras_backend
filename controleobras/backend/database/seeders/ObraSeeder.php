<?php

namespace Database\Seeders;

use App\Models\Obra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ObraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lista de nomes de bairros para diversidade de endereços
        $bairros = [
            'Centro', 'Jardim Paulista', 'Moema', 'Itaim Bibi', 'Vila Mariana', 
            'Pinheiros', 'Perdizes', 'Morumbi', 'Brooklin', 'Vila Madalena',
            'Tatuapé', 'Santana', 'Ipiranga', 'Lapa', 'Butantã',
            'Bela Vista', 'Consolação', 'Liberdade', 'Saúde', 'Campo Belo'
        ];

        // Lista de tipos de obras para diversidade nos nomes
        $tiposObra = [
            'Residencial', 'Comercial', 'Industrial', 'Misto', 'Corporativo',
            'Condomínio', 'Edifício', 'Galpão', 'Centro Empresarial', 'Shopping'
        ];

        // Lista de nomes para diversidade
        $nomes = [
            'Aurora', 'Horizonte', 'Vista Verde', 'Alameda', 'Parque',
            'Jardins', 'Bosque', 'Colina', 'Reserva', 'Espaço',
            'Plaza', 'Square', 'Ville', 'Tower', 'Garden',
            'Residence', 'Premium', 'Exclusive', 'Elite', 'Prime'
        ];

        // Criar 30 obras com dados variados
        for ($i = 1; $i <= 30; $i++) {
            // Gerar data de início aleatória nos últimos 3 anos
            $dataInicio = Carbon::now()->subDays(rand(1, 1095));
            
            // Gerar valores aleatórios para área, valor e taxa
            $areaM2 = rand(80, 5000);
            $valorEstimado = $areaM2 * rand(2000, 8000);
            $taxaAdministracao = rand(8, 15);
            
            // Gerar prazo estimado entre 3 e 36 meses
            $prazoEstimado = rand(3, 36);
            
            // Combinar elementos para criar nomes e endereços variados
            $tipoObra = $tiposObra[array_rand($tiposObra)];
            $nome = $tipoObra . ' ' . $nomes[array_rand($nomes)] . ' ' . rand(1, 999);
            $bairro = $bairros[array_rand($bairros)];
            $endereco = 'Rua ' . chr(rand(65, 90)) . '. ' . rand(100, 9999) . ', ' . $bairro . ', São Paulo';
            
            // Criar a obra com os dados gerados
            Obra::create([
                'nome' => $nome,
                'descricao' => "Projeto de construção $tipoObra localizado no bairro $bairro com área de $areaM2 m². Prazo estimado de $prazoEstimado meses.",
                'endereco' => $endereco,
                'data_inicio' => $dataInicio->format('Y-m-d'),
                'prazo_estimado' => $prazoEstimado,
                'area_m2' => $areaM2,
                'valor_estimado' => $valorEstimado,
                'taxa_administracao' => $taxaAdministracao,
            ]);
        }
    }
}
