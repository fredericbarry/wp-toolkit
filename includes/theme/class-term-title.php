<?php

namespace FredericBarry\WordPress\Toolkit\Theme;

if (!defined("ABSPATH")) {
    exit();
}

class Term_Title
{
    /**
     * The single term title.
     * @var string $single_term_title
     */
    private $single_term_title;

    public function __construct(string $single_term_title)
    {
        $this->single_term_title = $single_term_title;
    }

    /**
     * Filters the custom taxonomy archive page title.
     *
     * @param string $term_name Tag name for archive being displayed.
     * @return string
     */
    function filter_single_term_title(string $term_name): string
    {
        $option = $this->single_term_title;
        $term_title = $term_name;

        if (false !== strpos($option, "%term_name%")) {
            $term_title = str_replace("%term_name%", $term_name, $option);
        }

        return $term_title;
    }

    public function init(): void
    {
        add_filter("single_term_title", [$this, "filter_single_term_title"]);
    }
}
