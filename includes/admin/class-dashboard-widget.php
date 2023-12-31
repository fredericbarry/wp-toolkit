<?php

namespace FredericBarry\WordPress\Toolkit\Admin;

if (!defined("ABSPATH")) {
    exit();
}

class Dashboard_Widget
{
    /**
     * The widget ID.
     * @var string $widget_id
     */
    private $widget_id;

    /**
     * The widget title.
     * @var string $widget_title
     */
    private $widget_title;

    public function __construct(string $widget_id, string $widget_title)
    {
        $this->widget_id = $widget_id;
        $this->widget_title = $widget_title;
    }

    public function action_wp_dashboard_setup(): void
    {
        if (current_user_can("manage_options")) {
            wp_add_dashboard_widget($this->widget_id, $this->widget_title, [
                $this,
                "wp_add_dashboard_widget_callback"
            ]);
        }
    }

    public function wp_add_dashboard_widget_callback(): void
    {
    }

    public function init(): void
    {
        add_action("wp_dashboard_setup", [$this, "action_wp_dashboard_setup"]);
    }
}
