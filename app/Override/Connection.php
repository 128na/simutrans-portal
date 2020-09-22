<?php
namespace App\Override;

class Connection extends \Illuminate\Database\MySqlConnection
{
    public function query()
    {
        return new QueryBuilder(
            $this,
            $this->getQueryGrammar(),
            $this->getPostProcessor()
        );
    }
}
