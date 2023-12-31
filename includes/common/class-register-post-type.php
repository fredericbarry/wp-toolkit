<?php

namespace FredericBarry\WordPress\Toolkit\Common;

if (!defined("ABSPATH")) {
    exit();
}

class Register_Post_Type
{
    /**
     * @var array $args
     */
    private $args;

    /**
     * Post type key. Must not exceed 20 characters and may only contain lowercase alphanumeric characters, dashes, and underscores.
     *
     * @var string $post_type
     */
    private $post_type;

    public function __construct(string $post_type, array $args)
    {
        $this->post_type = $post_type;
        $this->args = $args;
    }

    public function action_init(): void
    {
        $this->register_post_type();
    }

    /**
     * Filters the bulk action updated messages.
     *
     * @param array $bulk_messages Array of messages, where key is the post type name.
     * @param array $bulk_counts Array containing count of posts involved in the action under respective keys.
     * @return array The filtered array of messages.
     */
    public function filter_bulk_post_updated_messages(
        array $bulk_messages,
        array $bulk_counts
    ): array {
        $bulk_messages[$this->post_type] = [
            "updated" => str_replace(
                "%updated%",
                $bulk_counts["updated"],
                $this->args["bulk_messages"]["updated"]
            ),
            "locked" => str_replace(
                "%locked%",
                $bulk_counts["locked"],
                $this->args["bulk_messages"]["locked"]
            ),
            "deleted" => str_replace(
                "%deleted%",
                $bulk_counts["deleted"],
                $this->args["bulk_messages"]["deleted"]
            ),
            "trashed" => str_replace(
                "%trashed%",
                $bulk_counts["trashed"],
                $this->args["bulk_messages"]["trashed"]
            ),
            "untrashed" => str_replace(
                "%untrashed%",
                $bulk_counts["untrashed"],
                $this->args["bulk_messages"]["untrashed"]
            )
        ];

        return $bulk_messages;
    }

    /**
     * Filters the post updated messages.
     *
     * @param array $messages Post updated messages.
     * @return array The filtered post updated messages.
     */
    public function filter_post_updated_messages(array $messages): array
    {
        $post = get_post();
        $post_type_object = get_post_type_object($this->post_type);

        $permalink = get_permalink($post->ID);

        if (!$permalink) {
            $permalink = "";
        }

        $preview_link_html = $scheduled_link_html = $view_link_html = "";

        $preview_url = get_preview_post_link($post);

        $viewable = is_post_type_viewable($post_type_object);

        if ($viewable) {
            // Preview link
            $preview_link_html = sprintf(
                ' <a href="%1$s">%2$s</a>',
                esc_url($preview_url),
                $this->args["post_updated"]["preview"]
            );

            // Scheduled preview link
            $scheduled_link_html = sprintf(
                ' <a href="%1$s">%2$s</a>',
                esc_url($permalink),
                $this->args["post_updated"]["scheduled_preview"]
            );

            // View link
            $view_link_html = sprintf(
                ' <a href="%1$s">%2$s</a>',
                esc_url($permalink),
                $this->args["post_updated"]["view"]
            );
        }

        /* translators: Publish box date format, see https://secure.php.net/date */
        $scheduled_date = date_i18n(
            $this->args["post_updated"]["date_format"],
            strtotime($post->post_date)
        );

        $revision = filter_input(INPUT_GET, "revision");

        $messages[$this->post_type] = [
            0 => "", // Unused. Messages start at index 1
            1 => $this->args["post_updated"]["messages"]["1"] . $view_link_html,
            2 => $this->args["post_updated"]["messages"]["2"],
            3 => $this->args["post_updated"]["messages"]["3"],
            4 => $this->args["post_updated"]["messages"]["4"],
            /* translators: %s: date and time of the revision */
            5 => isset($revision)
                ? sprintf(
                    $this->args["post_updated"]["messages"]["5"],
                    wp_post_revision_title((int) $revision, false)
                )
                : false,
            6 => $this->args["post_updated"]["messages"]["6"] . $view_link_html,
            7 => $this->args["post_updated"]["messages"]["7"],
            8 =>
                $this->args["post_updated"]["messages"]["8"] .
                $preview_link_html,
            9 =>
                sprintf(
                    $this->args["post_updated"]["messages"]["9"],
                    "<strong>" . $scheduled_date . "</strong>"
                ) . $scheduled_link_html,
            10 =>
                $this->args["post_updated"]["messages"]["10"] .
                $preview_link_html
        ];

        return $messages;
    }

    public function init(): void
    {
        add_action("init", [$this, "action_init"]);

        if (is_admin()) {
            add_filter(
                "bulk_post_updated_messages",
                [$this, "filter_bulk_post_updated_messages"],
                10,
                2
            );
            add_filter("post_updated_messages", [
                $this,
                "filter_post_updated_messages"
            ]);
        }
    }

    private function register_post_type(): void
    {
        register_post_type($this->post_type, [
            "labels" => [
                "name" => $this->args["labels"]["name"],
                "singular_name" => $this->args["labels"]["singular_name"],
                "add_new_item" => $this->args["labels"]["add_new_item"],
                "edit_item" => $this->args["labels"]["edit_item"],
                "new_item" => $this->args["labels"]["new_item"],
                "view_item" => $this->args["labels"]["view_item"],
                "view_items" => $this->args["labels"]["view_items"],
                "search_items" => $this->args["labels"]["search_items"],
                "not_found" => $this->args["labels"]["not_found"],
                "not_found_in_trash" =>
                    $this->args["labels"]["not_found_in_trash"],
                "all_items" => $this->args["labels"]["all_items"],
                "archives" => $this->args["labels"]["archives"]
            ],
            "public" => true,
            "show_in_rest" => true,
            "menu_position" => $this->args["menu_position"],
            "menu_icon" => $this->args["menu_icon"],
            "capabilities" => [
                "create_posts" => "do_not_allow"
            ],
            "map_meta_cap" => true,
            "supports" => ["title", "editor"],
            "taxonomies" => $this->args["taxonomies"],
            "has_archive" => $this->post_type,
            "rewrite" => ["with_front" => false],
            "template" => [
                [
                    "core/paragraph",
                    [
                        "placeholder" =>
                            $this->args["template"]["core/paragraph"][
                                "placeholder"
                            ]
                    ]
                ]
            ]
        ]);
    }
}
