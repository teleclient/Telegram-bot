<?php namespace AppName\abilities;

class awaitings
{
    public function __construct(object $db) {
        $this->db = &$db;
    }
    public function pullWith(string $function, array $settings)
    {
        $options = yield $this->$function($settings);
        $this->options[$function] = $options;
        return $options;
    }
    private function user(array $settings)
    {
        $user = &$settings['user'];

        try {
            yield $this->db->ping();
            $result = yield $this->db->query("SELECT * FROM users WHERE user = '$user'");
            $row = yield $result->fetch_assoc();
        } catch (\Throwable $th) {
            //throw $th;
        }
        return [
            'givenData' => &$settings,
            'isNew' => @$result->num_rows == 0,
            'hasName' => @$row['phone'] != NULL,
            'name' => @$row['phone'],
            'isOpped' => @$row['extra'] == "opped",
        ];
    }
}

?>