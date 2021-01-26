<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Contratos extends AbstractMigration
{
    
    public function change(): void
    {
        $table = $this->table('contratos');
        $table->addColumn('id')
            ->addColumn('hibrido')
            ->addColumn('idcliente')
            ->addColumn('numcontrato')
            ->addColumn('idsafra')
            ->addColumn('finalizado')
            ->addColumn('ativo')
            ->addColumn('justificativa')
            ->addColumn('created')
            ->addColumn('updated')
            ->create();

    }
}
