<?php
class Database
{
    public function connect()
    {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=test_transit_ecf',
            'root',
            ''
        );
        return $pdo;
    }
}
