<?php

namespace App\Providers;

use App\Override\Connection;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Contracts\Queue\EntityResolver;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\QueueEntityResolver;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;
use Illuminate\Database\Query\Grammars\MySqlGrammar as QueryGrammar;

class CacheableDatabaseServiceProvider extends \Illuminate\Database\DatabaseServiceProvider
{
    /**
     * Register the primary database bindings.
     *
     * @return void
     */
    protected function registerConnectionServices()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app->singleton('db', function ($app) {
            //Load the default DatabaseManager
            $dbm = new DatabaseManager($app, $app['db.factory']);
            //Extend to include the custom connection (MySql in this example)
            $dbm->extend('mysql', function ($config, $name) use ($app) {
                //Create default connection from factory
                $connection = $app['db.factory']->make($config, $name);
                //Instantiate our connection with the default connection data
                $new_connection = new Connection(
                    $connection->getPdo(),
                    $connection->getDatabaseName(),
                    $connection->getTablePrefix(),
                    $config
                );
                //Set the appropriate grammar object
                $new_connection->setQueryGrammar(new QueryGrammar());
                $new_connection->setSchemaGrammar(new SchemaGrammar());
                return $new_connection;
            });
            return $dbm;
        });

        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });
    }
}
