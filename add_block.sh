#!/bin/bash

# Prompt for block name
read -p "Enter block name: " block_name

# Convert to lowercase and replace spaces with underscores (for file naming)
block_slug=$(echo "$block_name" | tr '[:upper:]' '[:lower:]' | tr ' ' '_')

# Convert block name to kebab-case (for ACF location)
block_kebab=$(echo "$block_name" | tr '[:upper:]' '[:lower:]' | tr ' ' '-')

# Define file paths
php_file="./page-templates/blocks/${block_slug}.php"
scss_file="./src/sass/theme/blocks/_${block_slug}.scss"
blocks_scss="./src/sass/theme/blocks/_blocks.scss"
blocks_php="./inc/lc-blocks.php"
acf_json_file="./acf-json/${block_slug}.json"

# Create PHP and SCSS files
touch "$php_file"
touch "$scss_file"

echo "Created: $php_file"
echo "Created: $scss_file"

# Add import statement to _blocks.scss if not already present
if ! grep -q "@import '$block_slug';" "$blocks_scss"; then
    echo "@import '$block_slug';" >> "$blocks_scss"
    echo "Added @import to $blocks_scss"
else
    echo "Import already exists in $blocks_scss"
fi

# Define the block registration code
block_code="\n        acf_register_block_type(array(\n            'name'                => '$block_slug', \n            'title'               => __('$block_name'), \n            'category'            => 'layout',\n            'icon'                => 'cover-image', \n            'render_template'    => 'page-templates/blocks/$block_slug.php', \n            'mode'                => 'edit',\n            'supports'            => array('mode' => false),\n        ));\n"

# Insert block registration code into lc-blocks.php
if grep -q "function acf_blocks()" "$blocks_php"; then
    sed -i "/if (function_exists('acf_register_block_type')) {/a\\$block_code" "$blocks_php"
    echo "Added block registration to $blocks_php"
else
    echo "acf_blocks() function not found in $blocks_php. Please check the file."
fi

# Create ACF JSON skeleton with escaped forward slash and kebab-case block name
acf_json_content="{
    \"key\": \"group_${block_slug}\",
    \"title\": \"$block_name\",
    \"fields\": [
        {
            \"key\": \"field_${block_slug}_name\",
            \"label\": \"${block_name}\",
            \"name\": \"\",
            \"type\": \"message\"
        }
    ],
    \"location\": [
        [
            {
                \"param\": \"block\",
                \"operator\": \"==\",
                \"value\": \"acf\\/$block_kebab\"
            }
        ]
    ],
    \"active\": 1,
    \"description\": \"Field group for $block_name block.\"
}"

echo "$acf_json_content" > "$acf_json_file"
echo "Created ACF field group JSON: $acf_json_file"
