<?php

namespace FredericBarry\WordPress\Toolkit\Common;

if (!defined("ABSPATH")) {
    exit();
}

class Register_Taxonomy
{
    /**
     * @var array $args
     */
    private $args;

    /**
     * Object type with which the taxonomy should be associated.
     * @var string $object_type
     */
    private $object_type;

    /**
     * Taxonomy key. Must not exceed 32 characters and may only contain lowercase alphanumeric characters, dashes, and underscores.
     * @var string $taxonomy
     */
    private $taxonomy;

    public function __construct(
        string $taxonomy,
        string $object_type,
        array $args
    ) {
        $this->object_type = $object_type;
        $this->taxonomy = $taxonomy;
        $this->args = $args;
    }

    public function action_init(): void
    {
        $this->register_taxonomy();
    }

    public function init(): void
    {
        add_action("init", [$this, "action_init"]);
    }

    private function register_taxonomy(): void
    {
        register_taxonomy($this->taxonomy, $this->object_type, [
            "labels" => [
                "name" => $this->args["labels"]["name"]
            ],
            "show_admin_column" => true,
            "rewrite" => [
                "slug" => $this->args["rewrite"]["slug"],
                "with_front" => false
            ]
        ]);
    }
}
