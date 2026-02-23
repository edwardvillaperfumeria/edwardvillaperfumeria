<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CancelarPedidosPendientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidos:cancelar-pendientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela automáticamente los pedidos que han estado en estado pendiente por más de 24 horas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limite = now()->subHours(24);

        $pedidosActualizados = \App\Models\Order::where('status', 'pending')
            ->where('created_at', '<=', $limite)
            ->update(['status' => 'cancelled']);

        $this->info("Se han cancelado {$pedidosActualizados} pedidos pendientes antiguos.");

        return Command::SUCCESS;
    }
}
