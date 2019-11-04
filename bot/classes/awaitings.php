<?php

class awaitings
{
    public function __construct(Type $var = null) {
        $functions = get_class_methods($this);
        unset($functions[0]);
        unset($functions[1]);
        $this->functions = $functions;

        $this->db = new DataBase();
        $this->db->connect();
        $this->db->ping();
    }
    public function pullWith(array &$settings): array
    {
        $options = [];
        foreach ($this->functions as $function) {
            $options[$function] = call_user_func($function, $settings);
        }
        return $options;
    }
    private function user(array $settings)
    {
        $user = $settings['user'];
        
        $this->db->ping();
        $result = $this->db->query("SELECT * FROM users WHERE user = '$user'");
        @$row = $result->fetch_assoc();
        return [
            'isNew' => $result->num_rows == 0,
            'hasName' => @$row['phone'] == NULL,
            'name' => @$row['phone'],
            'isOpped' => @$row['extra'] == "opped",
        ];
    }
}

?>