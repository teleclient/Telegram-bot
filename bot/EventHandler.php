<?php

class EventHandler extends \danog\MadelineProto\EventHandler
{
    public function __construct($MadelineProto)
    {
        parent::__construct($MadelineProto);
        $this->db = yield new DataBase("/var/www/html/database.json");
        yield $this->db->connect();
        yield $this->db->ping();

        yield $this->updateInfo();
    }
}

?>